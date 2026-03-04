<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý nhân sự</title>
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
        <h1 class="mb-4">Quản lý nhân sự</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex mb-3">
            <form class="me-2" method="get" action="{{ route('staff.index') }}">
                <div class="input-group">
                    <input name="q" class="form-control" placeholder="Tìm tên, tài khoản, sđt" value="{{ $q ?? '' }}">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </div>
            </form>
            <a href="{{ route('staff.create') }}" class="btn btn-primary ms-auto">Thêm nhân viên</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>MaNV</th>
                    <th>Tên</th>
                    <th>Tài khoản</th>
                    <th>SDT</th>
                    <th>MaTL</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $s)
                <tr>
                    <td>{{ $s->MaNV }}</td>
                    <td>{{ $s->TenNV }}</td>
                    <td>{{ $s->TaiKhoan }}</td>
                    <td>{{ $s->SDT }}</td>
                    <td>{{ $s->MaTL }}</td>
                    <td>
                        <a href="{{ route('staff.edit', $s->MaNV) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('staff.destroy', $s->MaNV) }}" method="post" style="display:inline" onsubmit="return confirm('Vô hiệu hóa nhân viên?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-danger">Vô hiệu</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Không có nhân viên</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $staff->links() }}
    </div>
</body>

</html>