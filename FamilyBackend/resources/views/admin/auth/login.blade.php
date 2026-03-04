<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Admin - FamilyMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: white;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .login-header p {
            color: #999;
            font-size: 14px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .role-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            transition: all 0.3s;
        }
        .role-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <h1>FamilyMart Admin</h1>
        <p>Quản lý cửa hàng</p>
    </div>

    @if($errors->any())
        <div class="error-message">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ url('/admin/login') }}">
        @csrf

        <div class="role-selector">
            <button type="button" class="role-btn active" data-role="truong">
                Trưởng cửa hàng
            </button>
            <button type="button" class="role-btn" data-role="troly">
                Trợ lý cửa hàng
            </button>
        </div>

        <input type="hidden" id="role" name="role" value="truong">

        <div class="mb-3">
            <label class="form-label">Tài khoản</label>
            <input type="text" name="account" class="form-control" value="{{ old('account') }}" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login">Đăng nhập</button>
    </form>

    <hr class="my-4">
    <p style="text-align: center; color: #999; font-size: 12px;">
        Phiên bản: {{ config('app.version', '1.0') }}
    </p>
</div>

<script>
    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('role').value = this.dataset.role;
        });
    });
</script>
</body>
</html>
