<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('don_ban_hang', function (Blueprint $table) {
            $table->integer('SoLuong')->default(0)->after('LoaiDon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_ban_hang', function (Blueprint $table) {
            $table->dropColumn('SoLuong');
        });
    }
};
