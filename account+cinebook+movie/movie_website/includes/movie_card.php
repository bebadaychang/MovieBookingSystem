<?php
// =============================================
// includes/movie_card.php
// PARTIAL: Card hiển thị 1 phim
// =============================================
// Cách dùng: include file này trong vòng lặp while
// Biến $movie phải có sẵn từ vòng lặp bên ngoài
// =============================================
?>
<div class="movie-card">

    <!-- Poster phim -->
    <div class="poster-wrapper">
        <img src="images/<?php echo htmlspecialchars($movie['image']); ?>"
             alt="<?php echo htmlspecialchars($movie['title']); ?>"
             loading="lazy"
             onerror="this.src='images/no-poster.jpg'">

        <!-- Overlay xuất hiện khi hover -->
        <div class="poster-overlay">
            <a href="detail.php?id=<?php echo $movie['id']; ?>"
               class="btn btn-danger btn-sm px-3">
                <i class="bi bi-play-fill me-1"></i> Xem Phim
            </a>
        </div>
    </div>

    <!-- Thông tin bên dưới poster -->
    <div class="card-body">
        <!-- Tên phim -->
        <div class="card-title mb-1">
            <?php echo htmlspecialchars($movie['title']); ?>
        </div>

        <!-- Năm và thể loại -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="card-meta">
                <i class="bi bi-calendar3 me-1"></i><?php echo $movie['year']; ?>
            </span>
            <span class="category-badge"><?php echo htmlspecialchars($movie['category']); ?></span>
        </div>

        <!-- Lượt xem -->
        <div class="card-meta mt-1">
            <i class="bi bi-eye me-1"></i>
            <?php echo number_format($movie['views']); ?> lượt xem
        </div>
    </div>

</div>
