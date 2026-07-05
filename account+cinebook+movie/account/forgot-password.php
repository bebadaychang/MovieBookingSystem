<?php
require_once __DIR__ . "/bootstrap.php";

$db = getDatabaseConnection();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($email === '' || $newPassword === '' || $confirmPassword === '') {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } elseif (strlen($newPassword) < 8) {
        $error = "Mật khẩu mới phải có ít nhất 8 ký tự.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = "Email không tồn tại trong hệ thống.";
        } else {
            $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $update = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute([
                ':password' => $hashed,
                ':id' => $user['id']
            ]);

            $success = "Đặt lại mật khẩu thành công. Bạn có thể đăng nhập ngay.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width:450px">
    <h2 class="text-center mb-4">QUÊN MẬT KHẨU</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" class="form-control mb-2" placeholder="Email đăng ký" required>
        <input type="password" name="new_password" class="form-control mb-2" placeholder="Mật khẩu mới" required>
        <input type="password" name="confirm_password" class="form-control mb-2" placeholder="Nhập lại mật khẩu mới" required>
        <button class="btn btn-danger w-100">Đặt lại mật khẩu</button>
    </form>

    <p class="text-center mt-3">
        <a href="login.php">Quay lại đăng nhập</a>
    </p>
</div>

</body>
</html>
