<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark h-100 fixed-start" style="width: 280px; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100;">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4 fw-bold text-info"><i class="fas fa-store me-2"></i>FAMILY MART</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-white' }}" aria-current="page">
                <i class="fas fa-chart-pie me-2"></i>
                Tổng quan
            </a>
        </li>
        <li>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-box me-2"></i>
                Sản phẩm
            </a>
        </li>
        <li>
            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-shopping-cart me-2"></i>
                Đơn hàng
            </a>
        </li>
        <li>
            <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-users me-2"></i>
                Nhân viên
            </a>
        </li>
        <li>
            <a href="{{ route('shipping.index') }}" class="nav-link {{ request()->routeIs('shipping.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-truck me-2"></i>
                Vận chuyển
            </a>
        </li>
        <li>
            <a href="{{ route('promotions.index') }}" class="nav-link {{ request()->routeIs('promotions.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-tags me-2"></i>
                Khuyến mãi
            </a>
        </li>
        <li>
            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-user-friends me-2"></i>
                Khách hàng
            </a>
        </li>
        <li>
            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : 'text-white' }}">
                <i class="fas fa-building me-2"></i>
                Nhà cung cấp
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px;">
                @if(auth('truong')->check())
                    {{ substr(auth('truong')->user()->TenCHT, 0, 1) }}
                @elseif(auth('troly')->check())
                    {{ substr(auth('troly')->user()->TenTL, 0, 1) }}
                @else
                    A
                @endif
            </div>
            <strong>
                @if(auth('truong')->check())
                    {{ auth('truong')->user()->TenCHT }}
                @elseif(auth('troly')->check())
                    {{ auth('troly')->user()->TenTL }}
                @else
                    Admin
                @endif
            </strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item" type="submit">Đăng xuất</button>
                </form>
            </li>
        </ul>
    </div>
</div>