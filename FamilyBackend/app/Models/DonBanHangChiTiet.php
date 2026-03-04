<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonBanHangChiTiet extends Model
{
    protected $table = 'CT_DON_BAN';
    protected $primaryKey = ['MaDon', 'MaSP'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'MaDon', 'MaSP', 'SoLuong', 'DonGia', 'ThueVAT', 'ChietKhau', 'ThanhTien', 'MaKM_ApDung', 'SoTienGiam'
    ];

    public function getKeyName()
    {
        return ['MaDon', 'MaSP'];
    }
}
