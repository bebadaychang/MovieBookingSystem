<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #060b13; color: #fff; display: flex; min-height: 100vh; overflow-x: hidden; }
        
        /* SIDEBAR CHUNG */
        .sidebar { width: 260px; background-color: #0b111e; border-right: 1px solid #1e293b; padding: 20px; display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .sidebar-brand { display: flex; align-items: center; gap: 8px; font-size: 20px; font-weight: bold; color: #ff2a43; margin-bottom: 30px; padding-left: 10px; }
        .sidebar-brand span { color: #fff; }
        .menu-group { list-style: none; display: flex; flex-direction: column; gap: 6px; }
        .menu-item a { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
        .menu-item.active a, .menu-item a:hover { background-color: #1e293b; color: #38bdf8; font-weight: 500; }
        .logout-btn { margin-top: auto; padding-top: 15px; }

        /* PHẦN NỘI DUNG CHÍNH (Đẩy lề sang phải 260px do Sidebar cố định) */
        .main-content { flex: 1; background-color: #070c16; padding: 30px; margin-left: 260px; display: flex; flex-direction: column; gap: 25px; min-height: 100vh; }
        
        .top-header { display: flex; justify-content: space-between; align-items: center; }
        .top-header h2 { font-size: 22px; font-weight: 600; color: #fff; }
        .admin-profile { display: flex; align-items: center; gap: 10px; background: #0b111e; padding: 6px 16px; border-radius: 20px; border: 1px solid #1e293b; font-size: 14px; }
        .admin-avatar { width: 24px; height: 24px; background: #38bdf8; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🎬 CINE<span>BOOK</span></div>
        <ul class="menu-group">
            <li class="menu-item <?= ($active_menu ?? '') == 'dashboard' ? 'active' : '' ?>"><a href="dashboard.php">📊 Dashboard</a></li>
            <li class="menu-item <?= ($active_menu ?? '') == 'movies' ? 'active' : '' ?>"><a href="movies.php">🎬 Quản lý phim</a></li>
            <li class="menu-item <?= ($active_menu ?? '') == 'users' ? 'active' : '' ?>"><a href="users.php">👥 Quản lý người dùng</a></li>
            <li class="menu-item"><a href="#">📅 Lịch chiếu</a></li>
            <li class="menu-item"><a href="#">🎟️ Quản lý vé</a></li>
            
            <li class="menu-item logout-btn"><a href="../logout.php" style="color: #ef4444;">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-header">
            <h2>Quản lý Hệ thống</h2>
            <div class="admin-profile">
                <span class="admin-avatar"></span>
                <span>Admin ▼</span>
            </div>
        </div>