<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonBanHang extends Model
{
    protected $table = 'DON_BAN_HANG';
    protected $primaryKey = 'MaDon';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaDon', 'NgayDat', 'TongTienHang', 'TongThueVAT', 'TongChietKhau',
        'TongThanhToan', 'HinhThucTT', 'TrangThai', 'LoaiDon', 'MaKH', 'NguoiBan',
        'MaKM_ApDung', 'MaSP_ApDung', 'SoLuong'
    ];

    public function chiTiet()
    {
        return $this->hasMany(DonBanHangChiTiet::class, 'MaDon', 'MaDon');
    }
}
