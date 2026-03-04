<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = NhanVien::query();
        if ($q) {
            $query->where('TenNV', 'like', "%$q%")
                  ->orWhere('TaiKhoan', 'like', "%$q%")
                  ->orWhere('SDT', 'like', "%$q%");
        }
        $staff = $query->orderBy('MaNV', 'desc')->paginate(15)->appends(['q' => $q]);
        return view('admin.staff.index', compact('staff', 'q'));
    }

    public function create()
    {
        $trolys = DB::table('TRO_LY_CUA_HANG')->select('MaTL', 'TenTL')->get();
        return view('admin.staff.create', compact('trolys'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenNV' => 'required|string|max:100',
            'TaiKhoan' => 'required|string|max:50|unique:NHAN_VIEN,TaiKhoan',
            'MatKhau' => 'required|string|min:6',
            'SDT' => 'nullable|string|max:15',
            'DiaChi' => 'nullable|string|max:255',
            'MaTL' => 'required|string|max:20',
        ]);

        $ma = 'NV'.strtoupper(Str::random(6));
        while (NhanVien::where('MaNV', $ma)->exists()) {
            $ma = 'NV'.strtoupper(Str::random(6));
        }

        $payload = [
            'MaNV' => $ma,
            'TenNV' => $data['TenNV'],
            'TaiKhoan' => $data['TaiKhoan'],
            'MatKhau' => Hash::make($data['MatKhau']),
            'SDT' => $data['SDT'] ?? null,
            'DiaChi' => $data['DiaChi'] ?? null,
            'MaTL' => $data['MaTL'],
        ];

        if (Schema::hasColumn('NHAN_VIEN', 'TrangThai')) {
            $payload['TrangThai'] = 'active';
        }

        NhanVien::create($payload);

        return redirect()->route('staff.index')->with('success', 'Tạo nhân viên thành công');
    }

    public function edit($id)
    {
        $nv = NhanVien::findOrFail($id);
        $trolys = DB::table('TRO_LY_CUA_HANG')->select('MaTL', 'TenTL')->get();
        return view('admin.staff.edit', compact('nv', 'trolys'));
    }

    public function update(Request $request, $id)
    {
        $nv = NhanVien::findOrFail($id);
        $data = $request->validate([
            'TenNV' => 'required|string|max:100',
            'TaiKhoan' => "required|string|max:50|unique:NHAN_VIEN,TaiKhoan,{$id},MaNV",
            'MatKhau' => 'nullable|string|min:6',
            'SDT' => 'nullable|string|max:15',
            'DiaChi' => 'nullable|string|max:255',
            'MaTL' => 'required|string|max:20',
        ]);

        $nv->TenNV = $data['TenNV'];
        $nv->TaiKhoan = $data['TaiKhoan'];
        $nv->SDT = $data['SDT'] ?? null;
        $nv->DiaChi = $data['DiaChi'] ?? null;
        $nv->MaTL = $data['MaTL'];
        if (!empty($data['MatKhau'])) {
            $nv->MatKhau = Hash::make($data['MatKhau']);
        }
        $nv->save();

        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công');
    }

    public function destroy($id)
    {
        $nv = NhanVien::findOrFail($id);
        if (Schema::hasColumn('NHAN_VIEN', 'TrangThai')) {
            $nv->TrangThai = 'inactive';
            $nv->save();
        } else {
            $nv->delete();
        }

        return redirect()->route('staff.index')->with('success', 'Nhân viên đã được vô hiệu hoá');
    }
}
