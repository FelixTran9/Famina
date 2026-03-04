<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập nhật phiếu giao hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('admin.navbar')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Cập nhật phiếu giao #{{ $phieu->MaPhieuGiao }}</h1>
            <a href="{{ route('shipping.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="{{ route('shipping.update', $phieu->MaPhieuGiao) }}">
                    @csrf
                    @method('put')

                    @php
                    $currentStatus = $phieu->TrangThaiGiao;
                    // Logic đơn giản: Nếu đã kết thúc (đã giao/thất bại) thì khóa luôn
                    $isFinished = in_array($currentStatus, ['đã_giao', 'giao_thất_bại']);
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Trạng thái giao hàng</label>

                            @if($isFinished)
                            <div class="alert {{ $currentStatus == 'đã_giao' ? 'alert-success' : 'alert-danger' }}">
                                Phiếu này đã kết thúc với trạng thái: <strong>{{ $currentStatus }}</strong>.
                            </div>
                            <input type="hidden" name="TrangThaiGiao" value="{{ $currentStatus }}">
                            @else
                            <select name="TrangThaiGiao" class="form-select" required>
                                {{-- 1. Chưa giao --}}
                                <option value="chưa_giao"
                                    {{ $currentStatus == 'chưa_giao' ? 'selected' : '' }}
                                    {{ $currentStatus != 'chưa_giao' ? 'disabled' : '' }}>
                                    ⏳ Chưa giao
                                </option>

                                {{-- 2. Đang giao (Chỉ chọn được nếu đang là chưa_giao hoặc đang_giao) --}}
                                <option value="đang_giao"
                                    {{ $currentStatus == 'đang_giao' ? 'selected' : '' }}
                                    {{ $currentStatus == 'đã_giao' || $currentStatus == 'giao_thất_bại' ? 'disabled' : '' }}>
                                    🚚 Đang giao
                                </option>

                                {{-- 3. Kết thúc (Chỉ chọn được nếu đang là đang_giao) --}}
                                <option value="đã_giao"
                                    {{ $currentStatus == 'đã_giao' ? 'selected' : '' }}
                                    {{ $currentStatus == 'chưa_giao' ? 'disabled' : '' }} class="fw-bold text-success">
                                    ✅ Đã giao thành công
                                </option>

                                <option value="giao_thất_bại"
                                    {{ $currentStatus == 'giao_thất_bại' ? 'selected' : '' }}
                                    {{ $currentStatus == 'chưa_giao' ? 'disabled' : '' }} class="fw-bold text-danger">
                                    ❌ Giao thất bại
                                </option>
                            </select>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mã vận đơn (Tracking)</label>
                            <input name="MaVanDon" class="form-control" value="{{ old('MaVanDon', $phieu->MaVanDon) }}" placeholder="VD: GHN-123456">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tên Shipper</label>
                            <input name="TenShipper" class="form-control" value="{{ old('TenShipper', $phieu->TenShipper) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea name="GhiChu" class="form-control" rows="1">{{ old('GhiChu', $phieu->GhiChu) }}</textarea>
                        </div>
                    </div>

                    @if(!$isFinished)
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu cập nhật
                    </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</body>

</html>