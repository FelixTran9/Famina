<?php

namespace App\Services;

use Carbon\Carbon;

class PromotionService
{
    protected $promotions = null;

    protected function loadPromotions()
    {
        if ($this->promotions !== null) return $this->promotions;

        $path = storage_path('app/promotions.json');
        if (!file_exists($path)) {
            $this->promotions = [];
            return $this->promotions;
        }

        $json = file_get_contents($path);
        $arr = json_decode($json, true);
        $this->promotions = is_array($arr) ? $arr : [];
        return $this->promotions;
    }

    public function getActivePromotionsForProduct(string $maSP)
    {
        $promos = $this->loadPromotions();
        $now = Carbon::now();
        $found = [];

        foreach ($promos as $p) {
            $starts = isset($p['starts_at']) ? Carbon::parse($p['starts_at']) : null;
            $ends = isset($p['ends_at']) ? Carbon::parse($p['ends_at']) : null;

            if ($starts && $now->lt($starts)) continue;
            if ($ends && $now->gt($ends)) continue;

            // match by explicit product list
            if (!empty($p['products'])) {
                if (in_array($maSP, $p['products'])) {
                    $found[] = $p;
                    continue;
                }
            } else {
                // No product list means apply to all
                $found[] = $p;
                continue;
            }

            // match by category (optional)
            if (!empty($p['categories']) && !empty($p['category_map'])) {
                // category_map is a map MaSP => MaDanhMuc loaded externally by caller if needed
            }
        }

        return $found;
    }

    public function evaluateForCart($user, array $cartItems)
    {
        // $cartItems: array of ['MaSP'=>..., 'SoLuong'=>..., 'DonGia'=>...]
        $result = [];
        foreach ($cartItems as $item) {
            $maSP = $item['MaSP'];
            $qty = isset($item['SoLuong']) ? (int)$item['SoLuong'] : 1;
            $price = isset($item['DonGia']) ? (float)$item['DonGia'] : 0.0;
            $subtotal = $price * $qty;

            $bestDiscount = 0.0;
            $appliedPromoId = null;

            $promos = $this->getActivePromotionsForProduct($maSP);
            foreach ($promos as $p) {
                $discount = 0.0;
                if (isset($p['type']) && $p['type'] === 'percent' && isset($p['value'])) {
                    $discount = $subtotal * (float)$p['value'] / 100.0;
                } elseif (isset($p['type']) && $p['type'] === 'amount' && isset($p['value'])) {
                    $discount = min((float)$p['value'], $subtotal);
                }

                if ($discount > $bestDiscount) {
                    $bestDiscount = $discount;
                    $appliedPromoId = $p['id'] ?? ($p['name'] ?? null);
                }
            }

            $result[$maSP] = [
                'SoTienGiam' => round($bestDiscount, 2),
                'MaKM_ApDung' => $appliedPromoId
            ];
        }

        return $result;
    }
}
