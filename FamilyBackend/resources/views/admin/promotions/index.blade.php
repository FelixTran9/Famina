<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Khuyến mãi</title>
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
        <h1 class="mb-4"><i class="fas fa-tags"></i> Quản lý Khuyến mãi</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('promotions.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm tên khuyến mãi" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('promotions.create') }}" class="btn btn-primary ms-auto">Thêm khuyến mãi</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Giá trị</th>
                    <th>Sản phẩm áp dụng</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                <tr>
                    <td><span class="badge bg-info">{{ substr($promo['id'], 0, 8) }}</span></td>
                    <td>{{ $promo['name'] }}</td>
                    <td>
                        @if($promo['type'] === 'percent')
                            <span class="badge bg-primary">{{ $promo['value'] }}%</span>
                        @else
                            <span class="badge bg-success">{{ number_format($promo['value']) }}đ</span>
                        @endif
                    </td>
                    <td>{{ $promo['value'] }}</td>
                    <td>
                        @if(empty($promo['products']))
                            <span class="badge bg-warning">Tất cả</span>
                        @else
                            {{ count($promo['products']) }} sản phẩm
                        @endif
                    </td>
                    <td>
                        @if($promo['starts_at'])
                            {{ \Carbon\Carbon::parse($promo['starts_at'])->format('d/m/Y') }}
                            @if($promo['ends_at'])
                                - {{ \Carbon\Carbon::parse($promo['ends_at'])->format('d/m/Y') }}
                            @endif
                        @else
                            Vô thời hạn
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('promotions.edit', $promo['id']) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('promotions.destroy', $promo['id']) }}" method="post" style="display:inline" onsubmit="return confirm('Xóa khuyến mãi này?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">Không có khuyến mãi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
