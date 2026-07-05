<?php
require_once __DIR__ . '/bootstrap.php';

$user = requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hồ sơ cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #0f0f0f;
            color: white;
        }

        .profile-card {
            background: #1b1b1b;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            margin-top: 60px;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }

        .badge-custom {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
        }

        .role {
            background: #e50914;
            color: white;
        }

        .status {
            background: #28a745;
            color: white;
        }

        input {
            background: #2a2a2a !important;
            border: 1px solid #444 !important;
            color: white !important;
        }

        input:focus {
            box-shadow: none !important;
            border-color: #e50914 !important;
        }

        .btn-red {
            background: #e50914;
            color: white;
        }

        .btn-dark {
            background: #333;
            color: white;
        }

        .hide-role {
            display: none;
        }

        .ticket-code {
            font-weight: bold;
            color: #ffd700;
        }
    </style>
</head>

<body>

<div class="profile-card">

    <h3 class="text-center mb-4">👤 Hồ sơ cá nhân</h3>

    <!-- BADGES -->
    <div class="text-center mb-3">
        <span class="badge-custom role"><?= htmlspecialchars($user['role']) ?></span>
        <span class="badge-custom status"><?= htmlspecialchars($user['status']) ?></span>
    </div>

    <!-- FORM -->
    <form>

        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input class="form-control" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" readonly>
        </div>

        <div class="mb-3 hide-role">
            <label class="form-label">Vai trò</label>
            <input class="form-control" value="<?= htmlspecialchars($user['role'] ?? '') ?>" readonly>
        </div>

        <div class="mb-4">
            <label class="form-label">Vé của tôi</label>

            <?php
            $stmt = getDatabaseConnection()->prepare("
                SELECT b.id, b.ticket_code, b.total_price, b.status, b.created_at,
                       sh.cinema, sh.room, sh.show_date, sh.show_time
                FROM bookings b
                JOIN showtimes sh ON sh.id = b.showtime_id
                WHERE b.user_id = :user_id
                ORDER BY b.id DESC
            ");

            $stmt->execute([':user_id' => $user['id']]);
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (empty($tickets)): ?>
                <div class="alert alert-secondary">Bạn chưa có vé nào.</div>
            <?php else: ?>

                <div class="list-group">

                    <?php foreach ($tickets as $ticket): ?>
                        <div class="list-group-item bg-dark text-white border-secondary mb-2">

                            <div class="d-flex justify-content-between">
                                <span class="ticket-code">
                                    🎫 <?= htmlspecialchars($ticket['ticket_code'] ?? 'NO_CODE') ?>
                                </span>

                                <span class="badge bg-danger">
                                    <?= htmlspecialchars($ticket['status']) ?>
                                </span>
                            </div>

                            <div class="small text-muted mt-2">
                                <?= htmlspecialchars($ticket['cinema'] ?? '-') ?> - 
                                <?= htmlspecialchars($ticket['room'] ?? '-') ?><br>

                                <?= htmlspecialchars($ticket['show_date'] ?? '-') ?> 
                                <?= htmlspecialchars($ticket['show_time'] ?? '-') ?><br>

                                💰 <span style="color:white;">
                                <?= number_format((float)$ticket['total_price'], 0, ',', '.') ?>đ
                            </span>
                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

        <div class="d-flex gap-2 w-100">

            <a href="/movie_website/index.php" class="btn btn-outline-light flex-fill">
                ⬅ Quay lại
            </a>

            <a href="/account/logout.php" class="btn btn-red flex-fill">
                Đăng xuất
            </a>

        </div>

    </form>

</div>

</body>
</html>