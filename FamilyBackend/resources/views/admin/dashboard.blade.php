<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FamilyMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <style>
        body { padding-left: 250px; }
    </style>
</head>
<body>

    @include('admin.partials.sidebar')

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-store"></i>
                FamilyMart Admin
            </a>
            <div class="nav-user ms-auto">
                <div class="user-avatar">
                    @if(auth('truong')->check())
                        {{ substr(auth('truong')->user()->TenCHT, 0, 1) }}
                    @elseif(auth('troly')->check())
                        {{ substr(auth('troly')->user()->TenTL, 0, 1) }}
                    @endif
                </div>
                <div>
                    @if(auth('truong')->check())
                        <div style="font-weight: 600;">{{ auth('truong')->user()->TenCHT }}</div>
                        <div style="font-size: 0.85rem; opacity: 0.8;">Trưởng cửa hàng</div>
                    @elseif(auth('troly')->check())
                        <div style="font-weight: 600;">{{ auth('troly')->user()->TenTL }}</div>
                        <div style="font-size: 0.85rem; opacity: 0.8;">Trợ lý cửa hàng</div>
                    @endif
                </div>
                <form method="post" action="{{ route('admin.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="container-lg">
            <!-- Header -->
            <div class="dashboard-header">
                <h1>Xin chào! 👋</h1>
                <p>Quản lý toàn bộ hoạt động của cửa hàng</p>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Nhân viên</h3>
                        <div class="number">{{ number_format($staffCount ?? 0) }}</div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Sản phẩm</h3>
                        <div class="number">{{ number_format($productCount ?? 0) }}</div>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Đơn hàng</h3>
                        <div class="number">{{ number_format($orderCount ?? 0) }}</div>
                    </div>
                </div>

                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Giao hàng</h3>
                        <div class="number">{{ number_format($shippingCount ?? 0) }}</div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="features-section">
                <h2>
                    <i class="fas fa-bars"></i>
                    Quản lý các chức năng
                </h2>

                <div class="features-grid">
                    <!-- Staff Management -->
                    <div class="feature-card staff">
                        <div class="feature-card-header">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-card-body">
                            <h3>Quản lý Nhân viên</h3>
                            <p>Thêm, sửa, xóa thông tin nhân viên. Quản lý các trợ lý cửa hàng và nhân viên bán hàng.</p>
                            <div class="feature-card-footer">
                                <a href="{{ route('staff.index') }}" class="btn-module primary">
                                    <i class="fas fa-list"></i> Danh sách
                                </a>
                                <a href="{{ route('staff.create') }}" class="btn-module secondary">
                                    <i class="fas fa-plus"></i> Thêm mới
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Products Management -->
                    <div class="feature-card products">
                        <div class="feature-card-header">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="feature-card-body">
                            <h3>Quản lý Sản phẩm</h3>
                            <p>Cập nhật kho hàng, thêm sản phẩm mới, quản lý giá bán, tồn kho và hình ảnh.</p>
                            <div class="feature-card-footer">
                                <a href="{{ route('products.index') }}" class="btn-module primary">
                                    <i class="fas fa-list"></i> Danh sách
                                </a>
                                <a href="{{ route('products.create') }}" class="btn-module secondary">
                                    <i class="fas fa-plus"></i> Thêm mới
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Management -->
                    <div class="feature-card orders">
                        <div class="feature-card-header">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="feature-card-body">
                            <h3>Quản lý Đơn hàng</h3>
                            <p>Xem và quản lý tất cả đơn hàng, cập nhật trạng thái, tính toán tổng tiền và chiết khấu.</p>
                            <div class="feature-card-footer">
                                <a href="{{ route('orders.index') }}" class="btn-module primary">
                                    <i class="fas fa-list"></i> Danh sách
                                </a>
                                <a href="{{ route('orders.create') }}" class="btn-module secondary">
                                    <i class="fas fa-plus"></i> Tạo mới
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Management -->
                    <div class="feature-card shipping">
                        <div class="feature-card-header">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="feature-card-body">
                            <h3>Quản lý Giao hàng</h3>
                            <p>Tạo phiếu giao hàng, theo dõi trạng thái giao hàng, cập nhật thông tin shipper.</p>
                            <div class="feature-card-footer">
                                <a href="{{ route('shipping.index') }}" class="btn-module primary">
                                    <i class="fas fa-list"></i> Danh sách
                                </a>
                                <a href="{{ route('shipping.create') }}" class="btn-module secondary">
                                    <i class="fas fa-plus"></i> Tạo mới
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>
                    <i class="fas fa-bolt"></i>
                    Hành động nhanh
                </h3>
                <div class="action-buttons">
                    <a href="{{ route('staff.create') }}" class="action-btn">
                        <i class="fas fa-user-plus"></i> Thêm nhân viên
                    </a>
                    <a href="{{ route('products.create') }}" class="action-btn">
                        <i class="fas fa-plus-circle"></i> Thêm sản phẩm
                    </a>
                    <a href="{{ route('orders.create') }}" class="action-btn">
                        <i class="fas fa-file-alt"></i> Tạo đơn hàng
                    </a>
                    <a href="{{ route('shipping.create') }}" class="action-btn">
                        <i class="fas fa-clipboard-list"></i> Tạo phiếu giao
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
