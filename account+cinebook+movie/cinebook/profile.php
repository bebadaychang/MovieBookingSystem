<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

if (empty($_SESSION['user'])) {
    header('Location: /account/login.php?redirect=' . urlencode('/cinebook/profile.php'));
    exit;
}

$user = $_SESSION['user'];

$stmt = $pdo->prepare(
    "SELECT b.id, b.ticket_code, b.total_price, b.status, b.created_at, m.title AS movie_title, sh.cinema, sh.room, sh.show_date, sh.show_time, sh.format,
            (SELECT COUNT(*) FROM booking_details bd WHERE bd.booking_id = b.id) AS seat_count
     FROM bookings b
     JOIN showtimes sh ON sh.id = b.showtime_id
     LEFT JOIN movies m ON m.id = sh.movie_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
);
$stmt->execute([$user['id']]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ người dùng</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="top-nav" style="justify-content:space-between; max-width:900px;">
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <a href="/movie_website/index.php" class="btn-home">← Trang chủ</a>
        <a href="/cinebook/modules/booking/showtimes.php?movie_id=1" class="btn-home">Đặt vé</a>
        <div class="breadcrumb-nav">Trang chủ / <span>Hồ sơ</span></div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="/account/logout.php" class="btn-home">Đăng xuất</a>
    </div>
</div>

<div class="panel profile-panel" style="max-width:900px;">
    <h2 class="panel-title orange">HỒ SƠ NGƯỜI DÙNG</h2>

    <div class="profile-grid">
        <div class="profile-card">
            <h3>Thông tin tài khoản</h3>
            <div class="profile-item"><strong>Họ tên:</strong> <span><?= htmlspecialchars($user['fullname'] ?? '') ?></span></div>
            <div class="profile-item"><strong>Tên đăng nhập:</strong> <span><?= htmlspecialchars($user['username'] ?? '') ?></span></div>
            <div class="profile-item"><strong>Email:</strong> <span><?= htmlspecialchars($user['email'] ?? '') ?></span></div>
            <div class="profile-item"><strong>Điện thoại:</strong> <span><?= htmlspecialchars($user['phone'] ?? '') ?></span></div>
            <div class="profile-item"><strong>Vai trò:</strong> <span><?= htmlspecialchars(ucfirst($user['role'] ?? 'user')) ?></span></div>
        </div>

        <div class="profile-card">
            <h3>Vé của tôi</h3>
            <?php if (empty($tickets)): ?>
                <p class="ticket-empty">Bạn chưa đặt vé nào.</p>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $statusMap = [
                        'paid' => 'Đã thanh toán',
                        'pending' => 'Đang chờ',
                        'cancelled' => 'Đã hủy'
                    ];
                    $statusText = $statusMap[$ticket['status']] ?? $ticket['status'];
                    ?>
                    <div class="ticket-item">
                        <div class="ticket-top">
                            <strong><?= htmlspecialchars($ticket['ticket_code'] ?? 'Chưa có mã vé') ?> - <?= htmlspecialchars($ticket['movie_title'] ?? 'Phim') ?></strong>
                            <span class="ticket-status <?= htmlspecialchars($ticket['status']) ?>"><?= htmlspecialchars($statusText) ?></span>
                        </div>
                        <div class="ticket-meta">
                            <span>Rạp: <?= htmlspecialchars($ticket['cinema'] ?? '-') ?></span>
                            <span>Phòng: <?= htmlspecialchars($ticket['room'] ?? '-') ?></span>
                        </div>
                        <div class="ticket-meta">
                            <span>Ngày: <?= htmlspecialchars($ticket['show_date'] ?? '-') ?></span>
                            <span>Giờ: <?= htmlspecialchars($ticket['show_time'] ?? '-') ?></span>
                        </div>
                        <div class="ticket-meta">
                            <span>Định dạng: <?= htmlspecialchars($ticket['format'] ?? '-') ?></span>
                            <span>Số ghế: <?= (int)$ticket['seat_count'] ?></span>
                        </div>
                        <div class="ticket-meta">
                            <span>Tổng tiền: <?= number_format((float)$ticket['total_price'], 0, ',', '.') ?>đ</span>
                            <span>Ngày đặt: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($ticket['created_at']))) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
