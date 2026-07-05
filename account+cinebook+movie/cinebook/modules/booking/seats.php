<?php
// modules/booking/seats.php
require_once __DIR__ . '/../../config/db.php';

$showtime_id = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : 0;
if (!$showtime_id) {
    die('Thiếu showtime_id. Vui lòng chọn lịch chiếu trước.');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chọn ghế ngồi</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- Thanh nav: Quay về trang chủ -->
<div class="top-nav">
    <a href="/account+cinebook+movie/movie_website/index.php" class="btn-home">← Trang chủ</a>
    <a href="showtimes.php?movie_id=<?= isset($_GET['showtime_id']) ? '' : '' ?>" class="btn-home">← Chọn suất chiếu</a>
    <div class="breadcrumb-nav">Trang chủ / Đặt vé / <span>Chọn ghế</span></div>
</div>

<div class="panel seat-panel">
    <h2 class="panel-title red">CHỌN GHẾ NGỒI</h2>

    <div class="seat-legend">
        <span><i class="box gray"></i> Đã đặt</span>
        <span><i class="box red"></i> Đang chọn</span>
        <span><i class="box white"></i> Ghế trống</span>
        <span><i class="box orange"></i> VIP</span>
    </div>

    <div class="screen-label">MÀN HÌNH</div>
    <div id="seat-map" class="seat-map">
        <p>Đang tải sơ đồ ghế...</p>
    </div>

    <div class="seat-summary">
        <div id="selected-list">Ghế đã chọn: <span>chưa chọn ghế nào</span></div>
        <div id="total-price">Tổng tiền: <strong>0đ</strong></div>
        <button id="btn-continue" class="btn-red" disabled onclick="goToConfirm()">Tiếp tục</button>
    </div>
</div>

<script>
const SHOWTIME_ID = <?= $showtime_id ?>;
</script>
<script src="../../assets/js/seats.js"></script>

</body>
</html>
