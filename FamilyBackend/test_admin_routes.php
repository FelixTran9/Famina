<?php
/**
 * Test Script - Verify Admin Routes and Controllers
 * Run: php test_admin_routes.php
 */

echo "=== ADMIN ROUTES & CONTROLLERS VERIFICATION ===\n\n";

// Test 1: Routes List
echo "1. CHECKING ROUTES...\n";
$routesOutput = shell_exec('php artisan route:list 2>&1 | grep -E "admin/(promotions|customers|suppliers)"');
$adminRoutes = array_filter(explode("\n", $routesOutput));
echo "   Found " . count($adminRoutes) . " admin routes\n";
if (count($adminRoutes) >= 18) {
    echo "   ✓ All expected routes found\n";
} else {
    echo "   ✗ Missing routes\n";
}

// Test 2: Controllers Exist
echo "\n2. CHECKING CONTROLLERS...\n";
$controllers = [
    'app/Http/Controllers/Admin/WebPromotionController.php',
    'app/Http/Controllers/Admin/WebCustomerController.php',
    'app/Http/Controllers/Admin/WebSupplierController.php',
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        echo "   ✓ $controller exists\n";
    } else {
        echo "   ✗ $controller missing\n";
    }
}

// Test 3: Views Exist
echo "\n3. CHECKING VIEWS...\n";
$views = [
    'resources/views/admin/promotions/index.blade.php',
    'resources/views/admin/promotions/create.blade.php',
    'resources/views/admin/promotions/edit.blade.php',
    'resources/views/admin/customers/index.blade.php',
    'resources/views/admin/customers/create.blade.php',
    'resources/views/admin/customers/edit.blade.php',
    'resources/views/admin/suppliers/index.blade.php',
    'resources/views/admin/suppliers/create.blade.php',
    'resources/views/admin/suppliers/edit.blade.php',
];

foreach ($views as $view) {
    if (file_exists($view)) {
        echo "   ✓ $view exists\n";
    } else {
        echo "   ✗ $view missing\n";
    }
}

// Test 4: Models Fillable
echo "\n4. CHECKING MODEL FILLABLES...\n";
require 'app/Models/KhachHang.php';
require 'app/Models/NhanVien.php';

$kh = new \App\Models\KhachHang();
$nv = new \App\Models\NhanVien();

$khFields = ['TenKH', 'Email', 'SDT', 'LoaiKH', 'TongTieuDung', 'KhuyenMaiUuTien'];
$nvFields = ['TenNV', 'TaiKhoan', 'MatKhau', 'SDT', 'MaTL', 'TrangThai'];

foreach ($khFields as $field) {
    if (in_array($field, $kh->getFillable())) {
        echo "   ✓ KhachHang.$field fillable\n";
    } else {
        echo "   ✗ KhachHang.$field NOT fillable\n";
    }
}

foreach ($nvFields as $field) {
    if (in_array($field, $nv->getFillable())) {
        echo "   ✓ NhanVien.$field fillable\n";
    } else {
        echo "   ✗ NhanVien.$field NOT fillable\n";
    }
}

// Test 5: Migrations
echo "\n5. CHECKING MIGRATIONS...\n";
$migrationsOutput = shell_exec('php artisan migrate:status 2>&1 | grep "promo"');
if (strpos($migrationsOutput, 'Ran') !== false) {
    echo "   ✓ Promotion migrations ran\n";
} else {
    echo "   ✗ Promotion migrations not ran\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "\nTo start the server, run:\n";
echo "  php artisan serve\n\n";
echo "Access admin panel at:\n";
echo "  http://127.0.0.1:8000/admin/promotions\n";
echo "  http://127.0.0.1:8000/admin/customers\n";
echo "  http://127.0.0.1:8000/admin/suppliers\n";
?>
