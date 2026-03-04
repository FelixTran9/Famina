<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Nhà cung cấp</title>
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
        <h1 class="mb-4"><i class="fas fa-truck"></i> Quản lý Nhà cung cấp</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('suppliers.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm tên, mã, email" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary ms-auto">Thêm nhà cung cấp</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>MaNCC</th>
                    <th>Tên NCC</th>
                    <th>Mã số thuế</th>
                    <th>Địa chỉ</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td>{{ $s->MaNCC }}</td>
                    <td>{{ $s->TenNCC }}</td>
                    <td>{{ $s->MaSoThue }}</td>
                    <td>{{ $s->DiaChi }}</td>
                    <td>{{ $s->SDT }}</td>
                    <td>{{ $s->Email }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $s->MaNCC) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('suppliers.destroy', $s->MaNCC) }}" method="post" style="display:inline" onsubmit="return confirm('Xóa nhà cung cấp này?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">Không có nhà cung cấp</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $suppliers->links() }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
