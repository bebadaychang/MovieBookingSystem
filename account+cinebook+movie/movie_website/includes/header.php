<?php require_once dirname(dirname(__DIR__)) . '/account/bootstrap.php';?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tiêu đề trang - biến $pageTitle được set ở mỗi trang -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | CineViet' : 'CineViet'; ?></title>

    <!-- Bootstrap 5 CSS - framework giao diện -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons - bộ icon của Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS tùy chỉnh của dự án -->
    <link href="<?php echo $baseUrl ?? ''; ?>css/style.css" rel="stylesheet">
</head>
<body>

<!-- =============================================
     THANH ĐIỀU HƯỚNG (NAVBAR)
     ============================================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom border-danger">
    <div class="container">

        <!-- Logo website -->
        <a class="navbar-brand fw-bold text-danger fs-4" href="<?php echo $baseUrl ?? ''; ?>index.php">
            <i class="bi bi-play-circle-fill"></i> CineViet
        </a>

        <!-- Nút toggle cho màn hình nhỏ (mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Các mục menu -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active text-danger' : ''; ?>"
                       href="<?php echo $baseUrl ?? ''; ?>index.php">
                        <i class="bi bi-house"></i> Trang Chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'movies.php') ? 'active text-danger' : ''; ?>"
                       href="<?php echo $baseUrl ?? ''; ?>movies.php">
                        <i class="bi bi-film"></i> Phim
                    </a>
                </li>

                <!-- Dropdown Thể loại -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-grid"></i> Thể Loại
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <?php
                        // Danh sách thể loại cố định - có thể lấy từ DB nếu muốn
                        $categories = ['Hành động', 'Tình cảm', 'Kinh dị', 'Hài', 'Hoạt hình', 'Khoa học viễn tưởng'];
                        foreach ($categories as $cat):
                        ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo $baseUrl ?? ''; ?>movies.php?category=<?php echo urlencode($cat); ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>

            <!-- Form tìm kiếm trên navbar -->
            <form class="d-flex me-2" action="<?php echo $baseUrl ?? ''; ?>movies.php" method="GET">
                <input class="form-control form-control-sm bg-secondary text-white border-secondary me-2"
                       type="search" name="search" placeholder="Tìm phim..."
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button class="btn btn-danger btn-sm" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <!-- Nút đăng nhập -->
<?php if (!empty($_SESSION['user'])): ?>
    <a href="../account/profile.php" class="text-white me-2 text-decoration-none" style="font-size:13px; cursor:pointer;">
        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user']['fullname']) ?>
    </a>
    <a href="../account/logout.php" class="btn btn-outline-secondary btn-sm">Đăng xuất</a>
<?php else: ?>
    <a href="../account/login.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-person-circle"></i> Đăng Nhập
    </a>
<?php endif; ?>
        </div>
    </div>
</nav>
<!-- Kết thúc Navbar -->
