<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "movie_website";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

$msg = "";
$is_edit = false;
$edit_user = [
    'fullname' => '', 'username' => '', 'email' => '', 'phone' => '', 'role' => 'user', 'status' => 'active'
];

// KIỂM TRA NẾU LÀ CHẾ ĐỘ SỬA (CÓ THAM SỐ edit_id TRÊN URL)
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// XỬ LÝ KHI SUBMIT FORM (BẤM NÚT LƯU)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if ($is_edit) {
        // Hành động CẬP NHẬT
        if (!empty($_POST['password'])) {
            // Nếu admin nhập mật khẩu mới thì đổi, không thì giữ nguyên
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE `users` SET `fullname`=?, `username`=?, `email`=?, `phone`=?, `password`=?, `role`=?, `status`=? WHERE `id`=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $username, $email, $phone, $password, $role, $status, $_GET['edit_id']]);
        } else {
            $sql = "UPDATE `users` SET `fullname`=?, `username`=?, `email`=?, `phone`=?, `role`=?, `status`=? WHERE `id`=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $username, $email, $phone, $role, $status, $_GET['edit_id']]);
        }
        header("Location: users.php");
        exit();
    } else {
        // Hành động THÊM MỚI
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        try {
            $sql = "INSERT INTO `users` (`fullname`, `username`, `email`, `phone`, `password`, `role`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $username, $email, $phone, $password, $role, $status]);
            header("Location: users.php");
            exit();
        } catch (PDOException $e) {
            $msg = "Lỗi: Username, Email hoặc Số điện thoại đã tồn tại!";
        }
    }
}

include_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $is_edit ? 'Sửa người dùng' : 'Thêm người dùng' ?></title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #060b13; color: #fff; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: #0b111e; border-right: 1px solid #1e293b; padding: 20px; display: flex; flex-direction: column; }
        .sidebar-brand { font-size: 20px; font-weight: bold; color: #ff2a43; margin-bottom: 30px; padding-left: 10px; }
        .sidebar-brand span { color: #fff; }
        .menu-group { list-style: none; display: flex; flex-direction: column; gap: 6px; }
        .menu-item a { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 8px; font-size: 14px; }
        .menu-item.active a { background-color: #1e293b; color: #38bdf8; }
        
        .main-content { flex: 1; background-color: #070c16; padding: 30px; }
        .content-box { background-color: #0b111e; border: 1px solid #1e293b; border-radius: 12px; padding: 30px; max-width: 600px; margin-top: 20px; }
        
        /* CSS FORM */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 8px; }
        .form-control { width: 100%; background: #151f32; border: 1px solid #1e293b; padding: 10px; border-radius: 6px; color: #fff; font-size: 14px; }
        .form-control:focus { border-color: #38bdf8; outline: none; }
        
        .btn-submit { background-color: #ff2a43; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px; }
        .btn-submit:hover { background-color: #e02237; }
        .btn-back { color: #94a3b8; text-decoration: none; margin-left: 15px; font-size: 14px; }
        .alert { background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🎬 CINE<span>BOOK</span></div>
        <ul class="menu-group">
            <li class="menu-item"><a href="dashboard.php">📊 Dashboard</a></li>
            <li class="menu-item"><a href="movies.php">🎬 Quản lý phim</a></li>
            <li class="menu-item active"><a href="users.php">👥 Quản lý người dùng</a></li>
            <li class="menu-item"><a href="#">📅 Lịch chiếu</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2><?= $is_edit ? 'Chỉnh sửa tài khoản' : 'Thêm tài khoản mới' ?></h2>
        
        <div class="content-box">
            <?php if(!empty($msg)): ?>
                <div class="alert"><?= $msg ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" required value="<?= htmlspecialchars($edit_user['fullname']) ?>">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required <?= $is_edit ? 'readonly style="opacity:0.6;"' : '' ?> value="<?= htmlspecialchars($edit_user['username']) ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($edit_user['email']) ?>">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($edit_user['phone']) ?>">
                </div>
                <div class="form-group">
                    <label>Mật khẩu <?= $is_edit ? '(Để trống nếu không muốn đổi)' : '' ?></label>
                    <input type="password" name="password" class="form-control" <?= $is_edit ? '' : 'required' ?>>
                </div>
                <div class="form-group">
                    <label>Vai trò (Role)</label>
                    <select name="role" class="form-control">
                        <option value="user" <?= $edit_user['role'] == 'user' ? 'selected' : '' ?>>User (Khách hàng)</option>
                        <option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin (Quản trị viên)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= $edit_user['status'] == 'active' ? 'selected' : '' ?>>Kích hoạt</option>
                        <option value="locked" <?= $edit_user['status'] == 'locked' ? 'selected' : '' ?>>Khóa tài khoản</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Lưu thông tin</button>
                <a href="users.php" class="btn-back">Hủy bỏ</a>
            </form>
        </div>
    </div>

</body>
</html>