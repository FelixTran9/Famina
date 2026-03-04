<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    protected $table = 'nha_cung_cap';
    protected $primaryKey = 'MaNCC';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaNCC',
        'TenNCC',
        'MaSoThue',
        'DiaChi',
        'SDT',
        'Email',
    ];
}
