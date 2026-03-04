<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TroLyCuaHang extends Authenticatable
{
    protected $table = 'TRO_LY_CUA_HANG';
    protected $primaryKey = 'MaTL';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaTL', 'TenTL', 'TaiKhoan', 'MatKhau', 'SDT', 'Email', 'DiaChi'
    ];

    protected $hidden = ['MatKhau'];

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}
