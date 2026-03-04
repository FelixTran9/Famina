<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = KhachHang::query();

        if ($q = $request->query('q')) {
            $query->where('TenKH', 'like', "%{$q}%")
                  ->orWhere('MaKH', 'like', "%{$q}%")
                  ->orWhere('Email', 'like', "%{$q}%");
        }

        $customers = $query->get()->map(function($c) {
            return [
                'id' => $c->MaKH,
                'name' => $c->TenKH,
                'phone' => $c->SDT,
                'address' => $c->DiaChi,
                'email' => $c->Email,
                'points' => (int)$c->DiemTichLuy,
                'type' => $c->LoaiKH,
                'total_spent' => (float)$c->TongTieuDung,
                'priority' => (bool)$c->KhuyenMaiUuTien,
            ];
        });

        return response()->json($customers);
    }

    public function show($id)
    {
        $customer = KhachHang::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'id' => $customer->MaKH,
            'name' => $customer->TenKH,
            'phone' => $customer->SDT,
            'address' => $customer->DiaChi,
            'email' => $customer->Email,
            'points' => (int)$customer->DiemTichLuy,
            'type' => $customer->LoaiKH,
            'total_spent' => (float)$customer->TongTieuDung,
            'priority' => (bool)$customer->KhuyenMaiUuTien,
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = KhachHang::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'phone' => 'string|max:20',
            'address' => 'string',
            'email' => 'email',
            'type' => 'string|max:50',
            'total_spent' => 'numeric|min:0',
            'priority' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer->update([
            'TenKH' => $request->name ?? $customer->TenKH,
            'SDT' => $request->phone ?? $customer->SDT,
            'DiaChi' => $request->address ?? $customer->DiaChi,
            'Email' => $request->email ?? $customer->Email,
            'LoaiKH' => $request->type ?? $customer->LoaiKH,
            'TongTieuDung' => $request->total_spent ?? $customer->TongTieuDung,
            'KhuyenMaiUuTien' => $request->priority ?? $customer->KhuyenMaiUuTien,
        ]);

        return response()->json(['message' => 'Updated']);
    }

    public function destroy($id)
    {
        $customer = KhachHang::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['message' => 'Deleted']);
    }
}