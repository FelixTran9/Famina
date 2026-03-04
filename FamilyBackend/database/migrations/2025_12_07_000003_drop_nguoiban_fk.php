<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the FK constraint that requires NguoiBan to reference NHAN_VIEN
        // This allows customer orders to be created without assigning a staff member initially
        $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
            WHERE TABLE_NAME = 'DON_BAN_HANG' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'FK_DonBan_NhanVien'");
        if ($fkExists) {
            DB::statement('ALTER TABLE DON_BAN_HANG DROP FOREIGN KEY FK_DonBan_NhanVien');
        }
    }

    public function down()
    {
        // Restore the FK constraint if needed
        DB::statement('ALTER TABLE DON_BAN_HANG ADD CONSTRAINT FK_DonBan_NhanVien 
            FOREIGN KEY (NguoiBan) REFERENCES NHAN_VIEN(MaNV) ON UPDATE CASCADE');
    }
};
