<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tạo đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">Tạo đơn hàng</h1>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="post" action="{{ route('orders.store') }}" id="orderForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Khách hàng</label>
                    <select name="MaKH" class="form-select" required>
                        <option value="">-- Chọn KH --</option>
                        @foreach($khachhangs as $k)
                            <option value="{{ $k->MaKH }}">{{ $k->MaKH }} - {{ $k->TenKH }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nhân viên bán</label>
                    <select name="NguoiBan" class="form-select" required>
                        <option value="">-- Chọn NV --</option>
                        @foreach($nhanviens as $n)
                            <option value="{{ $n->MaNV }}">{{ $n->MaNV }} - {{ $n->TenNV }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình thức thanh toán</label>
            <input name="HinhThucTT" class="form-control" value="{{ old('HinhThucTT') ?? 'Tiền mặt' }}">
        </div>

        <h5 class="mt-4 mb-3">Chi tiết sản phẩm</h5>
        <table class="table table-bordered" id="itemsTable">
            <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Tồn kho</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="itemsBody">
            <tr class="item-row">
                <td>
                    <select name="items[0][MaSP]" class="form-select sanpham-select" required>
                        <option value="">-- Chọn SP --</option>
                        @foreach($sanphams as $s)
                            <option value="{{ $s->MaSP }}" data-gia="{{ $s->GiaBan }}" data-tonkho="{{ $s->TonKho }}">{{ $s->MaSP }} - {{ $s->TenSP }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" class="form-control tonkho" readonly></td>
                <td><input type="number" name="items[0][SoLuong]" class="form-control soluong" min="1" required></td>
                <td><input type="number" name="items[0][DonGia]" class="form-control dongia" step="0.01" required></td>
                <td><input type="text" class="form-control thanhtien" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row">Xóa</button></td>
            </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary mb-3" id="addRowBtn">Thêm sản phẩm</button>

        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label>Tổng tiền hàng</label>
                    <input type="text" id="tongTienHang" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label>Thuế VAT (10%)</label>
                    <input type="text" id="tongThueVAT" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label>Chiết khấu</label>
                    <input type="text" id="tongChietKhau" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label><strong>Tổng thanh toán</strong></label>
                    <input type="text" id="tongThanhToan" class="form-control" readonly>
                </div>
            </div>
        </div>

        <button class="btn btn-primary">Tạo đơn</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<script>
    const sanPhams = @json($sanphams);
    let rowCount = 1;

    function updateRow(row) {
        const spSelect = row.querySelector('.sanpham-select');
        const tonKho = row.querySelector('.tonkho');
        const soLuong = row.querySelector('.soluong');
        const donGia = row.querySelector('.dongia');
        const thanhTien = row.querySelector('.thanhtien');

        const selectedOption = spSelect.options[spSelect.selectedIndex];
        tonKho.value = selectedOption.dataset.tonkho || 0;
        donGia.value = selectedOption.dataset.gia || 0;

        const tien = (parseInt(soLuong.value) || 0) * (parseFloat(donGia.value) || 0);
        thanhTien.value = tien.toLocaleString('vi-VN');

        updateTotals();
    }

    function updateTotals() {
        let tongTienHang = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const soluong = parseInt(row.querySelector('.soluong').value) || 0;
            const dongia = parseFloat(row.querySelector('.dongia').value) || 0;
            tongTienHang += soluong * dongia;
        });

        const tongThueVAT = tongTienHang * 0.1;
        const tongChietKhau = 0;
        const tongThanhToan = tongTienHang + tongThueVAT - tongChietKhau;

        document.getElementById('tongTienHang').value = tongTienHang.toLocaleString('vi-VN');
        document.getElementById('tongThueVAT').value = tongThueVAT.toLocaleString('vi-VN');
        document.getElementById('tongChietKhau').value = tongChietKhau.toLocaleString('vi-VN');
        document.getElementById('tongThanhToan').value = tongThanhToan.toLocaleString('vi-VN');
    }

    document.getElementById('addRowBtn').addEventListener('click', function () {
        const tbody = document.getElementById('itemsBody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td>
                <select name="items[${rowCount}][MaSP]" class="form-select sanpham-select" required>
                    <option value="">-- Chọn SP --</option>
                    ${sanPhams.map(s => `<option value="${s.MaSP}" data-gia="${s.GiaBan}" data-tonkho="${s.TonKho}">${s.MaSP} - ${s.TenSP}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" class="form-control tonkho" readonly></td>
            <td><input type="number" name="items[${rowCount}][SoLuong]" class="form-control soluong" min="1" required></td>
            <td><input type="number" name="items[${rowCount}][DonGia]" class="form-control dongia" step="0.01" required></td>
            <td><input type="text" class="form-control thanhtien" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">Xóa</button></td>
        `;
        tbody.appendChild(newRow);
        rowCount++;
        attachRowEvents(newRow);
    });

    function attachRowEvents(row) {
        row.querySelector('.sanpham-select').addEventListener('change', () => updateRow(row));
        row.querySelector('.soluong').addEventListener('input', () => updateRow(row));
        row.querySelector('.dongia').addEventListener('input', () => updateRow(row));
        row.querySelector('.remove-row').addEventListener('click', function () {
            row.remove();
            updateTotals();
        });
    }

    document.querySelectorAll('.item-row').forEach(attachRowEvents);
    updateTotals();
</script>
</body>
</html>
