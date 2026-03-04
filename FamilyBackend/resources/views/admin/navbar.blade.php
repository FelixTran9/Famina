<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">FamilyMart Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">
                        @if(auth('truong')->check())
                            <strong>{{ auth('truong')->user()->TenCHTruong ?? 'Trưởng cửa hàng' }}</strong>
                        @elseif(auth('troly')->check())
                            <strong>{{ auth('troly')->user()->TenTL ?? 'Trợ lý cửa hàng' }}</strong>
                        @endif
                    </span>
                </li>
                <li class="nav-item">
                    <form method="post" action="{{ route('admin.logout') }}" style="display: inline;">
                        @csrf
                        <button class="btn btn-outline-light btn-sm" type="submit">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
