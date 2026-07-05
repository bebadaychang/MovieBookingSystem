<?php
// =============================================
// movies.php - TRANG DANH SÁCH PHIM
// =============================================
// Chức năng:
//   1. Nhận tham số tìm kiếm (search) và lọc (category, sort) từ URL
//   2. Xây dựng câu SQL động dựa trên tham số
//   3. Truy vấn và hiển thị danh sách phim
// =============================================

require_once 'config/database.php';
$pageTitle = 'Danh Sách Phim';

// -----------------------------------------------------------
// LẤY THAM SỐ LỌC TỪ URL (GET parameters)
// htmlspecialchars: ngăn chặn tấn công XSS
// trim: xóa khoảng trắng thừa
// -----------------------------------------------------------
$search   = trim($_GET['search']   ?? '');   // Tìm kiếm theo tên
$category = trim($_GET['category'] ?? '');   // Lọc theo thể loại
$sort     = trim($_GET['sort']     ?? '');   // Sắp xếp (mặc định: mới nhất)

// -----------------------------------------------------------
// XÂY DỰNG CÂU SQL ĐỘNG
// Thêm điều kiện WHERE tùy theo tham số người dùng gửi lên
// mysqli_real_escape_string: thoát ký tự đặc biệt, chống SQL Injection
// -----------------------------------------------------------
$where_conditions = []; // Mảng lưu các điều kiện WHERE

// Điều kiện tìm kiếm theo tên (dùng LIKE để tìm một phần tên)
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "title LIKE '%$safe_search%'";
}

// Điều kiện lọc theo thể loại (khớp chính xác)
if ($category !== '') {
    $safe_category = mysqli_real_escape_string($conn, $category);
    $where_conditions[] = "category = '$safe_category'";
}

// Ghép điều kiện: nếu có điều kiện thì thêm WHERE, không thì bỏ qua
$where_sql = '';
if (!empty($where_conditions)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Xác định cách sắp xếp
$order_sql = ($sort === 'views') ? 'ORDER BY views DESC' : 'ORDER BY id DESC';

// Câu SQL hoàn chỉnh
$sql = "SELECT * FROM movies $where_sql $order_sql";
$result = mysqli_query($conn, $sql);
$total_movies = mysqli_num_rows($result); // Đếm số phim tìm được

// -----------------------------------------------------------
// Lấy danh sách thể loại riêng biệt để hiển thị filter
// DISTINCT = chỉ lấy giá trị không trùng
// -----------------------------------------------------------
$sql_cats = "SELECT DISTINCT category FROM movies ORDER BY category";
$result_cats = mysqli_query($conn, $sql_cats);

include 'includes/header.php';
?>

<!-- =============================================
     PAGE HEADER
     ============================================= -->
<div class="bg-black border-bottom border-secondary py-4 mb-4">
    <div class="container">
        <h1 class="mb-1 fw-bold">
            <i class="bi bi-film text-danger me-2"></i> Danh Sách Phim
        </h1>
        <!-- Breadcrumb điều hướng -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-danger">Trang Chủ</a></li>
                <li class="breadcrumb-item active text-secondary">Phim</li>
                <?php if ($category): ?>
                <li class="breadcrumb-item active text-secondary">
                    <?php echo htmlspecialchars($category); ?>
                </li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">

    <!-- =============================================
         BỘ LỌC - TÌM KIẾM
         ============================================= -->
    <div class="filter-bar mb-4">
        <form method="GET" action="movies.php">
            <div class="row g-3 align-items-end">

                <!-- Ô tìm kiếm theo tên -->
                <div class="col-md-5">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-search"></i> Tìm kiếm theo tên phim
                    </label>
                    <input type="text" name="search" class="form-control bg-dark text-white border-secondary"
                           placeholder="Nhập tên phim..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <!-- Dropdown lọc theo thể loại -->
                <div class="col-md-3">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-grid"></i> Thể loại
                    </label>
                    <select name="category" class="form-select bg-dark text-white border-secondary">
                        <option value="">-- Tất cả thể loại --</option>
                        <?php while ($cat = mysqli_fetch_assoc($result_cats)): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                            <?php echo ($category === $cat['category']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Dropdown sắp xếp -->
                <div class="col-md-2">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-sort-down"></i> Sắp xếp
                    </label>
                    <select name="sort" class="form-select bg-dark text-white border-secondary">
                        <option value="" <?php echo ($sort === '') ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="views" <?php echo ($sort === 'views') ? 'selected' : ''; ?>>Xem nhiều nhất</option>
                    </select>
                </div>

                <!-- Nút tìm kiếm và xóa lọc -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                    <?php if ($search || $category || $sort): ?>
                    <a href="movies.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>

    <!-- Hiển thị số kết quả -->
    <p class="text-secondary small mb-3">
        Tìm thấy <strong class="text-white"><?php echo $total_movies; ?></strong> phim
        <?php if ($search): ?>
            với từ khóa "<strong class="text-danger"><?php echo htmlspecialchars($search); ?></strong>"
        <?php endif; ?>
        <?php if ($category): ?>
            trong thể loại "<strong class="text-warning"><?php echo htmlspecialchars($category); ?></strong>"
        <?php endif; ?>
    </p>

    <!-- =============================================
         DANH SÁCH PHIM - DẠNG BẢNG + CARD
         ============================================= -->
    <?php if ($total_movies > 0): ?>

    <!-- Grid hiển thị phim -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
        <?php while ($movie = mysqli_fetch_assoc($result)): ?>
        <div class="col">

            <!-- Card phim với đầy đủ thông tin -->
            <div class="movie-card h-100">

                <!-- Poster -->
                <div class="poster-wrapper">
                    <img src="images/<?php echo htmlspecialchars($movie['image']); ?>"
                         alt="<?php echo htmlspecialchars($movie['title']); ?>"
                         loading="lazy"
                         onerror="this.src='images/no-poster.jpg'">
                    <div class="poster-overlay">
                        <a href="detail.php?id=<?php echo $movie['id']; ?>" class="btn btn-danger btn-sm px-3">
                            <i class="bi bi-play-fill"></i> Xem
                        </a>
                    </div>
                </div>

                <!-- Thông tin phim -->
                <div class="card-body p-2">

                    <!-- Tên phim -->
                    <div class="card-title mb-1" title="<?php echo htmlspecialchars($movie['title']); ?>">
                        <?php echo htmlspecialchars($movie['title']); ?>
                    </div>

                    <!-- Mô tả ngắn (2 dòng) -->
                    <p class="card-meta mb-2" style="
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        font-size: 0.75rem;
                        line-height: 1.4;
                    ">
                        <?php echo htmlspecialchars($movie['description']); ?>
                    </p>

                    <!-- Meta: năm, thể loại -->
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="card-meta">
                            <i class="bi bi-calendar3 me-1"></i><?php echo $movie['year']; ?>
                        </span>
                        <span class="category-badge"><?php echo htmlspecialchars($movie['category']); ?></span>
                    </div>

                    <!-- Lượt xem -->
                    <div class="card-meta mb-2">
                        <i class="bi bi-eye me-1"></i><?php echo number_format($movie['views']); ?> lượt xem
                    </div>

                    <!-- Nút xem chi tiết -->
                    <a href="detail.php?id=<?php echo $movie['id']; ?>"
                       class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-info-circle me-1"></i> Xem Chi Tiết
                    </a>

                </div>
            </div>

        </div>
        <?php endwhile; ?>
    </div>

    <?php else: ?>
    <!-- Trạng thái rỗng khi không tìm thấy phim -->
    <div class="empty-state">
        <i class="bi bi-search"></i>
        <h5 class="text-white mb-2">Không tìm thấy phim nào</h5>
        <p class="text-secondary">Thử tìm kiếm với từ khóa khác hoặc chọn thể loại khác.</p>
        <a href="movies.php" class="btn btn-outline-danger mt-2">
            <i class="bi bi-arrow-left"></i> Xem tất cả phim
        </a>
    </div>
    <?php endif; ?>

</div>

<?php
mysqli_close($conn);
include 'includes/footer.php';
?>
