<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check nếu user đã auth bằng guard truong hoặc troly
        if (auth('truong')->check() || auth('troly')->check()) {
            return $next($request);
        }

        // Nếu không auth, redirect về login
        return redirect('/admin/login')->with('error', 'Vui lòng đăng nhập để truy cập');
    }
}
