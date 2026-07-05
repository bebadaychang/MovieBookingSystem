<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

function ensureTicketCodeColumn(PDO $pdo): void {
    $check = $pdo->prepare("SHOW COLUMNS FROM bookings LIKE 'ticket_code'");
    $check->execute();

    if ($check->rowCount() === 0) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN ticket_code VARCHAR(30) NULL AFTER status");
    }
}

function fillMissingTicketCodes(PDO $pdo): void {
    $check = $pdo->prepare("SELECT id FROM bookings WHERE ticket_code IS NULL OR ticket_code = ''");
    $check->execute();
    $rows = $check->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return;
    }

    $updateStmt = $pdo->prepare("UPDATE bookings SET ticket_code = ? WHERE id = ?");
    foreach ($rows as $row) {
        $ticketCode = 'TCK' . strtoupper(bin2hex(random_bytes(4)));
        $updateStmt->execute([$ticketCode, (int)$row['id']]);
    }
}

function generateUniqueTicketCode(PDO $pdo): string {
    do {
        $ticketCode = 'TCK' . strtoupper(bin2hex(random_bytes(4)));
        $check = $pdo->prepare("SELECT 1 FROM bookings WHERE ticket_code = ?");
        $check->execute([$ticketCode]);
    } while ($check->fetchColumn());

    return $ticketCode;
}

ensureTicketCodeColumn($pdo);
fillMissingTicketCodes($pdo);

$input = json_decode(file_get_contents('php://input'), true);

$showtime_id = isset($input['showtime_id']) ? (int)$input['showtime_id'] : 0;
$user_id     = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$seats       = $input['seats'] ?? [];

if (!$showtime_id || !$user_id || empty($seats)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu đặt vé.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // ===== 1. CHECK GHẾ ĐÃ BỊ ĐẶT =====
    $seatIds = array_column($seats, 'id');

    $placeholders = implode(',', array_fill(0, count($seatIds), '?'));

    $checkStmt = $pdo->prepare("
        SELECT bd.seat_id
        FROM booking_details bd
        JOIN bookings b ON bd.booking_id = b.id
        WHERE b.showtime_id = ?
        AND b.status IN ('paid','pending')
        AND bd.seat_id IN ($placeholders)
    ");

    $checkStmt->execute(array_merge([$showtime_id], $seatIds));

    if ($checkStmt->fetch()) {
        $pdo->rollBack();
        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => 'Ghế vừa có người đặt. Vui lòng chọn lại.'
        ]);
        exit;
    }

    // ===== 2. TÍNH TIỀN =====
    $total = array_sum(array_column($seats, 'price'));

    // ===== 3. TẠO MÃ VÉ =====
    $ticket_code = generateUniqueTicketCode($pdo);

    // ===== 4. INSERT BOOKING =====
    $stmt = $pdo->prepare("
        INSERT INTO bookings (user_id, showtime_id, total_price, status, ticket_code)
        VALUES (?, ?, ?, 'pending', ?)
    ");

    $stmt->execute([
        $user_id,
        $showtime_id,
        $total,
        $ticket_code
    ]);

    $bookingId = $pdo->lastInsertId();

    // ===== 5. INSERT SEATS =====
    $detailStmt = $pdo->prepare("
        INSERT INTO booking_details (booking_id, seat_id, price)
        VALUES (?, ?, ?)
    ");

    foreach ($seats as $seat) {
        $detailStmt->execute([
            $bookingId,
            $seat['id'],
            $seat['price']
        ]);
    }

    $pdo->commit();

    // ===== 6. RESPONSE =====
    echo json_encode([
        'success' => true,
        'booking_id' => $bookingId,
        'ticket_code' => $ticket_code,
        'total_price' => $total
    ]);

} catch (Exception $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}