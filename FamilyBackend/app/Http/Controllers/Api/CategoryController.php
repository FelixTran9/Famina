<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhMucSp;

class CategoryController extends Controller
{
    public function index()
    {
        $cats = DanhMucSp::all()->map(function($c){
            return [
                'id' => $c->MaDanhMuc,
                'name' => $c->TenDanhMuc,
                'description' => $c->MoTa
            ];
        });

        return response()->json($cats);
    }
}
