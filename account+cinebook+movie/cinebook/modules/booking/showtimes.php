<?php
// modules/booking/showtimes.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

// Lấy movie_id từ URL, ví dụ: showtimes.php?movie_id=1
$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if (!$movie_id) {
    die('Thiếu movie_id. Vui lòng chọn phim trước.');
}

// Lấy thông tin phim từ bảng movies thật (do module trang chủ tạo)
$movieStmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$movieStmt->execute([$movie_id]);
$movie = $movieStmt->fetch();
if (!$movie) {
    die('Không tìm thấy phim.');
}

// Lấy tất cả lịch chiếu của phim này, gom nhóm theo rạp
$stmt = $pdo->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY cinema, show_date, show_time");
$stmt->execute([$movie_id]);
$showtimes = $stmt->fetchAll();

// Gom nhóm theo rạp để hiển thị giống thiết kế (CGV Vincom, Galaxy...)
$grouped = [];
foreach ($showtimes as $st) {
    $grouped[$st['cinema']][] = $st;
}

// Lấy danh sách ngày duy nhất để hiển thị tab ngày
$dates = array_unique(array_column($showtimes, 'show_date'));
sort($dates);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chọn lịch chiếu</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- Thanh nav: Quay về trang chủ -->
<div class="top-nav" style="justify-content:space-between;">
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="/account+cinebook+movie/movie_website/index.php" class="btn-home">← Trang chủ</a>
        <a href="/account+cinebook+movie/movie_website/movies.php?id=<?= $movie_id ?>" class="btn-home">← Chi tiết phim</a>
        <div class="breadcrumb-nav">Trang chủ / <span><?= htmlspecialchars($movie['title'] ?? '') ?></span> / Chọn lịch chiếu</div>
    </div>
</div>
    <!-- Trạng thái đăng nhập -->
    <div>
        <?php if (!empty($_SESSION['user'])): ?>
            <a href="/cinebook/profile.php" style="color:#ccc;font-size:13px;text-decoration:none;">👤 <?= htmlspecialchars($_SESSION['user']['fullname']) ?></a>
            <a href="/account/logout.php" class="btn-home" style="margin-left:8px;">Đăng xuất</a>
        <?php else: ?>
            <a href="/account/login.php" class="btn-home" style="background:#ef4444;color:#fff;border-color:#ef4444;">Đăng nhập</a>
        <?php endif; ?>
    </div>
</div>

<div class="panel showtime-panel">
    <h2 class="panel-title orange">CHỌN LỊCH CHIẾU</h2>
    <h3 style="margin-bottom:12px;"><?= htmlspecialchars($movie['title']) ?></h3>

    <!-- Tab chọn ngày -->
    <div class="date-tabs">
        <?php foreach ($dates as $i => $d):
            $dayName = date('D', strtotime($d));
            $dayNum = date('d/m', strtotime($d));
        ?>
            <button class="date-tab <?= $i === 0 ? 'active' : '' ?>" data-date="<?= $d ?>">
                <?= $i === 0 ? 'Hôm nay' : $dayName ?><br><?= $dayNum ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Danh sách rạp + giờ chiếu -->
    <div class="cinema-list">
        <?php foreach ($grouped as $cinema => $sessions): ?>
            <div class="cinema-block">
                <h3 class="cinema-name"><?= htmlspecialchars($cinema) ?></h3>
                <div class="format-row">
                    <span class="format-tag"><?= htmlspecialchars($sessions[0]['format']) ?></span>
                    <div class="time-slots">
                        <?php foreach ($sessions as $s): ?>
                            <button
                                class="time-slot"
                                data-showtime-id="<?= $s['id'] ?>"
                                data-date="<?= $s['show_date'] ?>"
                                onclick="selectShowtime(<?= $s['id'] ?>, this)"
                            >
                                <?= date('H:i', strtotime($s['show_time'])) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($grouped)): ?>
            <p>Hiện chưa có lịch chiếu cho phim này.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function selectShowtime(showtimeId, btn) {
    // Bỏ active các nút khác, active nút vừa chọn
    document.querySelectorAll('.time-slot').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Chuyển sang bước chọn ghế, mang theo showtime_id
    window.location.href = '../booking/seats.php?showtime_id=' + showtimeId;
}
</script>

</body>
</html>
