<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tạo phiếu giao hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">Tạo phiếu giao hàng</h1>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="post" action="{{ route('shipping.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Đơn hàng</label>
            <select id="donHangSelect" name="MaDon" class="form-select" required>
                <option value="">-- Chọn đơn hàng --</option>
                @foreach($dons as $d)
                    <option value="{{ $d->MaDon }}">
                        {{ $d->MaDon }} - KH: {{ $d->MaKH }} ({{ $d->TenKH }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tên người nhận</label>
            <input id="tenNguoiNhan" name="TenNguoiNhan" class="form-control" value="{{ old('TenNguoiNhan') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">SĐT người nhận</label>
            <input id="sdtNguoiNhan" name="SDTNguoiNhan" class="form-control" value="{{ old('SDTNguoiNhan') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ giao hàng</label>
            <textarea id="diaChiGiao" name="DiaChiGiao" class="form-control" required>{{ old('DiaChiGiao') }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Ngày giao</label>
                    <input type="date" name="NgayGiao" class="form-control" value="{{ old('NgayGiao') ?? date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Mã vận đơn</label>
                    <input name="MaVanDon" class="form-control" value="{{ old('MaVanDon') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tên Shipper</label>
                    <input name="TenShipper" class="form-control" value="{{ old('TenShipper') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Người giao (Nhân viên)</label>
                    <select name="NguoiGiao" class="form-select">
                        <option value="">-- Chọn NV --</option>
                        @foreach($nhanviens as $n)
                            <option value="{{ $n->MaNV }}">{{ $n->MaNV }} - {{ $n->TenNV }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <button class="btn btn-primary">Tạo phiếu</button>
        <a href="{{ route('shipping.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<script>
    // Dữ liệu đơn hàng từ server
    const donsData = {!! json_encode($dons) !!};
    
    const donSelect = document.getElementById('donHangSelect');
    const tenNguoiNhan = document.getElementById('tenNguoiNhan');
    const sdtNguoiNhan = document.getElementById('sdtNguoiNhan');
    const diaChiGiao = document.getElementById('diaChiGiao');

    donSelect.addEventListener('change', function() {
        const selectedMaDon = this.value;
        
        if (selectedMaDon) {
            // Tìm đơn hàng trong dữ liệu
            const don = donsData.find(d => d.MaDon === selectedMaDon);
            
            if (don) {
                tenNguoiNhan.value = don.TenKH || '';
                sdtNguoiNhan.value = don.SDT || '';
                diaChiGiao.value = don.DiaChi || '';
            }
        } else {
            tenNguoiNhan.value = '';
            sdtNguoiNhan.value = '';
            diaChiGiao.value = '';
        }
    });
</script>
</body>
</html>
