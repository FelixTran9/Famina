<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Nhà cung cấp</title>
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
        <h1 class="mb-4"><i class="fas fa-plus"></i> Thêm Nhà cung cấp</h1>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('suppliers.store') }}" method="post">
            @csrf
            <div class="mb-3">
                <label class="form-label">Tên nhà cung cấp</label>
                <input name="TenNCC" class="form-control @error('TenNCC') is-invalid @enderror" value="{{ old('TenNCC') }}" required>
                @error('TenNCC')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mã số thuế</label>
                <input name="MaSoThue" class="form-control @error('MaSoThue') is-invalid @enderror" value="{{ old('MaSoThue') }}">
                @error('MaSoThue')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <textarea name="DiaChi" class="form-control @error('DiaChi') is-invalid @enderror" required>{{ old('DiaChi') }}</textarea>
                @error('DiaChi')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">SĐT</label>
                <input name="SDT" class="form-control @error('SDT') is-invalid @enderror" value="{{ old('SDT') }}" required>
                @error('SDT')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="Email" type="email" class="form-control @error('Email') is-invalid @enderror" value="{{ old('Email') }}" required>
                @error('Email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Tạo nhà cung cấp</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
