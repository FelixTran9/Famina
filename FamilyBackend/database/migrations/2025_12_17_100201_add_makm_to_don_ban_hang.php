<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaKmToDonBanHang extends Migration
{
    public function up()
    {
        Schema::table('DON_BAN_HANG', function (Blueprint $table) {
            if (!Schema::hasColumn('DON_BAN_HANG', 'MaKM_ApDung')) {
                $table->string('MaKM_ApDung', 64)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('DON_BAN_HANG', function (Blueprint $table) {
            if (Schema::hasColumn('DON_BAN_HANG', 'MaKM_ApDung')) {
                $table->dropColumn('MaKM_ApDung');
            }
        });
    }
}