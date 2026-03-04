<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect('/login'); // Hoặc route login của admin
        }

        // 2. Kiểm tra quyền (Dựa trên DB Legacy của bạn: CUA_HANG_TRUONG hoặc NHAN_VIEN role)
        $user = Auth::user();
        
        // Logic ví dụ: Nếu là Cửa hàng trưởng hoặc Role là Admin/Manager
        // Bạn cần điều chỉnh logic này khớp với cột 'role' trong bảng NHAN_VIEN
        if ($user->role !== 'Admin' && $user->role !== 'Manager' && !isset($user->MaCHT)) {
             abort(403, 'Bạn không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}