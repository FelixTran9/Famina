<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Increase MaDon length so generated cart IDs fit
        DB::statement("ALTER TABLE DON_BAN_HANG MODIFY MaDon VARCHAR(64);");
    }

    public function down()
    {
        DB::statement("ALTER TABLE DON_BAN_HANG MODIFY MaDon VARCHAR(20);");
    }
};
