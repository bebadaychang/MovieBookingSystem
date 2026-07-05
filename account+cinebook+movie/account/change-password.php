<?php
require_once __DIR__ . "/bootstrap.php";

checkLogin();

$db = (new Database())->connect();

$user = $_SESSION['user'];

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // kiểm tra confirm
    if ($new_password !== $confirm_password) {
        $error = "Mật khẩu mới không khớp";
    } elseif (strlen($new_password) < 8) {
        $error = "Mật khẩu phải từ 8 ký tự";
    } else {

        // check mật khẩu cũ
        $sql = "SELECT password FROM users WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $user['id']]);
        $db_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($old_password, $db_user['password'])) {
            $error = "Mật khẩu cũ không đúng";
        } else {

            // update password mới
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':password' => password_hash($new_password, PASSWORD_BCRYPT),
                ':id' => $user['id']
            ]);

            $success = "Đổi mật khẩu thành công!";
        }
    }
}
?>

<h2>🔒 ĐỔI MẬT KHẨU</h2>

<?php if ($success) echo "<p style='color:green'>$success</p>"; ?>
<?php if ($error) echo "<p style='color:red'>$error</p>"; ?>

<form method="POST">

    <label>Mật khẩu cũ</label><br>
    <input type="password" name="old_password" required><br><br>

    <label>Mật khẩu mới</label><br>
    <input type="password" name="new_password" required><br><br>

    <label>Nhập lại mật khẩu</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Đổi mật khẩu</button>

</form>

<hr>

<a href="profile.php">👤 Quay lại hồ sơ</a>