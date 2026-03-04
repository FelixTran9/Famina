<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h1 class="mb-4">Đơn hàng</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('orders.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm mã đơn, mã KH, nhân viên" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('orders.create') }}" class="btn btn-primary ms-auto">Tạo đơn</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Mã KH</th>
                    <th>Nhân viên</th>
                    <th>Tổng thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td>{{ $o->MaDon }}</td>
                    <td>{{ $o->NgayDat }}</td>
                    <td>{{ $o->MaKH }}</td>
                    <td>{{ $o->NguoiBan }}</td>
                    <td>{{ number_format($o->TongThanhToan, 0, ',', '.') }}</td>
                    <td>
                        @if($o->TrangThai == 'chờ_xác_nhận')
                        <span class="badge bg-secondary">Chờ xác nhận</span>
                        @elseif($o->TrangThai == 'đã_xác_nhận')
                        <span class="badge bg-primary">Đã xác nhận</span>
                        @elseif($o->TrangThai == 'đang_giao')
                        <span class="badge bg-info">Đang giao</span>
                        @elseif($o->TrangThai == 'đã_giao')
                        <span class="badge bg-success">Đã giao</span>
                        @elseif($o->TrangThai == 'đã_hủy')
                        <span class="badge bg-danger">Đã hủy</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('orders.show', $o->MaDon) }}" class="btn btn-sm btn-info">Chi tiết</a>
                        <a href="{{ route('orders.edit', $o->MaDon) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('orders.destroy', $o->MaDon) }}" method="post" style="display:inline" onsubmit="return confirm('Xóa đơn hàng?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">Không có đơn hàng</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
</body>

</html>