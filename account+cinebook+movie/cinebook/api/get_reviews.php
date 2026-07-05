<?php
// api/get_reviews.php
// Trả về: điểm trung bình, phân bố sao (5★ chiếm bao nhiêu %...), danh sách đánh giá
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if (!$movie_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Thiếu movie_id']);
    exit;
}

// Lấy toàn bộ đánh giá của phim, mới nhất trước
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE movie_id = ? ORDER BY created_at DESC");
$stmt->execute([$movie_id]);
$reviews = $stmt->fetchAll();

$total = count($reviews);

// Tính điểm trung bình + phân bố theo từng mức sao (1-5)
$avg = 0;
$distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

if ($total > 0) {
    $sum = 0;
    foreach ($reviews as $r) {
        $sum += $r['rating'];
        $distribution[(int)$r['rating']]++;
    }
    $avg = round($sum / $total, 1);
}

// Đổi số lượng thành phần trăm để vẽ thanh ngang giống thiết kế
$percentages = [];
foreach ($distribution as $star => $count) {
    $percentages[$star] = $total > 0 ? round(($count / $total) * 100) : 0;
}

echo json_encode([
    'average' => $avg,
    'total' => $total,
    'distribution' => $percentages,
    'reviews' => $reviews
]);
