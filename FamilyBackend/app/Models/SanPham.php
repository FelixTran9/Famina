<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'SAN_PHAM';
    protected $primaryKey = 'MaSP';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaSP','TenSP','QuyCach','DonViTinh','GiaVon','GiaBan','TonKho','HinhAnh','MaDanhMuc','MaNCC'
    ];
}
