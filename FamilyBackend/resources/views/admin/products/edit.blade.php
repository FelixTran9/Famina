<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">Sửa sản phẩm</h1>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="post" action="{{ route('products.update', $item->MaSP) }}" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="mb-3">
            <label class="form-label">Tên</label>
            <input name="TenSP" class="form-control" value="{{ old('TenSP', $item->TenSP) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quy cách</label>
            <input name="QuyCach" class="form-control" value="{{ old('QuyCach', $item->QuyCach) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Đơn vị tính</label>
            <input name="DonViTinh" class="form-control" value="{{ old('DonViTinh', $item->DonViTinh) }}">
        </div>
        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Giá vốn</label>
                <input name="GiaVon" class="form-control" value="{{ old('GiaVon', $item->GiaVon) ?? 0 }}" required>
            </div>
            <div class="col">
                <label class="form-label">Giá bán</label>
                <input name="GiaBan" class="form-control" value="{{ old('GiaBan', $item->GiaBan) ?? 0 }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Tồn kho</label>
            <input name="TonKho" class="form-control" value="{{ old('TonKho', $item->TonKho) ?? 0 }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Danh mục</label>
            <select name="MaDanhMuc" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $c)
                    <option value="{{ $c->MaDanhMuc }}" {{ (old('MaDanhMuc', $item->MaDanhMuc) == $c->MaDanhMuc) ? 'selected' : '' }}>{{ $c->MaDanhMuc }} - {{ $c->TenDanhMuc }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nhà cung cấp</label>
            <select name="MaNCC" class="form-select" required>
                <option value="">-- Chọn NCC --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->MaNCC }}" {{ (old('MaNCC', $item->MaNCC) == $s->MaNCC) ? 'selected' : '' }}>{{ $s->MaNCC }} - {{ $s->TenNCC }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Hình ảnh</label>
            <input type="file" name="HinhAnh" class="form-control">
            @if($item->HinhAnh)<div class="mt-2"><img src="/{{ $item->HinhAnh }}" style="height:60px"></div>@endif
        </div>

        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
