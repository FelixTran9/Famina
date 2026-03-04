<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoColsToKhachHang extends Migration
{
    public function up()
    {
        Schema::table('KHACH_HANG', function (Blueprint $table) {
            if (!Schema::hasColumn('KHACH_HANG', 'LoaiKH')) {
                $table->string('LoaiKH', 50)->nullable();
            }
            if (!Schema::hasColumn('KHACH_HANG', 'TongTieuDung')) {
                $table->decimal('TongTieuDung', 15, 2)->default(0.00);
            }
            if (!Schema::hasColumn('KHACH_HANG', 'KhuyenMaiUuTien')) {
                $table->boolean('KhuyenMaiUuTien')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('KHACH_HANG', function (Blueprint $table) {
            if (Schema::hasColumn('KHACH_HANG', 'KhuyenMaiUuTien')) {
                $table->dropColumn('KhuyenMaiUuTien');
            }
            if (Schema::hasColumn('KHACH_HANG', 'TongTieuDung')) {
                $table->dropColumn('TongTieuDung');
            }
            if (Schema::hasColumn('KHACH_HANG', 'LoaiKH')) {
                $table->dropColumn('LoaiKH');
            }
        });
    }
}