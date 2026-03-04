<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\KhachHang;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validate chặt chẽ: Email và SĐT phải duy nhất trong bảng KHACH_HANG
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:KHACH_HANG,Email',
            'phone' => 'required|numeric|digits_between:9,11|unique:KHACH_HANG,SDT',
            'password' => 'required|min:6'
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'phone.numeric' => 'Số điện thoại không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // 2. Tạo User
        $maKH = 'KH' . time() . Str::random(3);
        $kh = new KhachHang();
        $kh->MaKH = $maKH;
        $kh->TenKH = $request->name;
        $kh->Email = $request->email;
        $kh->SDT = $request->phone; // Lưu SĐT
        $kh->MatKhau = Hash::make($request->password);
        $kh->api_token = Str::random(60);
        $kh->DiemTichLuy = 0;
        $kh->save();

        return response()->json([
            'message' => 'Đăng ký thành công',
            'token' => $kh->api_token,
            'user' => [
                'name' => $kh->TenKH,
                'email' => $kh->Email,
                'phone' => $kh->SDT,
                'address' => $kh->DiaChi
            ]
        ], 201);
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required'
        ]);

        $loginId = $request->login_id;
        $password = $request->password;

        // 1. Backdoor Admin Redirect
        if ($loginId === 'admin' && $password === 'admin') {
            return response()->json([
                'redirect_url' => route('admin.login'), // Sử dụng route helper
                'message' => 'Redirecting to admin panel'
            ]);
        }

        // 2. Multi-Login Field Logic
        $user = null;
        $credentials = [];

        // Nhận diện loại dữ liệu
        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            // Là Email
            $credentials = ['Email' => $loginId, 'password' => $password];
            $user = KhachHang::where('Email', $loginId)->first();
        } elseif (is_numeric($loginId)) {
            // Là SĐT (numeric)
            $credentials = ['SDT' => $loginId, 'password' => $password];
            $user = KhachHang::where('SDT', $loginId)->first();
        } else {
            // Là MaKH hoặc TenDangNhap (nếu có)
            $credentials = ['MaKH' => $loginId, 'password' => $password];
            $user = KhachHang::where('MaKH', $loginId)->first();
        }

        // Thực hiện Auth::attempt tương tự (manual check vì không có guard)
        if (!$user || !Hash::check($password, $user->MatKhau)) {
            return response()->json(['message' => 'Thông tin đăng nhập không đúng'], 401);
        }

        // Tạo token mới
        $user->api_token = Str::random(60);
        $user->save();

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => [
                'MaKH' => $user->MaKH,
                'name' => $user->TenKH,
                'email' => $user->Email,
                'phone' => $user->SDT,
                'address' => $user->DiaChi
            ],
            'token' => $user->api_token
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->attributes->get('khachhang');

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:KHACH_HANG,Email,' . $user->MaKH . ',MaKH',
            'phone' => 'required|numeric|digits_between:9,11|unique:KHACH_HANG,SDT,' . $user->MaKH . ',MaKH',
            'address' => 'nullable|string|max:255'
        ]);

        $user->TenKH = $request->name;
        $user->Email = $request->email;
        $user->SDT = $request->phone;
        $user->DiaChi = $request->address;
        $user->save();

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'user' => [
                'name' => $user->TenKH,
                'email' => $user->Email,
                'phone' => $user->SDT,
                'address' => $user->DiaChi
            ]
        ]);
    }
}
