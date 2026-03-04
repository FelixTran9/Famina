<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = SanPham::query();
        if ($q) {
            $query->where('TenSP', 'like', "%$q%")
                  ->orWhere('MaSP', 'like', "%$q%")
                  ->orWhere('MaDanhMuc', 'like', "%$q%");
        }
        $items = $query->orderBy('MaSP', 'desc')->paginate(20)->appends(['q' => $q]);
        return view('admin.products.index', compact('items', 'q'));
    }

    public function create()
    {
        $categories = DB::table('DANH_MUC_SP')->select('MaDanhMuc','TenDanhMuc')->get();
        $suppliers = DB::table('NHA_CUNG_CAP')->select('MaNCC','TenNCC')->get();
        return view('admin.products.create', compact('categories','suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenSP' => 'required|string|max:150',
            'QuyCach' => 'nullable|string|max:100',
            'DonViTinh' => 'nullable|string|max:50',
            'GiaVon' => 'required|numeric',
            'GiaBan' => 'required|numeric',
            'TonKho' => 'nullable|integer',
            'MaDanhMuc' => 'required|string',
            'MaNCC' => 'required|string',
            'HinhAnh' => 'nullable|file|image|max:2048',
        ]);

        $ma = 'SP'.strtoupper(Str::random(6));
        while (SanPham::where('MaSP', $ma)->exists()) {
            $ma = 'SP'.strtoupper(Str::random(6));
        }

        $payload = $data;
        $payload['MaSP'] = $ma;
        if ($request->hasFile('HinhAnh')) {
            $file = $request->file('HinhAnh');
            $name = $ma . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $name, 'public');
            $payload['HinhAnh'] = 'products/' . $name;
        }

        $payload['TonKho'] = $payload['TonKho'] ?? 0;

        SanPham::create($payload);
        return redirect()->route('products.index')->with('success','Sản phẩm đã được tạo');
    }

    public function edit($id)
    {
        $item = SanPham::findOrFail($id);
        $categories = DB::table('DANH_MUC_SP')->select('MaDanhMuc','TenDanhMuc')->get();
        $suppliers = DB::table('NHA_CUNG_CAP')->select('MaNCC','TenNCC')->get();
        return view('admin.products.edit', compact('item','categories','suppliers'));
    }

    public function update(Request $request, $id)
    {
        $item = SanPham::findOrFail($id);
        $data = $request->validate([
            'TenSP' => 'required|string|max:150',
            'QuyCach' => 'nullable|string|max:100',
            'DonViTinh' => 'nullable|string|max:50',
            'GiaVon' => 'required|numeric',
            'GiaBan' => 'required|numeric',
            'TonKho' => 'nullable|integer',
            'MaDanhMuc' => 'required|string',
            'MaNCC' => 'required|string',
            'HinhAnh' => 'nullable|file|image|max:2048',
        ]);

        if ($request->hasFile('HinhAnh')) {
            $file = $request->file('HinhAnh');
            $name = $item->MaSP . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $name, 'public');
            $data['HinhAnh'] = 'products/' . $name;
        }

        $data['TonKho'] = $data['TonKho'] ?? $item->TonKho;
        $item->update($data);
        return redirect()->route('products.index')->with('success','Sản phẩm đã được cập nhật');
    }

    public function destroy($id)
    {
        $item = SanPham::findOrFail($id);
        $item->delete();
        return redirect()->route('products.index')->with('success','Sản phẩm đã được xóa');
    }
}
