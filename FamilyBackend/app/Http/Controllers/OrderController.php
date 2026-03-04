<?php

namespace App\Http\Controllers;

use App\Models\DonBanHang;
use App\Models\DonBanHangChiTiet;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Định nghĩa quy trình xử lý đơn hàng chuẩn
    // Key là index, Value là giá trị trong DB
    protected $orderFlow = [
        'chờ_xác_nhận',
        'đã_xác_nhận',
        'đang_giao',
        'đã_giao'
    ];

    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = DonBanHang::query();
        if ($q) {
            $query->where('MaDon', 'like', "%$q%")
                  ->orWhere('MaKH', 'like', "%$q%")
                  ->orWhere('NguoiBan', 'like', "%$q%");
        }
        $orders = $query->orderBy('NgayDat', 'desc')->paginate(15)->appends(['q' => $q]);
        return view('admin.orders.index', compact('orders', 'q'));
    }

    public function create()
    {
        $khachhangs = DB::table('KHACH_HANG')->select('MaKH', 'TenKH')->get();
        $nhanviens = DB::table('NHAN_VIEN')->select('MaNV', 'TenNV')->get();
        $sanphams = DB::table('SAN_PHAM')->select('MaSP', 'TenSP', 'GiaBan', 'TonKho')->get();
        return view('admin.orders.create', compact('khachhangs', 'nhanviens', 'sanphams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'MaKH' => 'required|string',
            'NguoiBan' => 'required|string',
            'HinhThucTT' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.MaSP' => 'required|string|distinct',
            'items.*.SoLuong' => 'required|integer|min:1',
            'items.*.DonGia' => 'required|numeric',
        ]);

        $ma = 'ĐH'.strtoupper(Str::random(8));
        while (DonBanHang::where('MaDon', $ma)->exists()) {
            $ma = 'ĐH'.strtoupper(Str::random(8));
        }

        $tong_tien_hang = 0;
        $tong_thue_vat = 0;
        $tong_chiet_khau = 0;

        DB::beginTransaction();
        try {
            $don = DonBanHang::create([
                'MaDon' => $ma,
                'NgayDat' => now(),
                'MaKH' => $data['MaKH'],
                'NguoiBan' => $data['NguoiBan'],
                'HinhThucTT' => $data['HinhThucTT'] ?? 'Tiền mặt',
                'TrangThai' => 'chờ_xác_nhận', // Luôn bắt đầu từ bước 1
                'TongTienHang' => 0,
                'TongThueVAT' => 0,
                'TongChietKhau' => 0,
                'TongThanhToan' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $sp = SanPham::find($item['MaSP']);
                if (!$sp || $sp->TonKho < $item['SoLuong']) {
                    throw new \Exception('Sản phẩm '.$item['MaSP'].' không đủ tồn kho');
                }

                $thue_vat = ($item['DonGia'] * $item['SoLuong']) * 0.1; // VAT 10% giả định
                $chiet_khau = 0;
                $thanh_tien = ($item['DonGia'] * $item['SoLuong']) + $thue_vat - $chiet_khau;

                DonBanHangChiTiet::create([
                    'MaDon' => $ma,
                    'MaSP' => $item['MaSP'],
                    'SoLuong' => $item['SoLuong'],
                    'DonGia' => $item['DonGia'],
                    'ThueVAT' => $thue_vat,
                    'ChietKhau' => $chiet_khau,
                    'ThanhTien' => $thanh_tien,
                ]);

                $tong_tien_hang += ($item['DonGia'] * $item['SoLuong']);
                $tong_thue_vat += $thue_vat;
                $tong_chiet_khau += $chiet_khau;

                // Trừ kho ngay khi tạo đơn
                $sp->TonKho -= $item['SoLuong'];
                $sp->save();
            }

            $tong_thanh_toan = $tong_tien_hang + $tong_thue_vat - $tong_chiet_khau;
            $don->update([
                'TongTienHang' => $tong_tien_hang,
                'TongThueVAT' => $tong_thue_vat,
                'TongChietKhau' => $tong_chiet_khau,
                'TongThanhToan' => $tong_thanh_toan,
            ]);

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Tạo đơn hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $don = DonBanHang::findOrFail($id);
        $chi_tiet = DonBanHangChiTiet::where('MaDon', $id)->get();
        // Truyền thêm biến orderFlow để View biết được các bước
        return view('admin.orders.detail', compact('don', 'chi_tiet'));
    }

    public function edit($id)
    {
        $don = DonBanHang::findOrFail($id);
        return view('admin.orders.edit', compact('don'));
    }

    public function update(Request $request, $id)
    {
        $don = DonBanHang::findOrFail($id);
        
        $data = $request->validate([
            'TrangThai' => 'required|string|in:chờ_xác_nhận,đã_xác_nhận,đang_giao,đã_giao,đã_hủy',
            'HinhThucTT' => 'nullable|string|max:50',
        ]);

        $currentStatus = $don->TrangThai;
        $newStatus = $data['TrangThai'];

        // --- START LOGIC STATE MACHINE ---
        
        // 1. Nếu là trạng thái HỦY
        if ($newStatus === 'đã_hủy') {
            // Chỉ cho phép hủy nếu đơn chưa giao cho shipper (tức là chưa đến bước 'đang_giao')
            // 'đang_giao' là index 2. Nếu index >= 2 thì không được hủy.
            $currentIndex = array_search($currentStatus, $this->orderFlow);
            if ($currentIndex !== false && $currentIndex >= 2) {
                 return back()->withErrors(['error' => 'Đơn hàng đang giao hoặc đã giao, không thể hủy!']);
            }

            // Nếu chưa hủy thì hoàn kho
            if ($currentStatus !== 'đã_hủy') {
                foreach ($don->chiTiet as $ct) {
                    $sp = SanPham::find($ct->MaSP);
                    if ($sp) {
                        $sp->TonKho += $ct->SoLuong;
                        $sp->save();
                    }
                }
            }
        } 
        // 2. Nếu là chuyển trạng thái TIẾN LÊN
        else {
            $currentIndex = array_search($currentStatus, $this->orderFlow);
            $newIndex = array_search($newStatus, $this->orderFlow);

            // Kiểm tra tính hợp lệ trong quy trình
            if ($currentIndex !== false && $newIndex !== false) {
                // Không cho phép quay lui (newIndex < currentIndex)
                if ($newIndex < $currentIndex) {
                    return back()->withErrors(['error' => 'Không thể quay ngược trạng thái đơn hàng!']);
                }
                // Không cho phép nhảy cóc (chỉ được tăng 1 bước)
                if ($newIndex > $currentIndex + 1) {
                    return back()->withErrors(['error' => 'Vui lòng cập nhật trạng thái theo đúng trình tự!']);
                }
            }
        }
        // --- END LOGIC STATE MACHINE ---

        $don->update($data);
        return redirect()->route('orders.show', $id)->with('success', 'Cập nhật trạng thái thành công: ' . $newStatus);
    }

    public function destroy($id)
    {
        $don = DonBanHang::findOrFail($id);
        
        // Chỉ cho phép xóa đơn hàng nếu nó đã hủy hoặc chưa xử lý xong
        // Tốt nhất là Admin chỉ nên Hủy chứ không nên Xóa hẳn khỏi DB để lưu vết
        // Nhưng nếu xóa thì phải hoàn kho.
        
        $chiTiets = DonBanHangChiTiet::where('MaDon', $id)->get();
        
        foreach ($chiTiets as $ct) {
            $sp = SanPham::find($ct->MaSP);
            if ($sp) {
                // Chỉ hoàn kho nếu đơn hàng chưa bị hủy trước đó (vì nếu hủy rồi thì đã hoàn kho rồi)
                if ($don->TrangThai !== 'đã_hủy') {
                    $sp->TonKho += $ct->SoLuong;
                    $sp->save();
                }
            }
        }
        
        // Xóa chi tiết trước (vì constraint FK)
        DB::table('CT_DON_BAN')->where('MaDon', $id)->delete();
        $don->delete();
        
        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được xóa vĩnh viễn');
    }
}