<?php
// =============================================
// config/database.php
// Kết nối đến cơ sở dữ liệu MySQL bằng MySQLi
// =============================================

// --- Thông tin kết nối ---
// Thay đổi các giá trị này cho phù hợp với máy của bạn
define('DB_HOST', 'localhost');   // Máy chủ database (thường là localhost trên XAMPP)
define('DB_USER', 'root');        // Tên đăng nhập MySQL (mặc định XAMPP là 'root')
define('DB_PASS', '');            // Mật khẩu MySQL (mặc định XAMPP để trống)
define('DB_NAME', 'movie_website'); // Tên database đã tạo

// --- Tạo kết nối ---
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Kiểm tra kết nối ---
// Nếu kết nối thất bại thì dừng chương trình và hiển thị lỗi
if (!$conn) {
    die('<div style="color:red; padding:20px; font-family:sans-serif;">
            <strong>❌ Lỗi kết nối database!</strong><br>
            ' . mysqli_connect_error() . '<br><br>
            <em>Hãy kiểm tra lại thông tin DB_HOST, DB_USER, DB_PASS, DB_NAME trong file config/database.php</em>
         </div>');
}

// --- Thiết lập bộ mã UTF-8 ---
// Giúp hiển thị tiếng Việt đúng định dạng
mysqli_set_charset($conn, 'utf8mb4');
?>
