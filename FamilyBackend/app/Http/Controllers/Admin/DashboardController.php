<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Models\SanPham;
use App\Models\DonBanHang;
use App\Models\PhieuGiaoHang;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Counts for dashboard cards. Keep queries simple and safe.
        $staffCount = NhanVien::count();
        $productCount = SanPham::count();
        $orderCount = DonBanHang::count();
        $shippingCount = PhieuGiaoHang::count();

        return view('admin.dashboard', compact('staffCount', 'productCount', 'orderCount', 'shippingCount'));
    }
}
