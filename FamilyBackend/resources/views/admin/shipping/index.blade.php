<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý giao hàng</title>
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
        <h1 class="mb-4">Phiếu giao hàng</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('shipping.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm mã phiếu, mã đơn, vận đơn" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('shipping.create') }}" class="btn btn-primary ms-auto">Tạo phiếu</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Mã đơn</th>
                    <th>Người nhận</th>
                    <th>Địa chỉ giao</th>
                    <th>Ngày giao</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($phieus as $p)
                <tr>
                    <td>{{ $p->MaPhieuGiao }}</td>
                    <td>{{ $p->MaDon }}</td>
                    <td>{{ $p->TenNguoiNhan }}</td>
                    <td>{{ $p->DiaChiGiao }}</td>
                    <td>{{ $p->NgayGiao }}</td>
                    <td>
                        @if($p->TrangThaiGiao == 'chưa_giao')
                        <span class="badge bg-secondary">Chưa giao</span>
                        @elseif($p->TrangThaiGiao == 'đang_giao')
                        <span class="badge bg-info">Đang giao</span>
                        @elseif($p->TrangThaiGiao == 'đã_giao')
                        <span class="badge bg-success">Đã giao</span>
                        @elseif($p->TrangThaiGiao == 'giao_thất_bại')
                        <span class="badge bg-danger">Giao thất bại</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('shipping.edit', $p->MaPhieuGiao) }}" class="btn btn-sm btn-warning">Cập nhật</a>
                        <form action="{{ route('shipping.destroy', $p->MaPhieuGiao) }}" method="post" style="display:inline" onsubmit="return confirm('Xóa phiếu?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">Không có phiếu giao hàng</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $phieus->links() }}
    </div>
</body>

</html>