<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuGiaoHang extends Model
{
    protected $table = 'PHIEU_GIAO_HANG';
    protected $primaryKey = 'MaPhieuGiao';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaPhieuGiao', 'NgayGiao', 'MaVanDon', 'TenNguoiNhan', 'SDTNguoiNhan',
        'DiaChiGiao', 'TenShipper', 'TrangThaiGiao', 'GhiChu', 'MaDon', 'NguoiGiao'
    ];
}
