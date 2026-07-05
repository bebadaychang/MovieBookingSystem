<?php
// modules/booking/success.php
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if (!$booking_id) die('Thiếu booking_id');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt vé thành công</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- Thanh nav: Quay về trang chủ -->
<div class="top-nav">
    <a href="/account+cinebook+movie/movie_website/index.php" class="btn-home">← Trang chủ</a>
    <div class="breadcrumb-nav">Trang chủ / Đặt vé / <span>Đặt vé thành công</span></div>
</div>

<div class="panel">
    <h2 class="panel-title" style="background:#22c55e;color:#000;">ĐẶT VÉ THÀNH CÔNG</h2>
    <div id="ticket-content"><p>Đang tải vé...</p></div>
</div>

<script>
fetch('../../api/get_booking.php?booking_id=<?= $booking_id ?>')
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            document.getElementById('ticket-content').innerHTML = `<p>${data.error}</p>`;
            return;
        }
        const b = data.booking;
        document.getElementById('ticket-content').innerHTML = `
            <h3>${b.movie_title}</h3>
            <p>${b.cinema} - ${b.room}</p>
            <p>${b.show_date} | ${b.show_time.substring(0,5)} | ${b.format}</p>
            <p>Ghế: ${data.seats.join(', ')}</p>
            <p style="color:#ef4444;font-size:18px;">${Number(b.total_price).toLocaleString('vi-VN')}đ</p>
            <p style="color:#22c55e;">✓ Trạng thái: ${b.status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán'}</p>
        `;
    });
</script>

</body>
</html>
