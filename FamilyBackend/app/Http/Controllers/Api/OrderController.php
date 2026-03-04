<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\KhachHang;
use App\Models\SanPham;
use App\Models\DonBanHang;

class OrderController extends Controller
{
// Thay thế function store cũ bằng đoạn này:

    public function store(Request $request)
    {
        // 1. Validate dữ liệu từ JS (Thêm validate address)
        $request->validate([
            'email' => 'required|email',
            'cart' => 'required|array|min:1',
            'cart.*.product_code' => 'required|string',
            'cart.*.quantity' => 'required|integer|min:1',
            'address' => 'required|string|min:5', // <-- BẮT BUỘC ĐỊA CHỈ
        ], [
            'address.required' => 'Vui lòng nhập địa chỉ giao hàng.',
            'address.min' => 'Địa chỉ quá ngắn, vui lòng nhập chi tiết hơn.'
        ]);

        // 2. Tìm khách hàng
        $user = $request->attributes->get('khachhang');
        if (!$user) {
            $user = KhachHang::where('Email', $request->email)->first();
        }
        
        if (!$user) {
            return response()->json(['message' => 'Khách hàng không tồn tại (Email chưa đăng ký)'], 404);
        }

        // --- LOGIC MỚI: CẬP NHẬT ĐỊA CHỈ KHÁCH HÀNG ---
        // Vì bảng DON_BAN_HANG Legacy không có cột địa chỉ, ta lưu vào hồ sơ khách
        if ($request->has('address') && $user->DiaChi !== $request->address) {
            $user->DiaChi = $request->address;
            $user->save();
        }

        DB::beginTransaction();
        try {
            // 3. Tạo Mã Đơn Hàng
            $maDon = 'DON' . time() . strtoupper(Str::random(3));
            $tongTien = 0;

            // 4. Tạo Header Đơn Hàng
            DB::table('DON_BAN_HANG')->insert([
                'MaDon' => $maDon,
                'NgayDat' => now(),
                'TongTienHang' => 0,
                'TongThanhToan' => 0,
                'HinhThucTT' => 'COD',
                'TrangThai' => 'ChoXuLy',
                'LoaiDon' => 'BanLe',
                'MaKH' => $user->MaKH,
                'NguoiBan' => 'ONLINE'
            ]);

            // 5. Duyệt qua giỏ hàng và chèn chi tiết
            foreach ($request->cart as $item) {
                $maSP = $item['product_code'];
                $qty = $item['quantity'];

                $product = SanPham::where('MaSP', $maSP)->first();

                if (!$product) {
                    throw new \Exception("Sản phẩm mã {$maSP} không tồn tại hoặc đã bị xóa.");
                }

                if ($product->TonKho < $qty) {
                    throw new \Exception("Sản phẩm {$product->TenSP} chỉ còn {$product->TonKho} món.");
                }

                $donGia = $product->GiaBan;
                $thanhTien = $donGia * $qty;
                $tongTien += $thanhTien;

                DB::table('CT_DON_BAN')->insert([
                    'MaDon' => $maDon,
                    'MaSP' => $product->MaSP,
                    'SoLuong' => $qty,
                    'DonGia' => $donGia,
                    'ThueVAT' => 0,
                    'ChietKhau' => 0,
                    'ThanhTien' => $thanhTien
                ]);

                // Trừ kho
                $product->decrement('TonKho', $qty);
            }

            // 6. Cập nhật lại tổng tiền đơn hàng
            DB::table('DON_BAN_HANG')
                ->where('MaDon', $maDon)
                ->update([
                    'TongTienHang' => $tongTien,
                    'TongThanhToan' => $tongTien
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Đặt hàng thành công! Mã đơn: ' . $maDon,
                'order_id' => $maDon
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi đặt hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myOrders(Request $request)
    {
        $user = $request->attributes->get('khachhang');

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 401);
        }

        // Lấy danh sách đơn hàng của khách hàng
        $orders = DB::table('DON_BAN_HANG')
            ->where('MaKH', $user->MaKH)
            ->orderBy('NgayDat', 'desc')
            ->get();

        // Lấy chi tiết cho mỗi đơn hàng
        foreach ($orders as $order) {
            $order->items = DB::table('CT_DON_BAN')
                ->join('SAN_PHAM', 'CT_DON_BAN.MaSP', '=', 'SAN_PHAM.MaSP')
                ->where('CT_DON_BAN.MaDon', $order->MaDon)
                ->select('CT_DON_BAN.*', 'SAN_PHAM.TenSP as TenSanPham')
                ->get();
        }

        return response()->json([
            'orders' => $orders
        ]);
    }
}