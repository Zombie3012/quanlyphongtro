<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Nhà Tôi</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            
            /* Cấu hình ảnh nền login.png */
            background: url("{{ asset('images/login.png') }}") no-repeat center center fixed;
            background-size: cover; /* Phủ kín màn hình */
            
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #e2e8f0;
            position: relative;
        }

        /* Lớp phủ tối (Overlay) giúp làm mờ ảnh nền và nổi bật form đăng nhập */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7); /* Tông màu đêm tối mờ 70% */
            z-index: 1;
        }

        .login-box {
            position: relative;
            z-index: 2; /* Đảm bảo form nằm trên lớp phủ */
            width: 420px;
            max-width: calc(100vw - 32px);
            /* Hiệu ứng kính mờ (Glassmorphism) */
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: clamp(22px, 6vw, 35px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        /* Biểu tượng ngôi nhà ấm áp */
        .house-icon {
            font-size: 42px;
            color: #f59e0b; /* Ánh đèn vàng */
            background: rgba(245, 158, 11, 0.12);
            width: 75px;
            height: 75px;
            line-height: 75px;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 12px;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);
        }

        .login-box h2 {
            margin: 0 0 5px 0;
            color: #f8fafc;
            font-size: 24px;
            font-weight: 600;
        }

        .login-subtitle {
            font-size: 14px;
            color: #94a3b8;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #cbd5e1;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid #334155;
            border-radius: 8px;
            font-size: 15px;
            color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #f59e0b;
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        }

        .error {
            color: #f87171;
            font-size: 13px;
            margin-top: 6px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        @media (max-width: 520px) {
            body {
                padding: 16px 0;
            }

            .login-box {
                width: calc(100vw - 32px);
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="login-header">
        <div class="house-icon">
            <i class="bi bi-house-heart-fill"></i>
        </div>
        <h2>NHÀ TÔI</h2>
        <p class="login-subtitle">Hệ thống quản lý phòng trọ thân thuộc</p>
    </div>

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="form-group">
            <label for="username">Tên đăng nhập</label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Nhập tài khoản của bạn..."
                required
                autofocus
            >

            @error('username')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                required
            >

            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-1"></i> Mở cửa vào nhà
        </button>
    </form>

</div>

</body>
</html>
