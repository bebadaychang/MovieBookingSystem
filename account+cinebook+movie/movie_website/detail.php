<?php
// =============================================
// detail.php - TRANG CHI TIẾT PHIM
// =============================================
// Chức năng:
//   1. Nhận ID phim từ URL (?id=...)
//   2. Truy vấn thông tin phim từ database
//   3. Tự động cộng 1 lượt xem mỗi khi trang được mở
//   4. Hiển thị đầy đủ thông tin và player xem phim
// =============================================

require_once 'config/database.php';

// -----------------------------------------------------------
// LẤY ID PHIM TỪ URL
// (int) ép kiểu số nguyên → tránh SQL injection
// -----------------------------------------------------------
$movie_id = (int)($_GET['id'] ?? 0);

// Nếu không có ID hợp lệ → chuyển hướng về trang danh sách
if ($movie_id <= 0) {
    header('Location: movies.php');
    exit;
}

// -----------------------------------------------------------
// TRUY VẤN THÔNG TIN PHIM THEO ID
// -----------------------------------------------------------
$sql = "SELECT * FROM movies WHERE id = $movie_id";
$result = mysqli_query($conn, $sql);
$movie = mysqli_fetch_assoc($result);

// Nếu không tìm thấy phim → chuyển hướng
if (!$movie) {
    header('Location: movies.php');
    exit;
}

// -----------------------------------------------------------
// TĂNG LƯỢT XEM: cộng thêm 1 mỗi lần trang được truy cập
// -----------------------------------------------------------
$sql_update = "UPDATE movies SET views = views + 1 WHERE id = $movie_id";
mysqli_query($conn, $sql_update);
// Cập nhật lại biến $movie để hiển thị số mới
$movie['views'] = $movie['views'] + 1;

// -----------------------------------------------------------
// LẤY PHIM LIÊN QUAN (cùng thể loại, trừ phim hiện tại)
// -----------------------------------------------------------
$safe_cat = mysqli_real_escape_string($conn, $movie['category']);
$sql_related = "SELECT * FROM movies 
                WHERE category = '$safe_cat' AND id != $movie_id
                ORDER BY views DESC LIMIT 4";
$result_related = mysqli_query($conn, $sql_related);

$pageTitle = $movie['title'];
include 'includes/header.php';
?>

<!-- =============================================
     BREADCRUMB
     ============================================= -->
<div class="bg-black border-bottom border-secondary py-3 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-danger">Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="movies.php" class="text-danger">Phim</a></li>
                <li class="breadcrumb-item active text-secondary">
                    <?php echo htmlspecialchars($movie['title']); ?>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">

        <!-- =============================================
             CỘT TRÁI: POSTER PHIM
             ============================================= -->
        <div class="col-md-3 col-sm-5">
            <img src="images/<?php echo htmlspecialchars($movie['image']); ?>"
                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                 class="detail-poster img-fluid"
                 onerror="this.src='images/no-poster.jpg'">
        </div>

        <!-- =============================================
             CỘT PHẢI: THÔNG TIN PHIM
             ============================================= -->
        <div class="col-md-9 col-sm-7">

            <!-- Tên phim -->
            <h1 class="fw-bold text-white mb-3">
                <?php echo htmlspecialchars($movie['title']); ?>
            </h1>

            <!-- Thông tin tóm tắt dạng hàng -->
            <div class="row g-3 mb-4">

                <div class="col-auto">
                    <div class="detail-info-label">Năm phát hành</div>
                    <div class="detail-info-value">
                        <i class="bi bi-calendar3 text-warning me-1"></i>
                        <?php echo $movie['year']; ?>
                    </div>
                </div>

                <div class="col-auto">
                    <div class="detail-info-label">Thể loại</div>
                    <div class="detail-info-value">
                        <!-- Link đến danh sách phim cùng thể loại -->
                        <a href="movies.php?category=<?php echo urlencode($movie['category']); ?>"
                           class="text-danger">
                            <i class="bi bi-grid me-1"></i>
                            <?php echo htmlspecialchars($movie['category']); ?>
                        </a>
                    </div>
                </div>

                <div class="col-auto">
                    <div class="detail-info-label">Lượt xem</div>
                    <div class="detail-info-value">
                        <i class="bi bi-eye text-info me-1"></i>
                        <?php echo number_format($movie['views']); ?>
                    </div>
                </div>

            </div>

            <!-- Nội dung / mô tả phim -->
            <div class="mb-4">
                <h5 class="text-white mb-2">
                    <i class="bi bi-card-text me-2 text-danger"></i> Nội Dung Phim
                </h5>
                <p class="text-secondary" style="line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($movie['description'])); ?>
                </p>
            </div>

            <!-- Nút xem phim - cuộn xuống player -->
            <a href="#movie-player" class="btn btn-danger btn-lg px-5 me-2">
                <i class="bi bi-play-fill me-1"></i> Xem Phim Ngay
            </a>
            <a href="../cinebook/modules/booking/showtimes.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-warning btn-lg px-5 me-2">
                <i class="bi bi-ticket-perforated me-1"></i> Đặt Vé Ngay
            </a>
            <a href="movies.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-1"></i> Quay Lại
            </a>

            <!-- Link xem/viết đánh giá -->
            <div class="mt-3">
                <a href="../cinebook/modules/review/review.php?movie_id=<?php echo $movie['id']; ?>" class="text-danger">
                    <i class="bi bi-star-fill me-1"></i> Xem đánh giá &amp; nhận xét về phim này
                </a>
            </div>

        </div>
    </div>

    <!-- =============================================
         PLAYER XEM PHIM
         ============================================= -->
    <div class="mt-5" id="movie-player">
        <h4 class="section-title mb-3">
            <i class="bi bi-play-circle me-2"></i> Xem Phim
        </h4>

        <?php if ($movie['video']): ?>
        <!-- Nếu video là file .mp4 trong thư mục videos/ -->
        <div class="ratio ratio-16x9 rounded overflow-hidden border border-secondary">
            <video controls autoplay class="w-100"
                   poster="images/<?php echo htmlspecialchars($movie['image']); ?>">
                <source src="videos/<?php echo htmlspecialchars($movie['video']); ?>" type="video/mp4">
                <p class="text-center text-secondary p-4">
                    Trình duyệt của bạn không hỗ trợ phát video.
                    <a href="videos/<?php echo htmlspecialchars($movie['video']); ?>" class="text-danger">
                        Tải xuống tại đây.
                    </a>
                </p>
            </video>
        </div>
        <?php else: ?>
        <!-- Khi chưa có file video -->
        <div class="bg-dark border border-secondary rounded text-center py-5">
            <i class="bi bi-camera-video-off fs-1 text-secondary d-block mb-3"></i>
            <p class="text-secondary mb-0">Video chưa có sẵn cho phim này.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- =============================================
         PHIM LIÊN QUAN
         ============================================= -->
    <?php if (mysqli_num_rows($result_related) > 0): ?>
    <div class="mt-5">
        <h4 class="section-title mb-3">
            <i class="bi bi-collection-play me-2"></i> Phim Liên Quan
        </h4>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php while ($movie = mysqli_fetch_assoc($result_related)): ?>
            <div class="col">
                <?php include 'includes/movie_card.php'; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php
mysqli_close($conn);
include 'includes/footer.php';
?>
