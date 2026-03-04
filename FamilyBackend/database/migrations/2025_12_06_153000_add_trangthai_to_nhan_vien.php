<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('NHAN_VIEN') && !Schema::hasColumn('NHAN_VIEN', 'TrangThai')) {
            Schema::table('NHAN_VIEN', function (Blueprint $table) {
                $table->string('TrangThai', 50)->default('active')->after('MaTL');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('NHAN_VIEN') && Schema::hasColumn('NHAN_VIEN', 'TrangThai')) {
            Schema::table('NHAN_VIEN', function (Blueprint $table) {
                $table->dropColumn('TrangThai');
            });
        }
    }
};
