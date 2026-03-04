<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhaCungCap;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaCungCap::query();

        if ($q = $request->query('q')) {
            $query->where('TenNCC', 'like', "%{$q}%")
                  ->orWhere('MaNCC', 'like', "%{$q}%")
                  ->orWhere('Email', 'like', "%{$q}%");
        }

        $suppliers = $query->get()->map(function($s) {
            return [
                'id' => $s->MaNCC,
                'name' => $s->TenNCC,
                'tax_code' => $s->MaSoThue,
                'address' => $s->DiaChi,
                'phone' => $s->SDT,
                'email' => $s->Email,
            ];
        });

        return response()->json($suppliers);
    }

    public function show($id)
    {
        $supplier = NhaCungCap::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        return response()->json([
            'id' => $supplier->MaNCC,
            'name' => $supplier->TenNCC,
            'tax_code' => $supplier->MaSoThue,
            'address' => $supplier->DiaChi,
            'phone' => $supplier->SDT,
            'email' => $supplier->Email,
        ]);
    }

    public function update(Request $request, $id)
    {
        $supplier = NhaCungCap::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'tax_code' => 'string|max:20',
            'address' => 'string',
            'phone' => 'string|max:15',
            'email' => 'email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier->update([
            'TenNCC' => $request->name ?? $supplier->TenNCC,
            'MaSoThue' => $request->tax_code ?? $supplier->MaSoThue,
            'DiaChi' => $request->address ?? $supplier->DiaChi,
            'SDT' => $request->phone ?? $supplier->SDT,
            'Email' => $request->email ?? $supplier->Email,
        ]);

        return response()->json(['message' => 'Updated']);
    }

    public function destroy($id)
    {
        $supplier = NhaCungCap::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->delete();
        return response()->json(['message' => 'Deleted']);
    }
}