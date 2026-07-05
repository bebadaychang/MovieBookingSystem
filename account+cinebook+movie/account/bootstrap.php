<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tự động định nghĩa $baseUrl chuẩn dựa trên thư mục chạy thực tế
$baseUrl = '/account+cinebook+movie/movie_website/';

// Nạp file Database nằm trong thư mục config của account (Kiểm tra xem file Database của bạn nằm ở đâu)
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
} elseif (file_exists(__DIR__ . '/config/db.php')) {
    require_once __DIR__ . '/config/db.php';
}

// Tạo hàm bọc để kết nối Database
if (!function_exists('getDatabaseConnection')) {
    function getDatabaseConnection() {
        if (class_exists('Database')) {
            $db = new Database();
            return $db->connect();
        }
        die("Lỗi: Không tìm thấy Class Database. Hãy kiểm tra lại file chứa Class.");
    }
}