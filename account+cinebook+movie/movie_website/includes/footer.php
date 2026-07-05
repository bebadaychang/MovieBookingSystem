<!-- =============================================
     FOOTER - PHẦN CHÂN TRANG
     ============================================= -->
<footer class="bg-dark text-secondary border-top border-secondary mt-5 pt-4 pb-3">
    <div class="container">
        <div class="row gy-3">

            <!-- Cột 1: Thông tin website -->
            <div class="col-md-4">
                <h5 class="text-danger fw-bold">
                    <i class="bi bi-play-circle-fill"></i> CineViet
                </h5>
                <p class="small">Website xem phim trực tuyến miễn phí với hàng nghìn bộ phim chất lượng cao.</p>
            </div>

            <!-- Cột 2: Liên kết nhanh -->
            <div class="col-md-4">
                <h6 class="text-white">Liên Kết Nhanh</h6>
                <ul class="list-unstyled small">
                    <li><a href="index.php" class="text-secondary text-decoration-none">
                        <i class="bi bi-chevron-right"></i> Trang Chủ
                    </a></li>
                    <li><a href="movies.php" class="text-secondary text-decoration-none">
                        <i class="bi bi-chevron-right"></i> Danh Sách Phim
                    </a></li>
                </ul>
            </div>

            <!-- Cột 3: Thể loại phổ biến -->
            <div class="col-md-4">
                <h6 class="text-white">Thể Loại</h6>
                <div class="d-flex flex-wrap gap-1">
                    <?php
                    $cats = ['Hành động', 'Tình cảm', 'Kinh dị', 'Hài', 'Hoạt hình'];
                    foreach ($cats as $c):
                    ?>
                    <a href="movies.php?category=<?php echo urlencode($c); ?>"
                       class="badge bg-secondary text-decoration-none">
                        <?php echo htmlspecialchars($c); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Dòng bản quyền -->
        <hr class="border-secondary">
        <p class="text-center small mb-0">
            &copy; <?php echo date('Y'); ?> CineViet &mdash; Bài tập Lập trình Web &mdash; Sinh viên CNTT
        </p>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle (bao gồm Popper.js cho dropdown, modal...) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
