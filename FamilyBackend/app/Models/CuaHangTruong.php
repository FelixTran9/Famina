<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CuaHangTruong extends Authenticatable
{
    protected $table = 'CUA_HANG_TRUONG';
    protected $primaryKey = 'MaCHT';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaCHT', 'TenCHT', 'TaiKhoan', 'MatKhau', 'SDT', 'Email', 'DiaChi'
    ];

    protected $hidden = ['MatKhau'];

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}

