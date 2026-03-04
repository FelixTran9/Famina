<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Use raw SQL to make NguoiBan nullable (avoid doctrine/dbal dependency)
        DB::statement('ALTER TABLE DON_BAN_HANG MODIFY NguoiBan VARCHAR(20) NULL');
    }

    public function down()
    {
        // Revert to non-nullable
        DB::statement('ALTER TABLE DON_BAN_HANG MODIFY NguoiBan VARCHAR(20) NOT NULL');
    }
};
