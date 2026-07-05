<?php
// =============================================
// index.php - TRANG CHỦ
// =============================================
// Chức năng:
//   1. Kết nối database
//   2. Lấy phim nổi bật nhất (banner)
//   3. Lấy 8 phim mới nhất (theo ID giảm dần)
//   4. Lấy 8 phim có lượt xem cao nhất
//   5. Hiển thị ra trang chủ
// =============================================

// Kết nối database (file config/database.php đã định nghĩa $conn)
require_once 'config/database.php';

$pageTitle = 'Trang Chủ';

// -----------------------------------------------------------
// TRUY VẤN 1: Lấy 1 phim nổi bật nhất cho banner
// Sắp xếp theo lượt xem giảm dần, lấy phim đầu tiên
// -----------------------------------------------------------
$sql_banner = "SELECT * FROM movies ORDER BY views DESC LIMIT 1";
$result_banner = mysqli_query($conn, $sql_banner);
$banner_movie = mysqli_fetch_assoc($result_banner); // Lấy 1 dòng kết quả

// -----------------------------------------------------------
// TRUY VẤN 2: Lấy 8 phim mới nhất
// Sắp xếp theo ID giảm dần (ID lớn hơn = thêm sau = mới hơn)
// -----------------------------------------------------------
$sql_new = "SELECT * FROM movies ORDER BY id DESC LIMIT 8";
$result_new = mysqli_query($conn, $sql_new);

// -----------------------------------------------------------
// TRUY VẤN 3: Lấy 8 phim nổi bật (lượt xem cao nhất)
// -----------------------------------------------------------
$sql_popular = "SELECT * FROM movies ORDER BY views DESC LIMIT 8";
$result_popular = mysqli_query($conn, $sql_popular);

// -----------------------------------------------------------
// Bắt đầu hiển thị HTML
// -----------------------------------------------------------
include 'includes/header.php';
?>

<!-- =============================================
     BANNER PHIM NỔI BẬT
     ============================================= -->
<?php if ($banner_movie): ?>
<section class="hero-banner no-image mb-5">
    <div class="container">
        <div class="row align-items-center">

            <!-- Nội dung text bên trái -->
            <div class="col-lg-6 col-md-8 py-4">

                <!-- Badge thể loại -->
                <span class="badge badge-category mb-3 px-3 py-2">
                    <i class="bi bi-film me-1"></i>
                    <?php echo htmlspecialchars($banner_movie['category']); ?>
                </span>

                <!-- Tên phim -->
                <h1 class="display-5 fw-bold text-white mb-3">
                    <?php echo htmlspecialchars($banner_movie['title']); ?>
                </h1>

                <!-- Mô tả ngắn (giới hạn 150 ký tự) -->
                <p class="text-secondary mb-4" style="font-size: 1rem; line-height: 1.7;">
                    <?php echo htmlspecialchars(mb_substr($banner_movie['description'], 0, 150)) . '...'; ?>
                </p>

                <!-- Thông tin phụ: năm, lượt xem -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="text-warning">
                        <i class="bi bi-calendar3"></i> <?php echo $banner_movie['year']; ?>
                    </span>
                    <span class="text-info">
                        <i class="bi bi-eye"></i> <?php echo number_format($banner_movie['views']); ?> lượt xem
                    </span>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex gap-3 flex-wrap">
                    <!-- Nút Xem Ngay: đi đến trang chi tiết phim -->
                    <a href="detail.php?id=<?php echo $banner_movie['id']; ?>"
                       class="btn btn-danger btn-lg px-4">
                        <i class="bi bi-play-fill me-1"></i> Xem Ngay
                    </a>
                    <!-- Nút Xem Thêm Info: scrolls xuống danh sách -->
                    <a href="movies.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-grid me-1"></i> Tất Cả Phim
                    </a>
                </div>
            </div>

            <!-- Poster phim bên phải -->
            <div class="col-lg-3 col-md-4 d-none d-md-block ms-auto">
                <img src="images/<?php echo htmlspecialchars($banner_movie['image']); ?>"
                     alt="<?php echo htmlspecialchars($banner_movie['title']); ?>"
                     class="img-fluid rounded shadow-lg"
                     style="max-height: 380px; width: auto; display: block; margin: 0 auto;"
                     onerror="this.src='images/no-poster.jpg'">
            </div>

        </div>
    </div>
</section>
<?php endif; ?>


<!-- =============================================
     PHIM MỚI NHẤT
     ============================================= -->
<section class="container mb-5">

    <!-- Tiêu đề section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">
            <i class="bi bi-stars me-2 text-warning"></i> Phim Mới Nhất
        </h2>
        <a href="movies.php" class="btn btn-outline-danger btn-sm">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <!-- Grid hiển thị phim dạng card (4 cột trên desktop, 2 cột trên mobile) -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <?php
        // Vòng lặp: hiển thị từng phim trong kết quả truy vấn
        while ($movie = mysqli_fetch_assoc($result_new)):
        ?>
        <div class="col">
            <!-- Gọi partial card phim -->
            <?php include 'includes/movie_card.php'; ?>
        </div>
        <?php endwhile; ?>
    </div>

</section>


<!-- =============================================
     PHIM NỔI BẬT (LƯỢT XEM CAO NHẤT)
     ============================================= -->
<section class="container mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">
            <i class="bi bi-fire me-2 text-danger"></i> Phim Nổi Bật
        </h2>
        <a href="movies.php?sort=views" class="btn btn-outline-danger btn-sm">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <?php while ($movie = mysqli_fetch_assoc($result_popular)): ?>
        <div class="col">
            <?php include 'includes/movie_card.php'; ?>
        </div>
        <?php endwhile; ?>
    </div>

</section>

<?php
// Đóng kết nối database (tốt nhất nên đóng khi không dùng nữa)
mysqli_close($conn);

include 'includes/footer.php';
?>
