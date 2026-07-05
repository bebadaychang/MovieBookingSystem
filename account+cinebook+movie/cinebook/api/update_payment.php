<?php
// api/update_payment.php
// Đổi trạng thái booking từ 'pending' sang 'paid' (demo, chưa nối cổng thanh toán thật)
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$booking_id = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;

if (!$booking_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu booking_id']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'paid' WHERE id = ? AND status = 'pending'");
    $stmt->execute([$booking_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đơn vé không hợp lệ hoặc đã thanh toán.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
