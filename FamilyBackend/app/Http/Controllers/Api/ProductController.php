<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SanPham;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = SanPham::query();

        // Xử lý tìm kiếm
        if ($q = $request->query('q')) {
            $query->where('TenSP', 'like', "%{$q}%")
                  ->orWhere('MaSP', 'like', "%{$q}%");
        }

        // Map dữ liệu Legacy (Tiếng Việt) sang chuẩn JSON cho Frontend
        $products = $query->get()->map(function($p) {
            return [
                'id' => $p->MaSP,            // ID duy nhất
                'product_code' => $p->MaSP,  // Frontend cần key này
                'name' => $p->TenSP,
                'price' => (float)$p->GiaBan,
                'image' => $p->HinhAnh,
                'category_id' => $p->MaDanhMuc,
                'in_stock' => (int)$p->TonKho
            ];
        });

        return response()->json($products);
    }

    public function promotions(Request $request)
    {
        $promoService = new \App\Services\PromotionService();
        $products = SanPham::all()->map(function($p) use ($promoService) {
            $promos = $promoService->getActivePromotionsForProduct($p->MaSP);
            $originalPrice = (float)$p->GiaBan;
            $discountedPrice = $originalPrice;
            $discountAmount = 0.0;
            $promoId = null;

            foreach ($promos as $promo) {
                $discount = 0.0;
                if ($promo['type'] === 'percent') {
                    $discount = $originalPrice * ($promo['value'] / 100.0);
                } elseif ($promo['type'] === 'amount') {
                    $discount = min($promo['value'], $originalPrice);
                }
                if ($discount > $discountAmount) {
                    $discountAmount = $discount;
                    $promoId = $promo['id'];
                }
            }

            $discountedPrice = max(0, $originalPrice - $discountAmount);

            return [
                'id' => $p->MaSP,
                'product_code' => $p->MaSP,
                'name' => $p->TenSP,
                'original_price' => $originalPrice,
                'discounted_price' => $discountedPrice,
                'discount_amount' => round($discountAmount, 2),
                'promotion_id' => $promoId,
                'image' => $p->HinhAnh,
                'category_id' => $p->MaDanhMuc,
                'in_stock' => (int)$p->TonKho
            ];
        });

        return response()->json($products);
    }}
