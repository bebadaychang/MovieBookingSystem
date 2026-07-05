<?php
// config/db.php
// File kết nối database dùng PDO - import 1 lần vào các file khác

$host = 'localhost';
$dbname = 'movie_website'; // dùng chung database với module trang chủ của nhóm
$user = 'root';
$pass = ''; // XAMPP mặc định MySQL không có password

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );
    // Bật chế độ báo lỗi rõ ràng khi query sai
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Trả kết quả dạng mảng kết hợp (key => value)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}
?>
