<?php
// 1. KẾT NỐI DATABASE
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

// 2. XỬ LÝ XÓA THÀNH VIÊN
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Không cho phép tự xóa chính mình nếu tài khoản đang đăng nhập là admin id = 1
    if ($delete_id != 1) {
        $stmt_del = $conn->prepare("DELETE FROM `users` WHERE `id` = ?");
        $stmt_del->execute([$delete_id]);
        header("Location: users.php");
        exit();
    }
}

// 3. LẤY DANH SÁCH TẤT CẢ NGƯỜI DÙNG
$stmt = $conn->query("SELECT * FROM `users` ORDER BY `id` DESC");
$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. ĐÁNH DẤU MENU ACTIVE ĐỂ SIDEBAR BIẾT ĐANG Ở TRANG NÀO
$active_menu = 'users';

// 5. GỌI HEADER CHUNG (Đã chứa sẵn cấu trúc layout, sidebar, topbar)
include_once 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 18px; font-weight: bold; color: #fff;">Danh sách thành viên</h3>
    <a href="user-create.php" style="background-color: #3b82f6; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: 0.2s;">+ Thêm người dùng</a>
</div>

<div class="content-box" style="background-color: #0b111e; border: 1px solid #1e293b; border-radius: 12px; padding: 20px;">
    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid #1e293b;">
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">ID</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Họ và tên</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Username</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Email</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Số điện thoại</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Vai trò</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Trạng thái</th>
                <th style="padding: 12px 10px; color: #64748b; font-weight: 500;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_users as $user): ?>
            <tr style="border-bottom: 1px solid #0f172a;">
                <td style="padding: 14px 10px; color: #64748b;">#<?= $user['id'] ?></td>
                <td style="padding: 14px 10px; font-weight: 500; color: #fff;"><?= htmlspecialchars($user['fullname']) ?></td>
                <td style="padding: 14px 10px; color: #cbd5e1;"><?= htmlspecialchars($user['username']) ?></td>
                <td style="padding: 14px 10px; color: #cbd5e1;"><?= htmlspecialchars($user['email']) ?></td>
                <td style="padding: 14px 10px; color: #cbd5e1;"><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></td>
                <td style="padding: 14px 10px;">
                    <span class="badge <?= $user['role'] ?>"><?= $user['role'] ?></span>
                </td>
                <td style="padding: 14px 10px;">
                    <span class="badge active" style="color: #10b981; background: rgba(16, 185, 129, 0.2);"><?= $user['status'] ?></span>
                </td>
                <td style="padding: 14px 10px;">
                    <a href="user-create.php?edit_id=<?= $user['id'] ?>" class="btn-action" style="color: #38bdf8; text-decoration: none; margin-right: 10px; font-size: 13px;">Sửa</a>
                    <?php if($user['id'] != 1): ?>
                        <a href="users.php?delete_id=<?= $user['id'] ?>" class="btn-action delete" style="color: #ef4444; text-decoration: none; font-size: 13px;" onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này?')">Xóa</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php 
// 6. GỌI FOOTER CHUNG ĐỂ ĐÓNG THẺ
include_once 'includes/footer.php'; 
?>