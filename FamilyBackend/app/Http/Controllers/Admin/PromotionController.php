<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    protected $promoFile = 'promotions.json';

    public function index()
    {
        $promotions = $this->loadPromotions();
        return response()->json($promotions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'products' => 'array',
            'products.*' => 'string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $promotions = $this->loadPromotions();
        $id = 'PROMO_' . time() . '_' . rand(100, 999);
        $promo = [
            'id' => $id,
            'name' => $request->name,
            'type' => $request->type,
            'value' => (float)$request->value,
            'products' => $request->products ?? [],
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ];
        $promotions[] = $promo;
        $this->savePromotions($promotions);

        return response()->json($promo, 201);
    }

    public function show($id)
    {
        $promotions = $this->loadPromotions();
        $promo = collect($promotions)->firstWhere('id', $id);
        if (!$promo) {
            return response()->json(['message' => 'Promotion not found'], 404);
        }
        return response()->json($promo);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'products' => 'array',
            'products.*' => 'string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $promotions = $this->loadPromotions();
        $index = collect($promotions)->search(function ($p) use ($id) {
            return $p['id'] === $id;
        });

        if ($index === false) {
            return response()->json(['message' => 'Promotion not found'], 404);
        }

        $promotions[$index] = array_merge($promotions[$index], [
            'name' => $request->name,
            'type' => $request->type,
            'value' => (float)$request->value,
            'products' => $request->products ?? [],
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        $this->savePromotions($promotions);

        return response()->json($promotions[$index]);
    }

    public function destroy($id)
    {
        $promotions = $this->loadPromotions();
        $promotions = array_filter($promotions, function ($p) use ($id) {
            return $p['id'] !== $id;
        });
        $this->savePromotions(array_values($promotions));

        return response()->json(['message' => 'Deleted']);
    }

    protected function loadPromotions()
    {
        if (!Storage::exists($this->promoFile)) {
            return [];
        }
        $json = Storage::get($this->promoFile);
        return json_decode($json, true) ?? [];
    }

    protected function savePromotions(array $promotions)
    {
        Storage::put($this->promoFile, json_encode($promotions, JSON_PRETTY_PRINT));
    }
}