<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập nhật đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body >
        @include('admin.navbar')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Cập nhật đơn hàng #{{ $don->MaDon }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('orders.update', $don->MaDon) }}">
                @csrf
                @method('put')

                {{-- LOGIC XÁC ĐỊNH TRẠNG THÁI HỢP LỆ --}}
                @php
                    $flow = ['chờ_xác_nhận', 'đã_xác_nhận', 'đang_giao', 'đã_giao'];
                    $labels = [
                        'chờ_xác_nhận' => '⏳ Chờ xác nhận',
                        'đã_xác_nhận' => '✅ Đã xác nhận',
                        'đang_giao' => '📦 Đang giao',
                        'đã_giao' => '🎉 Đã giao'
                    ];
                    
                    $currentIndex = array_search($don->TrangThai, $flow);
                    $isCancelled = ($don->TrangThai === 'đã_hủy');
                @endphp

                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái đơn hàng</label>
                    
                    {{-- Nếu đã hủy hoặc đã giao thành công, không cho sửa trạng thái nữa --}}
                    @if($isCancelled || $don->TrangThai === 'đã_giao')
                        <div class="alert {{ $isCancelled ? 'alert-danger' : 'alert-success' }}">
                            Đơn hàng hiện tại là: <strong>{{ $isCancelled ? 'ĐÃ HỦY' : 'ĐÃ GIAO THÀNH CÔNG' }}</strong>. 
                            Không thể thay đổi trạng thái.
                        </div>
                        <input type="hidden" name="TrangThai" value="{{ $don->TrangThai }}">
                    @else
                        <select name="TrangThai" class="form-select form-select-lg" required>
                            @foreach($flow as $index => $status)
                                @php
                                    // Logic Disable:
                                    // 1. Disable quá khứ ($index < $currentIndex)
                                    // 2. Disable tương lai xa ($index > $currentIndex + 1) -> Chặn nhảy cóc
                                    $disabled = ($index < $currentIndex) || ($index > $currentIndex + 1);
                                @endphp
                                <option value="{{ $status }}" 
                                    {{ $don->TrangThai == $status ? 'selected' : '' }}
                                    {{ $disabled ? 'disabled' : '' }}
                                    class="{{ $disabled ? 'text-muted bg-light' : 'fw-bold' }}">
                                    {{ $labels[$status] }} 
                                    @if($disabled && $index < $currentIndex) (Đã qua) @endif
                                    @if($disabled && $index > $currentIndex + 1) (Chưa đến) @endif
                                </option>
                            @endforeach

                            {{-- Option Hủy: Chỉ hiện khi chưa giao hàng (index < 2) --}}
                            @if($currentIndex < 2)
                                <option value="đã_hủy" class="text-danger fw-bold">❌ Đã hủy</option>
                            @endif
                        </select>
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle"></i> Bạn chỉ có thể chuyển sang bước tiếp theo hoặc hủy đơn. Không thể quay lại trạng thái cũ.
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Hình thức thanh toán</label>
                    <input name="HinhThucTT" class="form-control" value="{{ old('HinhThucTT', $don->HinhThucTT) }}">
                </div>

                <div class="d-flex gap-2">
                    @if(!$isCancelled && $don->TrangThai !== 'đã_giao')
                        <button class="btn btn-primary px-4">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
