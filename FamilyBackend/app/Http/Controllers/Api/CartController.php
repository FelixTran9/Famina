<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DonBanHang;
use App\Models\DonBanHangChiTiet;
use App\Models\SanPham;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('khachhang');
        $cart = DonBanHang::where('MaKH', $user->MaKH)->where('TrangThai', 'cart')->first();

        if (!$cart) {
            return response()->json([]);
        }

        $result = $cart->chiTiet->map(function($item) {
            $p = SanPham::find($item->MaSP);
            return [
                'id' => $item->MaSP,
                'product_code' => $item->MaSP,
                'name' => $p ? $p->TenSP : null,
                'price' => $p ? (float)$p->GiaBan : 0,
                'quantity' => (int)$item->SoLuong,
                'image' => $p ? $p->HinhAnh : null
            ];
        });

        return response()->json($result);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_code' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->attributes->get('khachhang');
        $maSP = $request->product_code;
        $quantity = $request->quantity;

        Log::info('Adding to cart', ['user' => $user->MaKH, 'product' => $maSP, 'quantity' => $quantity]);

        $product = SanPham::find($maSP);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $cart = DonBanHang::where('MaKH', $user->MaKH)->where('TrangThai', 'cart')->first();
        if (!$cart) {
            $maDon = 'CART_' . $user->MaKH . '_' . time();
            $cart = DonBanHang::create([
                'MaDon' => $maDon,
                'MaKH' => $user->MaKH,
                'TrangThai' => 'cart',
                'TongTienHang' => 0,
                'TongThueVAT' => 0,
                'TongChietKhau' => 0,
                'TongThanhToan' => 0,
                'LoaiDon' => 'Cart'
            ]);
        }

        $existing = $cart->chiTiet()->where('MaSP', $maSP)->first();
        if ($existing) {
            $existing->SoLuong += $quantity;
            $existing->ThanhTien = $existing->SoLuong * $existing->DonGia;
            $existing->save();
        } else {
            $cart->chiTiet()->create([
                'MaSP' => $maSP,
                'SoLuong' => $quantity,
                'DonGia' => $product->GiaBan,
                'ThueVAT' => 0,
                'ChietKhau' => 0,
                'ThanhTien' => $quantity * $product->GiaBan
            ]);
        }

        // Recompute promotions and apply discounts to cart items
        try {
            $promoService = new \App\Services\PromotionService();
            $items = $cart->chiTiet()->get()->map(function($it) {
                return [
                    'MaSP' => $it->MaSP,
                    'SoLuong' => $it->SoLuong,
                    'DonGia' => $it->DonGia
                ];
            })->toArray();

            $discounts = $promoService->evaluateForCart($user, $items);

            foreach ($cart->chiTiet as $it) {
                $ma = $it->MaSP;
                $discountInfo = $discounts[$ma] ?? ['SoTienGiam' => 0.00, 'MaKM_ApDung' => null];
                $it->SoTienGiam = $discountInfo['SoTienGiam'];
                $it->MaKM_ApDung = $discountInfo['MaKM_ApDung'];
                $it->ThanhTien = ($it->DonGia * $it->SoLuong) - $it->SoTienGiam;
                $it->save();
            }
        } catch (\Exception $e) {
            Log::error('Promotion apply failed', ['error' => $e->getMessage()]);
        }

        // Update totals
        $this->updateCartTotals($cart);

        Log::info('Added to cart successfully', ['cart_id' => $cart->MaDon]);

        return response()->json(['message' => 'Added to cart']);
    }

    public function remove(Request $request, $id)
    {
        $user = $request->attributes->get('khachhang');
        $cart = DonBanHang::where('MaKH', $user->MaKH)->where('TrangThai', 'cart')->first();
        if (!$cart) return response()->json(['message' => 'Cart not found'], 404);

        $item = $cart->chiTiet()->where('MaSP', $id)->first();
        if (!$item) return response()->json(['message' => 'Item not found'], 404);

        $item->delete();

        // Update totals
        $this->updateCartTotals($cart);

        return response()->json(['message' => 'Removed']);
    }

    private function updateCartTotals($cart)
    {
        $total = $cart->chiTiet->sum('ThanhTien');
        $cart->TongTienHang = $total;
        $cart->TongThanhToan = $total; // Assuming no tax/discount for cart
        $cart->save();
    }
}
