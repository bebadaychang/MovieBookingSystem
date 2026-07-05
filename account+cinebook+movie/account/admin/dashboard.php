<?php
// 1. KẾT NỐI DATABASE (Sử dụng đúng cơ sở dữ liệu `movie_website` của bạn)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "movie_website";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

// 2. LOGIC LẤY DỮ LIỆU THỰC TẾ ĐỂ ĐỔ VÀO KHUNG THỐNG KÊ
$total_movies = $conn->query("SELECT COUNT(*) FROM `movies`")->fetchColumn();
$total_users = $conn->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'user'")->fetchColumn();
$total_bookings = $conn->query("SELECT COUNT(*) FROM `bookings` WHERE `status` = 'paid'")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(`total_price`) FROM `bookings` WHERE `status` = 'paid'")->fetchColumn() ?? 0;

// 3. LẤY DỮ LIỆU CHO BẢNG "VÉ BÁN GẦN ĐÂY" (JOIN CÁC BẢNG THEO SQL CỦA BẠN)
$query_recent = "SELECT b.ticket_code, m.title AS movie_title, u.fullname, b.created_at, b.total_price 
                 FROM bookings b
                 JOIN showtimes s ON b.showtime_id = s.id
                 JOIN movies m ON s.movie_id = m.id
                 JOIN users u ON b.user_id = u.id
                 WHERE b.status = 'paid'
                 ORDER BY b.created_at DESC 
                 LIMIT 5";
$stmt_recent = $conn->query($query_recent);
$recent_bookings = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

// Gọi các file bổ trợ của bạn nếu cần, nếu không có thể để trống hoặc dùng style đồng bộ dưới này
include_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CineBook - Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #060b13; color: #fff; display: flex; min-height: 100vh; overflow-x: hidden; }
        
        /* SIDEBAR (Cột menu bên trái giống ảnh mẫu) */
        .sidebar { width: 260px; background-color: #0b111e; border-right: 1px solid #1e293b; padding: 20px; display: flex; flex-direction: column; }
        .sidebar-brand { display: flex; align-items: center; gap: 8px; font-size: 20px; font-weight: bold; color: #ff2a43; margin-bottom: 30px; padding-left: 10px; }
        .sidebar-brand span { color: #fff; }
        .menu-group { list-style: none; display: flex; flex-direction: column; gap: 6px; }
        .menu-item a { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
        .menu-item.active a, .menu-item a:hover { background-color: #1e293b; color: #38bdf8; font-weight: 500; }
        .menu-item a .icon { font-size: 16px; width: 20px; }
        .logout-btn { margin-top: auto; border-top: 1px solid #1e293b; padding-top: 15px; }

        /* PHẦN CHỨA NỘI DUNG CHÍNH (Bên phải) */
        .main-content { flex: 1; background-color: #070c16; padding: 30px; display: flex; flex-direction: column; gap: 25px; }
        
        /* Topbar Header admin */
        .top-header { display: flex; justify-content: space-between; align-items: center; }
        .top-header h2 { font-size: 22px; font-weight: 600; color: #fff; }
        .admin-profile { display: flex; align-items: center; gap: 10px; background: #0b111e; padding: 6px 16px; border-radius: 20px; border: 1px solid #1e293b; font-size: 14px; }
        .admin-avatar { width: 24px; height: 24px; background: #38bdf8; border-radius: 50%; display: inline-block; }

        /* BỐ CỤC KHỐI THỐNG KÊ (4 ô hiển thị) */
        .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .stat-card { background-color: #0b111e; border: 1px solid #1e293b; border-radius: 10px; padding: 20px; display: flex; justify-content: space-between; align-items: flex-start; position: relative; }
        .stat-info .stat-label { font-size: 13px; color: #64748b; text-transform: uppercase; font-weight: 500; }
        .stat-info .stat-value { font-size: 28px; font-weight: bold; margin-top: 8px; color: #f8fafc; }
        .stat-icon { background: rgba(30, 41, 59, 0.5); padding: 10px; border-radius: 8px; color: #ff2a43; font-size: 18px; }
        /* Đổi màu riêng cho ô Doanh Thu giống ảnh mẫu */
        .stat-card.revenue .stat-value { color: #10b981; }

        /* KHU VỰC CHI TIẾT BÊN DƯỚI (Chia 2 cột: Bảng vé & Biểu đồ giả lập) */
        .details-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        .content-box { background-color: #0b111e; border: 1px solid #1e293b; border-radius: 12px; padding: 20px; }
        .box-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #1e293b; }
        .box-title { font-size: 15px; font-weight: 600; color: #f8fafc; }
        .box-link { color: #38bdf8; text-decoration: none; font-size: 12px; }

        /* Bảng danh sách */
        .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
        .data-table th { padding: 12px 10px; color: #64748b; font-weight: 500; border-bottom: 1px solid #1e293b; }
        .data-table td { padding: 14px 10px; color: #cbd5e1; border-bottom: 1px solid #0f172a; }
        .data-table tr:last-child td { border-bottom: none; }
        .ticket-code { color: #64748b; }
        .price-text { color: #ff2a43; font-weight: bold; text-align: right; }

        /* Biểu đồ tròn giả lập CSS (Tỷ lệ vé phim) */
        .chart-container { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; position: relative; }
        .pie-chart { width: 130px; height: 130px; border-radius: 50%; background: conic-gradient(#ff2a43 0% 40%, #3b82f6 40% 70%, #eab308 70% 85%, #a855f7 85% 100%); }
        .chart-legends { width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 15px; font-size: 11px; color: #94a3b8; }
        .legend-item { display: flex; align-items: center; gap: 6px; }
        .color-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            🎬 CINE<span>BOOK</span>
        </div>
        <ul class="menu-group">
            <li class="menu-item active"><a href="dashboard.php"><span class="icon">📊</span> Dashboard</a></li>
            <li class="menu-item"><a href="movies.php"><span class="icon">🎬</span> Quản lý phim</a></li>
            <li class="menu-item"><a href="users.php"><span class="icon">👥</span> Quản lý người dùng</a></li>
            <li class="menu-item"><a href="#"><span class="icon">📅</span> Quản lý lịch chiếu</a></li>
            <li class="menu-item"><a href="#"><span class="icon">🎟️</span> Quản lý vé</a></li>
            <li class="menu-item"><a href="#"><span class="icon">⭐</span> Quản lý đánh giá</a></li>
            <li class="menu-item"><a href="#"><span class="icon">📈</span> Thống kê</a></li>
            <li class="menu-item logout-btn"><a href="../logout.php" style="color: #ef4444;"><span class="icon">🚪</span> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        
        <div class="top-header">
            <h2>Dashboard</h2>
            <div class="admin-profile">
                <span class="admin-avatar"></span>
                <span>Admin ▼</span>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Phim</div>
                    <div class="stat-value"><?= number_format($total_movies) ?></div>
                </div>
                <div class="stat-icon">🎬</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Người dùng</div>
                    <div class="stat-value"><?= number_format($total_users) ?></div>
                </div>
                <div class="stat-icon">👥</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Vé đã bán</div>
                    <div class="stat-value"><?= number_format($total_bookings) ?></div>
                </div>
                <div class="stat-icon">🎟️</div>
            </div>
            <div class="stat-card revenue">
                <div class="stat-info">
                    <div class="stat-label">Doanh thu</div>
                    <div class="stat-value"><?= number_format($total_revenue, 0, ',', '.') ?>đ</div>
                </div>
                <div class="stat-icon" style="color: #10b981;">💰</div>
            </div>
        </div>

        <div class="details-grid">
            
            <div class="content-box">
                <div class="box-header">
                    <div class="box-title">Vé bán gần đây</div>
                    <a href="#" class="box-link">Xem tất cả &raquo;</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã vé</th>
                            <th>Phim</th>
                            <th>Người dùng</th>
                            <th>Ngày</th>
                            <th style="text-align: right;">Số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_bookings) > 0): ?>
                            <?php foreach ($recent_bookings as $row): ?>
                                <tr>
                                    <td class="ticket-code">#<?= htmlspecialchars($row['ticket_code']) ?></td>
                                    <td style="font-weight: 500; color: #fff;"><?= htmlspecialchars($row['movie_title']) ?></td>
                                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td class="price-text"><?= number_format($row['total_price'], 0, ',', '.') ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">Không có giao dịch nào gần đây.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="content-box">
                <div class="box-header">
                    <div class="box-title">Tỷ lệ vé phim</div>
                </div>
                <div class="chart-container">
                    <div class="pie-chart"></div>
                    <div class="chart-legends">
                        <div class="legend-item"><span class="color-dot" style="background:#ff2a43;"></span> Avengers</div>
                        <div class="legend-item"><span class="color-dot" style="background:#3b82f6;"></span> Spider-Man</div>
                        <div class="legend-item"><span class="color-dot" style="background:#eab308;"></span> Doraemon</div>
                        <div class="legend-item"><span class="color-dot" style="background:#a855f7;"></span> Khác</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>

<?php 
include_once 'includes/footer.php'; 
?>