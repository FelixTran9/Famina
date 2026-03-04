<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang;

class WebCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = KhachHang::query();

        if ($q = $request->query('q')) {
            $query->where('TenKH', 'like', "%{$q}%")
                  ->orWhere('MaKH', 'like', "%{$q}%")
                  ->orWhere('Email', 'like', "%{$q}%");
        }

        $customers = $query->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenKH' => 'required|string|max:255',
            'SDT' => 'string|max:20',
            'DiaChi' => 'string',
            'Email' => 'email',
            'LoaiKH' => 'string|max:50',
            'TongTieuDung' => 'numeric|min:0',
            'KhuyenMaiUuTien' => 'boolean',
        ]);

        KhachHang::create($request->only([
            'TenKH', 'SDT', 'DiaChi', 'Email', 'LoaiKH', 'TongTieuDung', 'KhuyenMaiUuTien'
        ]));

        return redirect()->route('customers.index')->with('success', 'Customer created successfully');
    }

    public function edit($id)
    {
        $customer = KhachHang::find($id);
        if (!$customer) {
            abort(404);
        }
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = KhachHang::find($id);
        if (!$customer) {
            abort(404);
        }

        $request->validate([
            'TenKH' => 'string|max:255',
            'SDT' => 'string|max:20',
            'DiaChi' => 'string',
            'Email' => 'email',
            'LoaiKH' => 'string|max:50',
            'TongTieuDung' => 'numeric|min:0',
            'KhuyenMaiUuTien' => 'boolean',
        ]);

        $customer->update($request->only([
            'TenKH', 'SDT', 'DiaChi', 'Email', 'LoaiKH', 'TongTieuDung', 'KhuyenMaiUuTien'
        ]));

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    public function destroy($id)
    {
        $customer = KhachHang::find($id);
        if (!$customer) {
            abort(404);
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }
}