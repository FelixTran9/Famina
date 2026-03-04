<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class NhanVien extends Authenticatable
{
    protected $table = 'NHAN_VIEN';
    protected $primaryKey = 'MaNV';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaNV', 'TenNV', 'SDT', 'DiaChi', 'TaiKhoan', 'MatKhau', 'MaTL', 'TrangThai'
    ];

    protected $hidden = ['MatKhau'];

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}
