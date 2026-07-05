<?php
require_once __DIR__ . "/bootstrap.php";

checkLogin();
validateSession();

$user = $_SESSION['user'];
?>

<?php include __DIR__ . "/includes/header.php"; ?>

<!-- HERO SECTION -->
<div class="container mt-4 text-white">

    <div class="p-4 rounded bg-dark shadow">

        <h2>🎬 Chào mừng bạn đến CineBook</h2>

        <p>
            Xin chào:
            <b><?= htmlspecialchars($user['fullname']) ?></b>
        </p>

        <p>
            Vai trò:
            <span class="badge bg-danger">
                <?= htmlspecialchars($user['role']) ?>
            </span>
        </p>

        <a href="logout.php" class="btn btn-outline-light btn-sm mt-2">
            Đăng xuất
        </a>

    </div>

    <!-- SEARCH -->
    <div class="mt-4">
        <input type="text" class="form-control form-control-lg"
               placeholder="🔍 Tìm kiếm phim...">
    </div>

    <!-- MOVIE SECTION -->
    <h4 class="mt-4">🎥 Phim đang chiếu</h4>

    <div class="row mt-3">

        <!-- CARD 1 -->
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white movie-card">
                <img src="https://image.tmdb.org/t/p/w500/5Jd5QJjZ.jpg" class="card-img-top">
                <div class="card-body">
                    <h6>The Batman</h6>
                    <span class="text-warning">⭐ 4.8</span>
                </div>
            </div>
        </div>

        <!-- CARD 2 -->
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white movie-card">
                <img src="https://image.tmdb.org/t/p/w500/8Vt6m.jpg" class="card-img-top">
                <div class="card-body">
                    <h6>Avengers</h6>
                    <span class="text-warning">⭐ 4.6</span>
                </div>
            </div>
        </div>

        <!-- CARD 3 -->
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white movie-card">
                <img src="https://image.tmdb.org/t/p/w500/7Wsy.jpg" class="card-img-top">
                <div class="card-body">
                    <h6>Fast X</h6>
                    <span class="text-warning">⭐ 4.4</span>
                </div>
            </div>
        </div>

        <!-- CARD 4 -->
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white movie-card">
                <img src="https://image.tmdb.org/t/p/w500/9Xj.jpg" class="card-img-top">
                <div class="card-body">
                    <h6>John Wick 4</h6>
                    <span class="text-warning">⭐ 4.7</span>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include __DIR__ . "/includes/footer.php"; ?>