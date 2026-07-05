<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userSession = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CineBook</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/account/assets/css/style.css">

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand text-danger fw-bold" href="/account/index.php">
        🎬 CINEBOOK
    </a>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-3">
            <li class="nav-item"><a class="nav-link" href="/account/index.php">Trang chủ</a></li>
            <li class="nav-item"><a class="nav-link" href="/account/profile.php">Hồ sơ cá nhân</a></li>
            <li class="nav-item"><a class="nav-link">Phim</a></li>
            <li class="nav-item"><a class="nav-link">Lịch chiếu</a></li>
        </ul>

        <div class="ms-auto d-flex gap-2 align-items-center">
            <input class="form-control form-control-sm" placeholder="Tìm phim...">
            <?php if ($userSession): ?>
                <a href="/account/profile.php"
                   class="user-link btn btn-outline-light btn-sm"
                   title="Xem hồ sơ"
                   aria-label="Xem hồ sơ của <?= htmlspecialchars($userSession['fullname']) ?>">
                    👤 <?= htmlspecialchars($userSession['fullname']) ?>
                </a>
                <a href="/account/logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
            <?php else: ?>
                <a href="/account/login.php" class="btn btn-danger btn-sm">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>