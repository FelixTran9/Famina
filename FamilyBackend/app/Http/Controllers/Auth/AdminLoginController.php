<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'account' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:truong,troly',
        ]);

        $guard = $validated['role'];
        $table = $guard === 'truong' ? 'CUA_HANG_TRUONG' : 'TRO_LY_CUA_HANG';
        $pk = $guard === 'truong' ? 'MaCHT' : 'MaTL';

        // Tìm user từ database
        $user = DB::table($table)->where('TaiKhoan', $validated['account'])->first();

        if ($user && $user->MatKhau === $validated['password']) {
            // Login thủ công
            Auth::guard($guard)->loginUsingId($user->$pk);
            $request->session()->regenerate();
            // Redirect to the admin dashboard route (named `admin.dashboard`)
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công');
        }

        return back()->withErrors(['account' => 'Tài khoản hoặc mật khẩu không chính xác'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}

