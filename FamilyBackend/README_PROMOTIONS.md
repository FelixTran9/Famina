# Family Backend - Promotions & Management Features

## Overview
This Laravel backend supports e-commerce features including promotions, customer management, and supplier management, built on a legacy MySQL database.

## New Features Added

### Promotions
- **Admin API**: CRUD operations for promotions stored in `storage/app/promotions.json`
- **Automatic Discounts**: Applied at cart add time, stored in `CT_DON_BAN.SoTienGiam` and `DON_BAN_HANG.MaKM_ApDung`
- **Frontend API**: `/api/promotions` returns products with computed discounted prices
- **Promotion Types**: Percent (%) or fixed amount discounts
- **Eligibility**: By product list, active date range

### Customer Management
- **Admin API**: View, update, delete customers
- **New Fields**: `LoaiKH` (type), `TongTieuDung` (total spent), `KhuyenMaiUuTien` (priority flag)
- **Search**: By name, ID, email

### Supplier Management
- **Admin API**: CRUD for suppliers (NhanVien table)
- **Fields**: Name, phone, address, username, role, status

## Database Changes
- Added columns to `KHACH_HANG`: `LoaiKH`, `TongTieuDung`, `KhuyenMaiUuTien`
- Added columns to `CT_DON_BAN`: `MaKM_ApDung`, `SoTienGiam`
- Added column to `DON_BAN_HANG`: `MaKM_ApDung` (if not exists)

## API Endpoints
- `GET /api/promotions` - Products with discounts
- `GET /api/admin/promotions` - List promotions
- `POST /api/admin/promotions` - Create promotion
- `PUT /api/admin/promotions/{id}` - Update promotion
- `DELETE /api/admin/promotions/{id}` - Delete promotion
- `GET /api/admin/customers` - List customers
- `GET /api/admin/customers/{id}` - Get customer
- `PUT /api/admin/customers/{id}` - Update customer
- `DELETE /api/admin/customers/{id}` - Delete customer
- `GET /api/admin/suppliers` - List suppliers
- `GET /api/admin/suppliers/{id}` - Get supplier
- `PUT /api/admin/suppliers/{id}` - Update supplier
- `DELETE /api/admin/suppliers/{id}` - Delete supplier

## Usage
1. Run migrations: `php artisan migrate`
2. Start server: `php artisan serve`
3. Test cart add: POST `/api/don-dat-hang/them` with product_code and quantity (authenticated)
4. Manage promotions via admin endpoints

## Notes
- Promotions are stored in JSON file for simplicity; can be migrated to DB later
- Discounts are computed server-side, not cached in product prices
- Legacy DB constraints respected: only added columns, no type changes