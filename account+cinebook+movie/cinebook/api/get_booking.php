<?php
// api/get_booking.php
// Trả về chi tiết 1 booking: phim, suất chiếu, ghế, tổng tiền
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if (!$booking_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Thiếu booking_id']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT b.id, b.ticket_code, b.total_price, b.status, b.created_at,
           m.title AS movie_title, s.cinema, s.room, s.format, s.show_date, s.show_time
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.id
    JOIN movies m ON s.movie_id = m.id
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    http_response_code(404);
    echo json_encode(['error' => 'Không tìm thấy đơn đặt vé']);
    exit;
}

$seatStmt = $pdo->prepare("
    SELECT s.row_label, s.col_number
    FROM booking_details bd
    JOIN seats s ON bd.seat_id = s.id
    WHERE bd.booking_id = ?
    ORDER BY s.row_label, s.col_number
");
$seatStmt->execute([$booking_id]);
$seatRows = $seatStmt->fetchAll();
$seatLabels = array_map(fn($s) => $s['row_label'] . $s['col_number'], $seatRows);

echo json_encode([
    'booking' => $booking,
    'seats' => $seatLabels
]);
