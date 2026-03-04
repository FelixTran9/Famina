<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoColsToCtDonBan extends Migration
{
    public function up()
    {
        Schema::table('CT_DON_BAN', function (Blueprint $table) {
            if (!Schema::hasColumn('CT_DON_BAN', 'MaKM_ApDung')) {
                $table->string('MaKM_ApDung', 64)->nullable();
            }
            if (!Schema::hasColumn('CT_DON_BAN', 'SoTienGiam')) {
                $table->decimal('SoTienGiam', 15, 2)->default(0.00);
            }
        });
    }

    public function down()
    {
        Schema::table('CT_DON_BAN', function (Blueprint $table) {
            if (Schema::hasColumn('CT_DON_BAN', 'SoTienGiam')) {
                $table->dropColumn('SoTienGiam');
            }
            if (Schema::hasColumn('CT_DON_BAN', 'MaKM_ApDung')) {
                $table->dropColumn('MaKM_ApDung');
            }
        });
    }
}