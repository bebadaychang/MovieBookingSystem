<?php
// api/get_seats.php
// Trả về JSON: toàn bộ ghế của phòng + danh sách ghế đã bị đặt cho 1 suất chiếu cụ thể
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$showtime_id = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : 0;

if (!$showtime_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Thiếu showtime_id']);
    exit;
}

// Lấy thông tin suất chiếu kèm tên phim thật từ bảng movies
$stmt = $pdo->prepare("
    SELECT s.*, m.title AS movie_title
    FROM showtimes s
    JOIN movies m ON s.movie_id = m.id
    WHERE s.id = ?
");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

if (!$showtime) {
    http_response_code(404);
    echo json_encode(['error' => 'Không tìm thấy suất chiếu']);
    exit;
}

$room = $showtime['room'];

// Lấy toàn bộ ghế của phòng này
$stmt = $pdo->prepare("SELECT id, row_label, col_number, seat_type FROM seats WHERE room = ? ORDER BY row_label, col_number");
$stmt->execute([$room]);
$allSeats = $stmt->fetchAll();

// Lấy danh sách seat_id đã được đặt (trạng thái paid hoặc pending) cho đúng suất chiếu này
$stmt = $pdo->prepare("
    SELECT bd.seat_id
    FROM booking_details bd
    JOIN bookings b ON bd.booking_id = b.id
    WHERE b.showtime_id = ? AND b.status IN ('paid', 'pending')
");
$stmt->execute([$showtime_id]);
$bookedSeatIds = array_column($stmt->fetchAll(), 'seat_id');

// Gắn trạng thái cho từng ghế
$result = [];
foreach ($allSeats as $seat) {
    $result[] = [
        'id'        => $seat['id'],
        'label'     => $seat['row_label'] . $seat['col_number'], // VD: D6
        'row'       => $seat['row_label'],
        'col'       => (int)$seat['col_number'],
        'type'      => $seat['seat_type'], // normal | vip
        'booked'    => in_array($seat['id'], $bookedSeatIds)
    ];
}

echo json_encode([
    'showtime' => [
        'id' => $showtime['id'],
        'movie_title' => $showtime['movie_title'],
        'cinema' => $showtime['cinema'],
        'room' => $showtime['room'],
        'date' => $showtime['show_date'],
        'time' => $showtime['show_time'],
        'price_normal' => (float)$showtime['price'],
        'price_vip' => (float)$showtime['price'] + 20000  // VIP đắt hơn 20k, tùy chỉnh sau
    ],
    'seats' => $result
]);
