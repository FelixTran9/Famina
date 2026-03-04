<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Khách hàng</title>
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
        <h1 class="mb-4"><i class="fas fa-plus"></i> Thêm Khách hàng mới</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="TenKH" class="form-label">Tên khách hàng</label>
                                <input type="text" class="form-control @error('TenKH') is-invalid @enderror" id="TenKH" name="TenKH" value="{{ old('TenKH') }}" required>
                                @error('TenKH')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('Email') is-invalid @enderror" id="Email" name="Email" value="{{ old('Email') }}">
                                @error('Email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SDT" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control @error('SDT') is-invalid @enderror" id="SDT" name="SDT" value="{{ old('SDT') }}">
                                @error('SDT')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="LoaiKH" class="form-label">Loại khách hàng</label>
                                <input type="text" class="form-control @error('LoaiKH') is-invalid @enderror" id="LoaiKH" name="LoaiKH" value="{{ old('LoaiKH') }}">
                                @error('LoaiKH')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="DiaChi" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('DiaChi') is-invalid @enderror" id="DiaChi" name="DiaChi" rows="3">{{ old('DiaChi') }}</textarea>
                        @error('DiaChi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="TongTieuDung" class="form-label">Tổng tiêu dùng</label>
                                <input type="number" class="form-control @error('TongTieuDung') is-invalid @enderror" id="TongTieuDung" name="TongTieuDung" value="{{ old('TongTieuDung', 0) }}" step="0.01">
                                @error('TongTieuDung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="KhuyenMaiUuTien" class="form-label">Ưu tiên khuyến mãi</label>
                                <select class="form-select @error('KhuyenMaiUuTien') is-invalid @enderror" id="KhuyenMaiUuTien" name="KhuyenMaiUuTien">
                                    <option value="0">Không</option>
                                    <option value="1">Có</option>
                                </select>
                                @error('KhuyenMaiUuTien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Tạo khách hàng</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
