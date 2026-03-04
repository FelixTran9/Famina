<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-left: 250px;
        }
    </style>
</head>

<body>

    @include('admin.partials.sidebar')
    @include('admin.navbar')

    <div class="container p-4">
        <h1 class="mb-4"><i class="fas fa-users"></i> Quản lý Khách hàng</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('customers.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm tên, email, sđt" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('customers.create') }}" class="btn btn-primary ms-auto">Thêm khách hàng</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>MaKH</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Địa chỉ</th>
                    <th>Loại KH</th>
                    <th>Tổng tiêu dùng</th>
                    <th>Ưu tiên KM</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td>{{ $c->MaKH }}</td>
                    <td>{{ $c->TenKH }}</td>
                    <td>{{ $c->Email }}</td>
                    <td>{{ $c->SDT }}</td>
                    <td>{{ $c->DiaChi }}</td>
                    <td>{{ $c->LoaiKH ?? '-' }}</td>
                    <td>{{ number_format($c->TongTieuDung ?? 0) }}đ</td>
                    <td>
                        @if($c->KhuyenMaiUuTien)
                            <span class="badge bg-success">Có</span>
                        @else
                            <span class="badge bg-secondary">Không</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('customers.edit', $c->MaKH) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('customers.destroy', $c->MaKH) }}" method="post" style="display:inline" onsubmit="return confirm('Xóa khách hàng này?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">Không có khách hàng</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $customers->links() }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
