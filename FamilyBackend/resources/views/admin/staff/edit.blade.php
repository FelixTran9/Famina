<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa nhân viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">Sửa nhân viên</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('staff.update', $nv->MaNV) }}">
        @csrf
        @method('put')
        <div class="mb-3">
            <label class="form-label">Tên</label>
            <input name="TenNV" class="form-control" value="{{ old('TenNV', $nv->TenNV) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tài khoản</label>
            <input name="TaiKhoan" class="form-control" value="{{ old('TaiKhoan', $nv->TaiKhoan) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu (để trống nếu không đổi)</label>
            <input type="password" name="MatKhau" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">SDT</label>
            <input name="SDT" class="form-control" value="{{ old('SDT', $nv->SDT) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input name="DiaChi" class="form-control" value="{{ old('DiaChi', $nv->DiaChi) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Trợ lý cửa hàng (MaTL)</label>
            <select name="MaTL" class="form-select" required>
                <option value="">-- Chọn trợ lý --</option>
                @foreach($trolys as $t)
                    <option value="{{ $t->MaTL }}" {{ (old('MaTL', $nv->MaTL) == $t->MaTL) ? 'selected' : '' }}>{{ $t->MaTL }} - {{ $t->TenTL }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
