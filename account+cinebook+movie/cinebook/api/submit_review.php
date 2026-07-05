<?php
// api/submit_review.php
// Nhận POST JSON: movie_id, user_id, user_name, rating, comment
// Quy tắc: 1 user chỉ được đánh giá 1 phim 1 lần (có thể sửa lại nếu nhóm muốn cho sửa)

require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

$movie_id  = isset($input['movie_id']) ? (int)$input['movie_id'] : 0;
$user_id   = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$user_name = isset($input['user_name']) ? trim($input['user_name']) : '';
$rating    = isset($input['rating']) ? (int)$input['rating'] : 0;
$comment   = isset($input['comment']) ? trim($input['comment']) : '';

// Validate dữ liệu đầu vào
if (!$movie_id || !$user_id || !$user_name || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu đánh giá không hợp lệ.']);
    exit;
}

try {
    // Kiểm tra user này đã đánh giá phim này chưa
    $checkStmt = $pdo->prepare("SELECT id FROM reviews WHERE movie_id = ? AND user_id = ?");
    $checkStmt->execute([$movie_id, $user_id]);

    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá phim này rồi.']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO reviews (user_id, movie_id, user_name, rating, comment)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $movie_id, $user_name, $rating, $comment]);

    echo json_encode([
        'success' => true,
        'review_id' => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
