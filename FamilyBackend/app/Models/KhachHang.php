<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'KHACH_HANG';
    protected $primaryKey = 'MaKH';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaKH', 'TenKH', 'SDT', 'DiaChi', 'Email', 'MatKhau', 'DiemTichLuy', 'api_token', 'LoaiKH', 'TongTieuDung', 'KhuyenMaiUuTien'
    ];

    protected $hidden = ['MatKhau', 'api_token'];

    public function carts()
    {
        return $this->hasMany(GioHang::class, 'MaKH', 'MaKH');
    }
}
