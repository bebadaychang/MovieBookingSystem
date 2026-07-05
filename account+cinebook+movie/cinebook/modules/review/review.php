<?php
// modules/review/review.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';

$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if (!$movie_id) {
    die('Thiếu movie_id.');
}

// Lấy tên phim từ bảng movies thật
$stmt = $pdo->prepare("SELECT title FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();
$movieTitle = $movie ? $movie['title'] : 'Phim';

// Lấy thông tin user từ session thật
// Nếu chưa đăng nhập thì vẫn cho xem đánh giá, nhưng không cho gửi
$user_id = !empty($_SESSION['user']) ? (int)$_SESSION['user']['id'] : 0;
$user_name = !empty($_SESSION['user']) ? $_SESSION['user']['fullname'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đánh giá phim</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- Thanh nav: Quay về trang chủ -->
<div class="top-nav" style="max-width:700px;">
    <a href="/account+cinebook+movie/movie_website/index.php" class="btn-home">← Trang chủ</a>
    <a href="/account+cinebook+movie/movie_website/movies.php?id=<?= $movie_id ?>" class="btn-home">← Chi tiết phim</a>
    <div class="breadcrumb-nav">Trang chủ / <span><?= htmlspecialchars($movieTitle) ?></span> / Đánh giá</div>
</div>

<div class="panel review-panel" style="max-width:600px;">
    <h2 class="panel-title" style="background:#ec4899;color:#fff;">ĐÁNH GIÁ PHIM</h2>
    <h3><?= htmlspecialchars($movieTitle) ?></h3>

    <div class="review-stats" style="display:flex; gap:24px; margin:16px 0;">
        <div class="rating-summary">
            <div id="avg-score" style="font-size:32px; font-weight:bold;">--</div>
            <div class="review-stars" id="avg-stars">★★★★★</div>
            <div id="total-count" style="color:#888; font-size:12px;">0 đánh giá</div>
        </div>
        <div id="distribution-bars" style="flex:1;">
            <!-- JS sẽ render 5 thanh % vào đây -->
        </div>
    </div>

    <!-- Form viết đánh giá -->
    <div class="write-review" style="border-top:1px solid #333; padding-top:16px;">
        <p style="margin-bottom:8px;">Viết đánh giá của bạn</p>
        <div id="star-input" style="font-size:24px; cursor:pointer; margin-bottom:10px;">
            <span data-star="1">☆</span><span data-star="2">☆</span><span data-star="3">☆</span><span data-star="4">☆</span><span data-star="5">☆</span>
        </div>
        <textarea id="comment-input" placeholder="Nhận xét của bạn..." style="width:100%; height:70px; background:#0d1117; color:#fff; border:1px solid #333; border-radius:6px; padding:8px;"></textarea>
        <button class="btn-red" onclick="submitReview()" style="margin-top:8px;">Gửi đánh giá</button>
        <p id="review-msg" style="font-size:12px; margin-top:6px;"></p>
    </div>

    <!-- Danh sách đánh giá -->
    <div id="review-list" style="margin-top:20px;">
        <p>Đang tải đánh giá...</p>
    </div>
</div>

<script>
const MOVIE_ID = <?= $movie_id ?>;
const USER_ID = <?= $user_id ?>;
const USER_NAME = "<?= htmlspecialchars($user_name) ?>";
</script>
<script src="../../assets/js/review.js"></script>

</body>
</html>
