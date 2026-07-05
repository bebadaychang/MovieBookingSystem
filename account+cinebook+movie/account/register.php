<?php
require_once __DIR__ . '/bootstrap.php';

$db = getDatabaseConnection();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // VALIDATE
    if ($fullname === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
        $error = "Vui lòng điền đầy đủ thông tin.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ.";
    }
    elseif (strlen($password) < 8) {
        $error = "Mật khẩu phải có ít nhất 8 ký tự.";
    }
    elseif (!preg_match('/[A-Z]/', $password)
        || !preg_match('/[a-z]/', $password)
        || !preg_match('/[0-9]/', $password)
        || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $error = "Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.";
    }
    elseif ($password !== $confirm) {
        $error = "Xác nhận mật khẩu không khớp.";
    }
    else {

        // Kiểm tra tồn tại trước khi INSERT để đưa thông báo cụ thể
        $conditions = [];
        $params = [];
        if ($username !== '') {
            $conditions[] = 'username = :u';
            $params[':u'] = $username;
        }
        if ($email !== '') {
            $conditions[] = 'email = :e';
            $params[':e'] = $email;
        }
        if ($phone !== '') {
            $conditions[] = 'phone = :p';
            $params[':p'] = $phone;
        }

        if ($conditions) {
            $sql = 'SELECT username, email, phone FROM users WHERE ' . implode(' OR ', $conditions) . ' LIMIT 1';
            $check = $db->prepare($sql);
            $check->execute($params);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if (!empty($existing['username']) && $existing['username'] === $username) {
                    $error = "Username đã tồn tại";
                } elseif (!empty($existing['email']) && $existing['email'] === $email) {
                    $error = "Email đã tồn tại";
                } elseif (!empty($existing['phone']) && $existing['phone'] === $phone) {
                    $error = "Số điện thoại đã tồn tại";
                } else {
                    $error = "Dữ liệu đã tồn tại";
                }
            }
        }

        if (!$error) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $db->prepare(
                "INSERT INTO users (fullname, username, email, phone, password, role, status) VALUES (:fullname, :username, :email, :phone, :password, 'user', 'active')"
            );

            try {
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':username' => $username,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':password' => $hashedPassword
                ]);

                $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay.";

            } catch (PDOException $e) {
                // Không để fatal error: xử lý duplicate (SQLSTATE 23000)
                $sqlState = $e->getCode();
                $errorInfo = $e->errorInfo ?? null;

                if ($sqlState === '23000' || (is_array($errorInfo) && ($errorInfo[0] ?? '') === '23000')) {
                    $msg = strtolower($errorInfo[2] ?? $e->getMessage());

                    if (strpos($msg, 'username') !== false) {
                        $error = "Username đã tồn tại";
                    } elseif (strpos($msg, 'email') !== false) {
                        $error = "Email đã tồn tại";
                    } elseif (strpos($msg, 'phone') !== false || strpos($msg, 'sdt') !== false) {
                        $error = "Số điện thoại đã tồn tại";
                    } else {
                        $error = "Dữ liệu đã tồn tại (duplicate).";
                    }
                } else {
                    $error = "Lỗi hệ thống. Vui lòng thử lại sau.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineViet - Đăng ký tài khoản</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* CSS căn giữa & thiết lập nền tối chuẩn rạp phim */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0b111e !important;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .register-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 15px;
            margin: auto;
        }

        .card-cinebook {
            background: #131a26;
            border: 1px solid #222f43;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .text-red {
            color: #e50914 !important;
        }

        .btn-red {
            background: #e50914;
            color: white;
            font-weight: 600;
            border: none;
            padding: 11px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-red:hover {
            background: #b20710;
            color: white;
            transform: translateY(-1px);
        }

        .form-control {
            background: #1a2333 !important;
            color: #ffffff !important;
            border: 1px solid #2c3b54 !important;
            border-radius: 8px !important;
            padding: 11px 15px;
        }

        .form-control::placeholder {
            color: #6c7a93 !important;
            opacity: 1;
        }

        .form-control:focus {
            background: #1d293d !important;
            border-color: #e50914 !important;
            box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25) !important;
        }

        .register-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        .login-text {
            color: #6c7a93;
            font-size: 14px;
        }

        .login-text a {
            color: #e50914;
            text-decoration: none;
            font-weight: 500;
        }

        .login-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="register-wrapper">
    <div class="card-cinebook">

        <h2 class="text-center mb-1 text-red fw-bold text-uppercase" style="letter-spacing: 1px;">🎬 CineViet</h2>
        <h4 class="text-center mb-4 register-title text-uppercase">Đăng ký</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 text-center border-0 small mb-3" style="background-color: rgba(229, 9, 20, 0.15); color: #ff4d5a; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success py-2 text-center border-0 small mb-3" style="background-color: rgba(25, 135, 84, 0.15); color: #2ea869; border-radius: 8px;">
                <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($success) ?>
            </div>
            <div class="text-center mb-3">
                <a href="login.php" class="btn btn-outline-light btn-sm w-100 border-secondary" style="border-radius: 8px;">👉 Đi tới đăng nhập ngay</a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input class="form-control" name="fullname" placeholder="Họ và tên" required value="<?= htmlspecialchars($fullname ?? '') ?>">
            </div>

            <div class="mb-3">
                <input class="form-control" name="username" placeholder="Tên đăng nhập (Username)" required value="<?= htmlspecialchars($username ?? '') ?>">
            </div>

            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Địa chỉ Email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>

            <div class="mb-3">
                <input type="tel" class="form-control" name="phone" placeholder="Số điện thoại" required value="<?= htmlspecialchars($phone ?? '') ?>">
            </div>

            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu (Tối thiểu 8 ký tự)" required>
            </div>

            <div class="mb-4">
                <input type="password" class="form-control" name="confirm" placeholder="Xác nhận lại mật khẩu" required>
            </div>

            <button class="btn btn-red w-100 text-uppercase">Đăng ký tài khoản</button>
        </form>

        <p class="text-center login-text mt-4 mb-0">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </p>

    </div>
</div>

</body>
</html>