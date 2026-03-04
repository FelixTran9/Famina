<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết đơn hàng {{ $don->MaDon }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS tùy chỉnh cho Stepper */
        .stepper-wrapper {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            position: relative;
        }
        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .stepper-item::before {
            position: absolute;
            content: "";
            border-bottom: 2px solid #ccc;
            width: 100%;
            top: 20px;
            left: -50%;
            z-index: 2;
        }
        .stepper-item::after {
            position: absolute;
            content: "";
            border-bottom: 2px solid #ccc;
            width: 100%;
            top: 20px;
            left: 50%;
            z-index: 2;
        }
        .stepper-item .step-counter {
            position: relative;
            z-index: 5;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ccc;
            margin-bottom: 6px;
            color: #fff;
            font-weight: bold;
        }
        .stepper-item.active {
            font-weight: bold;
        }
        .stepper-item.completed .step-counter,
        .stepper-item.active .step-counter {
            background-color: #0d6efd; /* Bootstrap Primary Color */
        }
        .stepper-item.completed::after {
            position: absolute;
            content: "";
            border-bottom: 2px solid #0d6efd;
            width: 100%;
            top: 20px;
            left: 50%;
            z-index: 3;
        }
        .stepper-item:first-child::before { content: none; }
        .stepper-item:last-child::after { content: none; }
        
        /* Trạng thái Hủy */
        .stepper-item.cancelled .step-counter { background-color: #dc3545; } 
    </style>
</head>
<body class="p-4 bg-light">

<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Đơn hàng #{{ $don->MaDon }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- HIỂN THỊ THÔNG BÁO LỖI HOẶC THÀNH CÔNG --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- THANH TIẾN TRÌNH (STEPPER) --}}
    @php
        $flow = ['chờ_xác_nhận', 'đã_xác_nhận', 'đang_giao', 'đã_giao'];
        $flowLabels = [
            'chờ_xác_nhận' => 'Chờ xác nhận',
            'đã_xác_nhận' => 'Đã xác nhận',
            'đang_giao' => 'Đang giao',
            'đã_giao' => 'Đã giao'
        ];
        
        $currentStatus = $don->TrangThai;
        $currentIndex = array_search($currentStatus, $flow);
        if($currentStatus == 'đã_hủy') $currentIndex = -1; // Case đặc biệt
    @endphp

    <div class="card mb-4">
        <div class="card-body">
            @if($currentStatus === 'đã_hủy')
                <div class="alert alert-danger text-center mb-0">
                    <strong><i class="fas fa-times-circle"></i> ĐƠN HÀNG ĐÃ HỦY</strong>
                </div>
            @else
                <div class="stepper-wrapper">
                    @foreach($flow as $index => $stepKey)
                        @php
                            $isCompleted = $index <= $currentIndex;
                            $isActive = $index == $currentIndex;
                        @endphp
                        <div class="stepper-item {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                            <div class="step-counter">
                                @if($index < $currentIndex)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <div class="step-name">{{ $flowLabels[$stepKey] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- THANH ĐIỀU KHIỂN TRẠNG THÁI --}}
            <div class="d-flex justify-content-end mt-4 gap-2">
                {{-- Nút Hủy: Chỉ hiện khi chưa giao hàng (index < 2) và chưa hủy --}}
                @if($currentIndex !== false && $currentIndex < 2 && $currentStatus !== 'đã_hủy')
                    <form action="{{ route('orders.update', $don->MaDon) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này? Hành động này sẽ hoàn lại kho.')">
                        @csrf @method('PUT')
                        <input type="hidden" name="TrangThai" value="đã_hủy">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Hủy đơn hàng
                        </button>
                    </form>
                @endif

                {{-- Nút Tiến tới bước tiếp theo --}}
                @if($currentIndex !== false && $currentIndex < count($flow) - 1)
                    @php $nextStatus = $flow[$currentIndex + 1]; @endphp
                    <form action="{{ route('orders.update', $don->MaDon) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="TrangThai" value="{{ $nextStatus }}">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            Tiến tới: {{ $flowLabels[$nextStatus] }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold">Thông tin chung</div>
                <div class="card-body">
                    <p class="mb-2"><strong>Mã khách hàng:</strong> {{ $don->MaKH }}</p>
                    <p class="mb-2"><strong>Nhân viên bán:</strong> {{ $don->NguoiBan }}</p>
                    <p class="mb-2"><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($don->NgayDat)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold">Thông tin thanh toán</div>
                <div class="card-body">
                    <p class="mb-2"><strong>Trạng thái:</strong> 
                        @if($don->TrangThai == 'đã_hủy')
                            <span class="badge bg-danger">Đã hủy</span>
                        @elseif($don->TrangThai == 'đã_giao')
                            <span class="badge bg-success">Đã giao</span>
                        @else
                            <span class="badge bg-info text-dark">{{ $flowLabels[$don->TrangThai] ?? $don->TrangThai }}</span>
                        @endif
                    </p>
                    <p class="mb-2"><strong>Hình thức TT:</strong> {{ $don->HinhThucTT }}</p>
                </div>
            </div>
        </div>
    </div>

    <h5>Chi tiết sản phẩm</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
            <tr>
                <th>Mã SP</th>
                <th>Tên Sản Phẩm</th>
                <th class="text-center">Số lượng</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thuế VAT</th>
                <th class="text-end">Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            @foreach($chi_tiet as $ct)
                <tr>
                    <td>{{ $ct->MaSP }}</td>
                    {{-- Giả sử bạn có relationship product trong model ChiTiet hoặc join ở controller --}}
                    <td>{{ $ct->TenSanPham ?? 'Sản phẩm ' . $ct->MaSP }}</td> 
                    <td class="text-center">{{ $ct->SoLuong }}</td>
                    <td class="text-end">{{ number_format($ct->DonGia, 0, ',', '.') }} ₫</td>
                    <td class="text-end">{{ number_format($ct->ThueVAT, 0, ',', '.') }} ₫</td>
                    <td class="text-end fw-bold">{{ number_format($ct->ThanhTien, 0, ',', '.') }} ₫</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="table-group-divider">
                <tr>
                    <td colspan="5" class="text-end">Tổng tiền hàng:</td>
                    <td class="text-end">{{ number_format($don->TongTienHang, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end">Tổng Thuế VAT:</td>
                    <td class="text-end">{{ number_format($don->TongThueVAT, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end">Tổng Chiết khấu:</td>
                    <td class="text-end">-{{ number_format($don->TongChietKhau, 0, ',', '.') }} ₫</td>
                </tr>
                <tr class="bg-light">
                    <td colspan="5" class="text-end fw-bold fs-5">TỔNG THANH TOÁN:</td>
                    <td class="text-end fw-bold fs-5 text-primary">{{ number_format($don->TongThanhToan, 0, ',', '.') }} ₫</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>