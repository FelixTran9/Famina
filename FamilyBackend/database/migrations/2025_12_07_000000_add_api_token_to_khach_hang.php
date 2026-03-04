<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('KHACH_HANG', function (Blueprint $table) {
            if (!Schema::hasColumn('KHACH_HANG', 'api_token')) {
                $table->string('api_token', 100)->nullable()->after('MatKhau');
            }
        });
    }

    public function down()
    {
        Schema::table('KHACH_HANG', function (Blueprint $table) {
            if (Schema::hasColumn('KHACH_HANG', 'api_token')) {
                $table->dropColumn('api_token');
            }
        });
    }
};
