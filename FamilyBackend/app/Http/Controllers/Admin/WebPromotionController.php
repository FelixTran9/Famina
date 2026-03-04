<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WebPromotionController extends Controller
{
    protected $promoFile = 'promotions.json';

    public function index()
    {
        $promotions = $this->loadPromotions();
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'products' => 'nullable|string', // comma separated
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $promotions = $this->loadPromotions();
        $id = 'PROMO_' . time() . '_' . rand(100, 999);
        $products = $request->products ? array_map('trim', explode(',', $request->products)) : [];
        $promo = [
            'id' => $id,
            'name' => $request->name,
            'type' => $request->type,
            'value' => (float)$request->value,
            'products' => $products,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ];
        $promotions[] = $promo;
        $this->savePromotions($promotions);

        return redirect()->route('promotions.index')->with('success', 'Promotion created successfully');
    }

    public function edit($id)
    {
        $promotions = $this->loadPromotions();
        $promo = collect($promotions)->firstWhere('id', $id);
        if (!$promo) {
            abort(404);
        }
        return view('admin.promotions.edit', compact('promo'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'products' => 'nullable|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $promotions = $this->loadPromotions();
        $index = collect($promotions)->search(function ($p) use ($id) {
            return $p['id'] === $id;
        });

        if ($index === false) {
            abort(404);
        }

        $products = $request->products ? array_map('trim', explode(',', $request->products)) : [];
        $promotions[$index] = array_merge($promotions[$index], [
            'name' => $request->name,
            'type' => $request->type,
            'value' => (float)$request->value,
            'products' => $products,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        $this->savePromotions($promotions);

        return redirect()->route('promotions.index')->with('success', 'Promotion updated successfully');
    }

    public function destroy($id)
    {
        $promotions = $this->loadPromotions();
        $promotions = array_filter($promotions, function ($p) use ($id) {
            return $p['id'] !== $id;
        });
        $this->savePromotions(array_values($promotions));

        return redirect()->route('promotions.index')->with('success', 'Promotion deleted successfully');
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