<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucSp extends Model
{
    protected $table = 'DANH_MUC_SP';
    protected $primaryKey = 'MaDanhMuc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaDanhMuc', 'TenDanhMuc', 'MoTa'
    ];
}
