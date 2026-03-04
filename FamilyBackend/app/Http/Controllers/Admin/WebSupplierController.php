<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhaCungCap;

class WebSupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaCungCap::query();

        if ($q = $request->query('q')) {
            $query->where('TenNCC', 'like', "%{$q}%")
                  ->orWhere('MaNCC', 'like', "%{$q}%")
                  ->orWhere('Email', 'like', "%{$q}%");
        }

        $suppliers = $query->paginate(20);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenNCC' => 'required|string|max:255',
            'MaSoThue' => 'string|max:20',
            'DiaChi' => 'required|string',
            'SDT' => 'required|string|max:15',
            'Email' => 'required|email|max:255|unique:nha_cung_cap,Email',
        ]);

        NhaCungCap::create([
            'MaNCC' => 'NCC' . strtoupper(substr(md5(time()), 0, 6)),
            'TenNCC' => $request->TenNCC,
            'MaSoThue' => $request->MaSoThue,
            'DiaChi' => $request->DiaChi,
            'SDT' => $request->SDT,
            'Email' => $request->Email,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Nhà cung cấp được tạo thành công');
    }

    public function edit($id)
    {
        $supplier = NhaCungCap::find($id);
        if (!$supplier) {
            abort(404);
        }
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = NhaCungCap::find($id);
        if (!$supplier) {
            abort(404);
        }

        $request->validate([
            'TenNCC' => 'required|string|max:255',
            'MaSoThue' => 'string|max:20',
            'DiaChi' => 'required|string',
            'SDT' => 'required|string|max:15',
            'Email' => 'required|email|max:255|unique:nha_cung_cap,Email,' . $id . ',MaNCC',
        ]);

        $supplier->update([
            'TenNCC' => $request->TenNCC,
            'MaSoThue' => $request->MaSoThue,
            'DiaChi' => $request->DiaChi,
            'SDT' => $request->SDT,
            'Email' => $request->Email,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Nhà cung cấp được cập nhật thành công');
    }

    public function destroy($id)
    {
        $supplier = NhaCungCap::find($id);
        if (!$supplier) {
            abort(404);
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Nhà cung cấp được xóa thành công');
    }
}
