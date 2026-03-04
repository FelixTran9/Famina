<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'gio_hang';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'MaKH', 'MaSP', 'SoLuong'
    ];

    public function product()
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }
}
