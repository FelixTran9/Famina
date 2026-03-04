<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\KhachHang;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (!$header) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            $token = $matches[1];
            $user = KhachHang::where('api_token', $token)->first();
            if ($user) {
                // attach user to request
                $request->attributes->set('khachhang', $user);
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
