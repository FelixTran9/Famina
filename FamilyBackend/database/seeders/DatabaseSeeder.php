<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Xóa dữ liệu cũ (không dùng truncate vì có foreign key)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('CUA_HANG_TRUONG')->delete();
        DB::table('TRO_LY_CUA_HANG')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Tạo tài khoản Trưởng cửa hàng
        DB::table('CUA_HANG_TRUONG')->insert([
            'MaCHT' => 'CHT001',
            'TenCHT' => 'Nguyễn Văn A - Trưởng cửa hàng',
            'TaiKhoan' => 'truong001',
            'MatKhau' => '123456789',
            'SDT' => '0987654321',
            'DiaChi' => 'Tầng 8, Toà nhà An Khánh, 63 Phạm Ngọc Thạch, Q.3, TP.HCM'
        ]);

        DB::table('CUA_HANG_TRUONG')->insert([
            'MaCHT' => 'CHT002',
            'TenCHT' => 'Trần Thị B - Trưởng cửa hàng',
            'TaiKhoan' => 'truong002',
            'MatKhau' => '123456789',
            'SDT' => '0912345678',
            'DiaChi' => '123 Đường B, Q.1, TP.HCM'
        ]);

        // Tạo tài khoản Trợ lý cửa hàng
        DB::table('TRO_LY_CUA_HANG')->insert([
            'MaCHT' => 'CHT001',
            'MaTL' => 'TL001',
            'TenTL' => 'Lê Văn C - Trợ lý',
            'TaiKhoan' => 'troly001',
            'MatKhau' => '123456789',
            'SDT' => '0901234567',
            'DiaChi' => 'Tầng 8, Toà nhà An Khánh, 63 Phạm Ngọc Thạch, Q.3, TP.HCM'
        ]);

        DB::table('TRO_LY_CUA_HANG')->insert([
            'MaCHT' => 'CHT001',
            'MaTL' => 'TL002',
            'TenTL' => 'Phạm Thị D - Trợ lý',
            'TaiKhoan' => 'troly002',
            'MatKhau' => '123456789',
            'SDT' => '0923456789',
            'DiaChi' => '456 Đường C, Q.2, TP.HCM'
        ]);

        $this->command->info('✅ Dữ liệu admin đã được tạo thành công!');
        $this->command->info('');
        $this->command->line('📋 Tài khoản Trưởng cửa hàng:');
        $this->command->line('  - Tài khoản: truong001 | Mật khẩu: 123456789');
        $this->command->line('  - Tài khoản: truong002 | Mật khẩu: 123456789');
        $this->command->line('');
        $this->command->line('📋 Tài khoản Trợ lý cửa hàng:');
        $this->command->line('  - Tài khoản: troly001 | Mật khẩu: 123456789');
        $this->command->line('  - Tài khoản: troly002 | Mật khẩu: 123456789');
    }
}

