<?php
require_once __DIR__ . '/bootstrap.php';

$db = getDatabaseConnection();

$error = "";

// Rate limit settings
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCK_SECONDS', 300); // 5 minutes

/* =========================
   XỬ LÝ LOGIN
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = $_POST['login'];
    $password = $_POST['password'];

    // IP và user key
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userKey = strtolower(trim((string)$login));

    // init session store for rate limiting
    if (!isset($_SESSION['login_rate'])) {
        $_SESSION['login_rate'] = ['by_ip' => [], 'by_user' => []];
    }

    if (!isset($_SESSION['login_rate']['by_ip'][$ip])) {
        $_SESSION['login_rate']['by_ip'][$ip] = ['attempts' => 0, 'first_time' => time(), 'lock_until' => 0];
    }
    if (!isset($_SESSION['login_rate']['by_user'][$userKey])) {
        $_SESSION['login_rate']['by_user'][$userKey] = ['attempts' => 0, 'first_time' => time(), 'lock_until' => 0];
    }

    // Check session-based locks (IP or username)
    $now = time();
    $ipEntry = &$_SESSION['login_rate']['by_ip'][$ip];
    $userEntry = &$_SESSION['login_rate']['by_user'][$userKey];

    if (!empty($ipEntry['lock_until']) && $ipEntry['lock_until'] > $now) {
        $remain = ceil(($ipEntry['lock_until'] - $now) / 60);
        $error = "Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$remain} phút";
    }
    if (!$error && !empty($userEntry['lock_until']) && $userEntry['lock_until'] > $now) {
        $remain = ceil(($userEntry['lock_until'] - $now) / 60);
        $error = "Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$remain} phút";
    }

    // tìm user (lấy UNIX timestamp của lock_time từ MySQL để tránh sai lệch timezone)
    $sql = "SELECT *, UNIX_TIMESTAMP(lock_time) AS lock_ts FROM users WHERE username = :login OR email = :login LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* =========================
        CHECK USER TỒN TẠI
    ========================= */
    if ($user) {

        /* =========================
            CHECK LOCK ACCOUNT (RATE LIMIT)
        ========================= */
        if (!empty($user['lock_ts'])) {
            $lockTime = (int)$user['lock_ts'];

            if ($now < $lockTime + LOGIN_LOCK_SECONDS) {
                $remainSeconds = ($lockTime + LOGIN_LOCK_SECONDS) - $now;
                $remain = (int) ceil($remainSeconds / 60);
                $error = "Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$remain} phút";
            } else {
                // mở khóa lại
                $sql = "UPDATE users 
                        SET login_attempts = 0, lock_time = NULL 
                        WHERE id = :id";

                $stmt = $db->prepare($sql);
                $stmt->execute([':id' => $user['id']]);
            }
        }

        /* =========================
            LOGIN SUCCESS
        ========================= */
        if (password_verify($password, $user['password']) && empty($error)) {

            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'username' => $user['username'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role' => $user['role'],
                'status' => $user['status']
            ];

            /* =========================
                RESET LOGIN ATTEMPTS
            ========================= */
            $sql = "UPDATE users 
                    SET login_attempts = 0 
                    WHERE id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $user['id']]);

            // reset session counters for this IP and username
            $_SESSION['login_rate']['by_ip'][$ip] = ['attempts' => 0, 'first_time' => time(), 'lock_until' => 0];
            $_SESSION['login_rate']['by_user'][$userKey] = ['attempts' => 0, 'first_time' => time(), 'lock_until' => 0];

            /* =========================
                LOGIN LOG
            ========================= */
            $ip = $_SERVER['REMOTE_ADDR'];
            $browser = $_SERVER['HTTP_USER_AGENT'];

            $sql = "INSERT INTO login_logs(user_id, ip_address, browser)
                    VALUES(:user_id, :ip, :browser)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $user['id'],
                ':ip' => $ip,
                ':browser' => $browser
            ]);

            /* =========================
                REDIRECT ROLE
            ========================= */
            if ($user['role'] === 'admin') {
                header("Location: /account+cinebook+movie/account/admin/dashboard.php");
            } else {
                header("Location: /account+cinebook+movie/movie_website/index.php");
            }

            exit;
        }

        /* =========================
            LOGIN FAIL → TĂNG COUNT
        ========================= */
        if (!password_verify($password, $user['password'])) {

            // Increment DB counter safely
            $sql = "UPDATE users SET login_attempts = login_attempts + 1 WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $user['id']]);

            // Fetch updated attempts and lock_time
            $sql = "SELECT login_attempts, lock_time FROM users WHERE id = :id LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $user['id']]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            $attempts = (int)($u['login_attempts'] ?? 0);

            // If reached limit, set lock_time (if not already)
            if ($attempts >= LOGIN_MAX_ATTEMPTS) {
                if (empty($u['lock_time'])) {
                    $sql = "UPDATE users SET lock_time = NOW() WHERE id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':id' => $user['id']]);
                }

                // Message for 5th failed attempt
                $error = "Tài khoản đã bị khóa 5 phút";

            } else {
                // Message showing remaining attempts
                $remaining = LOGIN_MAX_ATTEMPTS - $attempts;
                $error = "Sai mật khẩu. Bạn còn {$remaining} lần thử";
            }

            // Update session counters for IP and username (keep additional protection)
            $ipEntry['attempts'] = ($ipEntry['attempts'] ?? 0) + 1;
            $userEntry['attempts'] = ($userEntry['attempts'] ?? 0) + 1;
            if ($ipEntry['attempts'] >= LOGIN_MAX_ATTEMPTS) {
                $ipEntry['lock_until'] = time() + LOGIN_LOCK_SECONDS;
            }
            if ($userEntry['attempts'] >= LOGIN_MAX_ATTEMPTS) {
                $userEntry['lock_until'] = time() + LOGIN_LOCK_SECONDS;
            }

            // progressive delay to slow brute-force
            $attemptsSession = max($ipEntry['attempts'], $userEntry['attempts']);
            $delay = (int) min(pow(2, max(0, $attemptsSession - 1)), 8);
            if ($delay > 0) {
                $sleep($delay);
            }
        }

    } else {
        // user không tồn tại — vẫn tăng bộ đếm session theo IP và username để chống brute-force
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userKey = strtolower(trim((string)$login));

        if (!isset($_SESSION['login_rate'])) {
            $_SESSION['login_rate'] = ['by_ip' => [], 'by_user' => []];
        }
        if (!isset($_SESSION['login_rate']['by_ip'][$ip])) {
            $_SESSION['login_rate']['by_ip'][$ip] = ['attempts' => 0, 'first_time' => time(), 'lock_until' => 0];
        }
        if (!isset($_SESSION['login_rate']['by_user'][$userKey])) {
            $_SESSION['login_rate']['by_user'][$userKey] = ['attempts' => 0, 'first_time' => time(), 'lock_until' => 0];
        }

        $ipEntry = &$_SESSION['login_rate']['by_ip'][$ip];
        $userEntry = &$_SESSION['login_rate']['by_user'][$userKey];

        $ipEntry['attempts'] = ($ipEntry['attempts'] ?? 0) + 1;
        $userEntry['attempts'] = ($userEntry['attempts'] ?? 0) + 1;

        if ($ipEntry['attempts'] >= LOGIN_MAX_ATTEMPTS) {
            $ipEntry['lock_until'] = time() + LOGIN_LOCK_SECONDS;
        }
        if ($userEntry['attempts'] >= LOGIN_MAX_ATTEMPTS) {
            $userEntry['lock_until'] = time() + LOGIN_LOCK_SECONDS;
        }

        $attempts = max($ipEntry['attempts'], $userEntry['attempts']);
        $delay = (int) min(pow(2, max(0, $attempts - 1)), 8);
        if ($delay > 0) {
            sleep($delay);
        }

        $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBook - Đăng nhập</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* CSS nội bộ căn giữa màn hình theo chiều dọc tuyệt đối */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 15px;
            margin: auto;
        }
        .login-box {
            background: #131a26;
            border: 1px solid #222f43;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .login-box h2 {
            font-weight: 700;
            letter-spacing: 1px;
            color: #ffffff;
        }
        .forgot-link {
            color: #6c7a93;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        .forgot-link:hover {
            color: #e50914;
        }
        .register-text {
            color: #6c7a93;
            font-size: 14px;
        }
        .register-text a {
            color: #e50914;
            text-decoration: none;
            font-weight: 500;
        }
        .register-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-box">
        
        <h2 class="text-center mb-4 text-uppercase">Đăng Nhập</h2>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="login" class="form-control" placeholder="Username hoặc Email" required autocomplete="username">
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required autocomplete="current-password">
            </div>

            <div class="mb-4 text-end">
                <a href="forgot-password.php" class="forgot-link">Quên mật khẩu?</a>
            </div>

            <button class="btn btn-danger w-100 py-2.5 mb-3 text-uppercase">Đăng nhập</button>
        </form>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center border-0 small mb-3" style="background-color: rgba(229, 9, 20, 0.15); color: #ff4d5a; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <p class="text-center register-text mt-3 mb-0">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </p>

    </div>
</div>

</body>
</html>