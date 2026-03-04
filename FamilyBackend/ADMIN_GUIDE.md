# Admin Panel - Hệ thống Quản lý Promotions, Customers, Suppliers

## 📋 Tính năng

### 1. **Quản lý Khuyến mãi (Promotions)**
- ✅ CRUD (Create, Read, Update, Delete) khuyến mãi
- ✅ Hai loại khuyến mãi: **Phần trăm (%)** và **Số tiền (VNĐ)**
- ✅ Áp dụng cho tất cả sản phẩm hoặc sản phẩm cụ thể
- ✅ Thiết lập thời gian có hiệu lực (bắt đầu - kết thúc)
- ✅ Lưu trữ trong JSON file (storage/app/promotions.json)
- ✅ Tự động áp dụng khi khách hàng thêm hàng vào giỏ

**Đường dẫn**: `/admin/promotions`

### 2. **Quản lý Khách hàng (Customers)**
- ✅ CRUD khách hàng
- ✅ Thông tin: Tên, Email, SĐT, Địa chỉ, Loại KH
- ✅ Theo dõi tổng tiêu dùng
- ✅ Đánh dấu ưu tiên khuyến mãi
- ✅ Tìm kiếm theo tên, ID, email
- ✅ Phân trang (20 records/trang)

**Đường dẫn**: `/admin/customers`

### 3. **Quản lý Nhà cung cấp (Suppliers)**
- ✅ CRUD nhà cung cấp (dựa trên model NhanVien)
- ✅ Thông tin: Tên, Tài khoản, Mật khẩu, SĐT, Địa chỉ
- ✅ Quản lý trạng thái (Hoạt động/Không hoạt động)
- ✅ Tìm kiếm theo tên, ID, tài khoản
- ✅ Phân trang (20 records/trang)

**Đường dẫn**: `/admin/suppliers`

---

## 🛠️ Cấu trúc Hệ thống

### Controllers
```
app/Http/Controllers/Admin/
├── WebPromotionController.php
├── WebCustomerController.php
├── WebSupplierController.php
├── PromotionController.php (API)
├── CustomerController.php (API)
└── SupplierController.php (API)
```

### Views
```
resources/views/admin/
├── layout.blade.php (Base layout)
├── partials/sidebar.blade.php (Navigation)
├── promotions/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── customers/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── suppliers/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### Models
- `KhachHang`: Quản lý khách hàng (fields mở rộng: LoaiKH, TongTieuDung, KhuyenMaiUuTien)
- `NhanVien`: Quản lý nhà cung cấp

### Services
- `PromotionService`: Xử lý logic khuyến mãi (loadPromotions, evaluateForCart, etc.)

---

## 🚀 Cách Sử Dụng

### 1. **Khởi động Server**
```bash
php artisan serve
```
Server sẽ chạy tại `http://127.0.0.1:8000`

### 2. **Đăng nhập Admin**
- Truy cập: `http://127.0.0.1:8000/admin/login`
- Dùng tài khoản admin có sẵn

### 3. **Quản lý Khuyến mãi**
- **Xem danh sách**: Sidebar → "Khuyến mãi" hoặc `/admin/promotions`
- **Thêm mới**: Click nút "Thêm khuyến mãi"
  - Nhập Tên, chọn Loại (%, VNĐ), nhập Giá trị
  - Tùy chọn: Chọn sản phẩm áp dụng (comma-separated) hoặc để trống = tất cả
  - Chọn ngày bắt đầu & kết thúc
- **Sửa**: Click icon "Sửa" trên hàng
- **Xóa**: Click icon "Xóa" trên hàng

### 4. **Quản lý Khách hàng**
- **Xem danh sách**: Sidebar → "Khách hàng" hoặc `/admin/customers`
- **Tìm kiếm**: Nhập tên/ID/email vào ô "Tìm kiếm"
- **Thêm mới**: Click nút "Thêm khách hàng"
  - Nhập đầy đủ thông tin: Tên, Email, SĐT, Địa chỉ, Loại
  - Nhập Tổng tiêu dùng (mặc định 0)
  - Chọn Ưu tiên khuyến mãi (Có/Không)
- **Sửa**: Click icon "Sửa"
- **Xóa**: Click icon "Xóa"

### 5. **Quản lý Nhà cung cấp**
- **Xem danh sách**: Sidebar → "Nhà cung cấp" hoặc `/admin/suppliers`
- **Tìm kiếm**: Nhập tên/ID/tài khoản vào ô "Tìm kiếm"
- **Thêm mới**: Click nút "Thêm nhà cung cấp"
  - Nhập Tên, Tài khoản (duy nhất), Mật khẩu
  - Nhập SĐT, Địa chỉ, Mã trợ lý
  - Chọn Trạng thái (Hoạt động/Không hoạt động)
- **Sửa**: Click icon "Sửa"
- **Xóa**: Click icon "Xóa"

---

## 📡 API Endpoints

### Promotions API
```
GET    /api/admin/promotions              # Danh sách
POST   /api/admin/promotions              # Tạo mới
GET    /api/admin/promotions/{id}         # Chi tiết
PUT    /api/admin/promotions/{id}         # Cập nhật
DELETE /api/admin/promotions/{id}         # Xóa
```

### Customers API
```
GET    /api/admin/customers               # Danh sách
GET    /api/admin/customers/{id}          # Chi tiết
PUT    /api/admin/customers/{id}          # Cập nhật
DELETE /api/admin/customers/{id}          # Xóa
```

### Suppliers API
```
GET    /api/admin/suppliers               # Danh sách
GET    /api/admin/suppliers/{id}          # Chi tiết
PUT    /api/admin/suppliers/{id}          # Cập nhật
DELETE /api/admin/suppliers/{id}          # Xóa
```

---

## 🗄️ Database Changes

### Migrations Applied
1. `add_promo_cols_to_khach_hang`: Thêm LoaiKH, TongTieuDung, KhuyenMaiUuTien vào KHACH_HANG
2. `add_promo_cols_to_ct_don_ban`: Thêm MaKM_ApDung, SoTienGiam vào CT_DON_BAN
3. `add_makm_to_don_ban_hang`: Thêm MaKM_ApDung vào DON_BAN_HANG

### Storage
- Promotions được lưu tại: `storage/app/promotions.json`

---

## 🎨 Giao diện

- **Framework**: Bootstrap 5
- **Icons**: Font Awesome 6
- **CSS**: Custom admin-dashboard.css
- **Responsive**: Mobile-friendly

---

## 🔐 Bảo mật

- Tất cả routes admin được bảo vệ bởi middleware `admin`
- CSRF protection trên tất cả forms
- Password hashed với Bcrypt
- API token authentication cho API endpoints

---

## ⚙️ Cấu hình

### Config Files
- `config/app.php`: Cấu hình ứng dụng
- `config/auth.php`: Cấu hình authentication
- `.env`: Environment variables

### Key Environment Variables
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quanlycuahang
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🧪 Testing

Chạy test verification:
```bash
php test_admin_routes.php
```

---

## 📝 Notes

- Khuyến mãi được lưu dưới dạng JSON để dễ quản lý và không ảnh hưởng DB schema legacy
- Khi khách hàng thêm hàng vào giỏ, hệ thống tự động tìm khuyến mãi áp dụng
- Các thay đổi trực tiếp trên DB cần migrate lại (chỉ thêm column, không đổi type)
- Sidebar tự động active menu dựa trên current route

---

## 🔄 Integration Points

1. **Cart API** (`/api/cart/add`): Auto-apply promotions khi add item
2. **News Page**: Hiển thị active promotions
3. **Admin Dashboard**: Navigation to all management pages
4. **API Endpoints**: Hỗ trợ CRUD via HTTP requests

---

## 📞 Support

Nếu gặp lỗi:
1. Check logs: `storage/logs/laravel.log`
2. Run migrations: `php artisan migrate`
3. Clear cache: `php artisan cache:clear && php artisan view:clear`
4. Check routes: `php artisan route:list`

---

**Created**: December 17, 2025  
**Last Updated**: December 17, 2025
