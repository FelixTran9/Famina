<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Increase MaDon length on CT_DON_BAN to match DON_BAN_HANG
        DB::statement("ALTER TABLE CT_DON_BAN MODIFY MaDon VARCHAR(64);");
    }

    public function down()
    {
        DB::statement("ALTER TABLE CT_DON_BAN MODIFY MaDon VARCHAR(20);");
    }
};
