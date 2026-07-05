<?php
// modules/booking/confirm.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Kiểm tra đăng nhập - dùng session của module account đồng đội
if (empty($_SESSION['user'])) {
    header("Location: /account/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$showtime_id = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : 0;
if (!$showtime_id) {
    die('Thiếu showtime_id. Vui lòng chọn lại ghế.');
}

// Lấy user_id từ session thật của module đăng nhập
$user_id = (int)$_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xác nhận đặt vé</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- Thanh nav: Quay về trang chủ -->
<div class="top-nav">
    <a href="../../../../../../movie_website/index.php" class="btn-home">← Trang chủ</a>
    <a href="seats.php?showtime_id=<?= $showtime_id ?>" class="btn-home">← Chọn lại ghế</a>
    <div class="breadcrumb-nav">Trang chủ / Đặt vé / <span>Xác nhận</span></div>
</div>

<div class="panel confirm-panel">
    <h2 class="panel-title" style="background:#a855f7;color:#fff;">XÁC NHẬN ĐẶT VÉ</h2>

    <div id="confirm-content">
        <p>Đang tải thông tin vé...</p>
    </div>

    <div id="payment-section" style="display:none; margin-top:16px;">
        <p style="font-size:13px;color:#888;margin-bottom:8px;">Phương thức thanh toán</p>
        <div class="payment-options">
            <button class="pay-method active" data-method="momo">MoMo</button>
            <button class="pay-method" data-method="vnpay">VNPay</button>
            <button class="pay-method" data-method="bank">Thẻ ngân hàng</button>
            <button class="pay-method" data-method="zalopay">ZaloPay</button>
        </div>
        <button id="btn-pay" class="btn-red" onclick="confirmPayment()">Thanh toán</button>
    </div>
</div>

<script>
const SHOWTIME_ID = <?= $showtime_id ?>;
const USER_ID = <?= $user_id ?>;
</script>
<script src="../../assets/js/confirm.js"></script>

</body>
</html>
