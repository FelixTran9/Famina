<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sản phẩm</title>
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
        <h1 class="mb-4">Sản phẩm</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('products.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm tên, mã, danh mục" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('products.create') }}" class="btn btn-primary ms-auto">Thêm SP</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>MaSP</th>
                    <th>Tên</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Hình</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $it)
                <tr>
                    <td>{{ $it->MaSP }}</td>
                    <td>{{ $it->TenSP }}</td>
                    <td>{{ $it->GiaBan }}</td>
                    <td>{{ $it->TonKho }}</td>
                    <td>@if($it->HinhAnh)<img src="/{{ $it->HinhAnh }}" alt="" style="height:40px">@endif</td>
                    <td>
                        <a href="{{ route('products.edit', $it->MaSP) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('products.destroy', $it->MaSP) }}" method="post" style="display:inline" onsubmit="return confirm('Xóa sản phẩm?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Không có sản phẩm</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
</body>

</html>