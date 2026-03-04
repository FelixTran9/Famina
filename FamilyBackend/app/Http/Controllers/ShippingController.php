<?php

namespace App\Http\Controllers;

use App\Models\PhieuGiaoHang;
use App\Models\DonBanHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShippingController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = PhieuGiaoHang::query();
        if ($q) {
            $query->where('MaPhieuGiao', 'like', "%$q%")
                  ->orWhere('MaDon', 'like', "%$q%")
                  ->orWhere('MaVanDon', 'like', "%$q%");
        }
        $phieus = $query->orderBy('NgayGiao', 'desc')->paginate(15)->appends(['q' => $q]);
        return view('admin.shipping.index', compact('phieus', 'q'));
    }

    public function create()
    {
        $dons = DB::table('DON_BAN_HANG')
            ->leftJoin('KHACH_HANG', 'DON_BAN_HANG.MaKH', '=', 'KHACH_HANG.MaKH')
            ->where('DON_BAN_HANG.TrangThai', 'đã_xác_nhận')
            ->select('DON_BAN_HANG.MaDon', 'KHACH_HANG.MaKH', 'KHACH_HANG.TenKH', 
                     'KHACH_HANG.SDT', 'KHACH_HANG.DiaChi', 'DON_BAN_HANG.NgayDat')
            ->get();
        $nhanviens = DB::table('NHAN_VIEN')->select('MaNV', 'TenNV')->get();
        return view('admin.shipping.create', compact('dons', 'nhanviens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'MaDon' => 'required|string|exists:DON_BAN_HANG,MaDon',
            'NgayGiao' => 'nullable|date',
            'TenNguoiNhan' => 'required|string|max:100',
            'SDTNguoiNhan' => 'required|string|max:15',
            'DiaChiGiao' => 'required|string|max:255',
            'MaVanDon' => 'nullable|string|max:50',
            'TenShipper' => 'nullable|string|max:100',
            'NguoiGiao' => 'nullable|string|max:20',
        ]);

        $ma = 'PG'.strtoupper(Str::random(8));
        while (PhieuGiaoHang::where('MaPhieuGiao', $ma)->exists()) {
            $ma = 'PG'.strtoupper(Str::random(8));
        }

        PhieuGiaoHang::create([
            'MaPhieuGiao' => $ma,
            'MaDon' => $data['MaDon'],
            'NgayGiao' => $data['NgayGiao'] ?? now()->toDateString(),
            'TenNguoiNhan' => $data['TenNguoiNhan'],
            'SDTNguoiNhan' => $data['SDTNguoiNhan'],
            'DiaChiGiao' => $data['DiaChiGiao'],
            'MaVanDon' => $data['MaVanDon'] ?? null,
            'TenShipper' => $data['TenShipper'] ?? null,
            'TrangThaiGiao' => 'chưa_giao',
            'NguoiGiao' => $data['NguoiGiao'] ?? null,
        ]);

        return redirect()->route('shipping.index')->with('success', 'Tạo phiếu giao hàng thành công');
    }

    public function edit($id)
    {
        $phieu = PhieuGiaoHang::findOrFail($id);
        $nhanviens = DB::table('NHAN_VIEN')->select('MaNV', 'TenNV')->get();
        return view('admin.shipping.edit', compact('phieu', 'nhanviens'));
    }

    public function update(Request $request, $id)
    {
        $phieu = PhieuGiaoHang::findOrFail($id);
        $data = $request->validate([
            'TrangThaiGiao' => 'required|string|in:chưa_giao,đang_giao,đã_giao,giao_thất_bại',
            'TenShipper' => 'nullable|string|max:100',
            'MaVanDon' => 'nullable|string|max:50',
            'GhiChu' => 'nullable|string',
        ]);

        $phieu->update($data);

        if ($data['TrangThaiGiao'] === 'đã_giao') {
            DonBanHang::where('MaDon', $phieu->MaDon)->update(['TrangThai' => 'đã_giao']);
        }

        return redirect()->route('shipping.index')->with('success', 'Cập nhật phiếu giao hàng thành công');
    }

    public function destroy($id)
    {
        $phieu = PhieuGiaoHang::findOrFail($id);
        $phieu->delete();
        return redirect()->route('shipping.index')->with('success', 'Phiếu giao hàng đã được xóa');
    }
}
