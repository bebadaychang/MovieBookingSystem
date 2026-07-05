<?php
// 1. Khởi chạy session để hệ thống nhận diện phiên làm việc hiện tại
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Xóa sạch toàn bộ dữ liệu trong session (Xóa thông tin user đang lưu)
$_SESSION = array();

// 3. Hủy bỏ session hoàn toàn trên hệ thống
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 4. Chuyển hướng người dùng về trang login.php nằm chung thư mục
header("Location: login.php");
exit;