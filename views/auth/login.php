<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập Quản Trị Hệ Thống</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="/" class="navbar-brand">🎓 HCMUS - PORTAL</a>
        <div class="navbar-nav">
            <a href="/">Home</a>
            <a href="/consultations/create">Secure Form</a>
            <a href="/dashboard">Login/Session</a>
            <a href="/login" class="active">Đăng Nhập</a>
        </div>
    </nav>

    <div class="container">
        <?php 
        // 1. Đọc gói tin thông báo trạng thái logout/timeout
        $logoutMsg = '';
        if (function_exists('flash_get')) {
            $logoutMsg = flash_get('logout_notice');
        }
        if (empty($logoutMsg) && isset($_SESSION['logout_notice'])) {
            $logoutMsg = $_SESSION['logout_notice'];
        }
        // Xóa ngay lập tức để không bị hiện mãi khi reload (chống kẹt trạng thái)
        if (isset($_SESSION['logout_notice'])) { unset($_SESSION['logout_notice']); }
        ?>

        <?php if (!empty($logoutMsg)): ?>
            <h2>Phiên đăng nhập đã kết thúc</h2>
        <?php else: ?>
            <h2>🔐 ĐĂNG NHẬP QUẢN TRỊ</h2>
        <?php endif; ?>

        <?php if (!empty($logoutMsg)): ?>
            <div style="background-color: #fffaf0; color: #dd6b20; border: 1px solid #feebc8; padding: 12px 15px; margin-bottom: 20px; border-radius: 6px; font-size: 14px; text-align: left; font-weight: bold;">
                <?= htmlspecialchars($logoutMsg, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php 
        // 2. Đọc gói tin thông báo lỗi sai tài khoản hoặc lỗi hệ thống
        $errorMsg = '';
        if (function_exists('flash_get')) { 
            $errorMsg = flash_get('error'); 
        } 
        if (empty($errorMsg) && isset($_SESSION['error'])) { 
            $errorMsg = $_SESSION['error']; 
        }
        if (isset($_SESSION['error'])) { 
            unset($_SESSION['error']); 
        }

        // 3. Lấy lại email cũ đã điền từ session dự phòng nếu không dùng helper
        $oldEmail = '';
        if (function_exists('old_get')) {
            $oldEmail = old_get('email') ?? '';
        } else {
            $oldEmail = $_SESSION['old']['email'] ?? '';
        }
        if (isset($_SESSION['old'])) { unset($_SESSION['old']); }

        if (!empty($errorMsg)): 
        ?>
            <div class="alert-danger-highlight">
                <span class="alert-icon">⚠️</span>
                <div class="alert-content">
                    <strong>Thông báo hệ thống:</strong>
                    <p><?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="form-group">
                <label>Email quản trị</label>
                <input type="text" name="email" value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>" placeholder="Nhập email...">
            </div>
            
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu...">
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <input type="checkbox" name="remember_me" id="remember_me" style="width: auto; cursor: pointer; margin: 0;">
                <label for="remember_me" style="margin: 0; font-weight: normal; cursor: pointer; color: #555; font-size: 14px;">Ghi nhớ đăng nhập (Remember Me)</label>
            </div>
            
            <button type="submit" class="btn-submit">Login</button>
        </form>
    </div>

    <?php if (function_exists('old_clear')) { old_clear(); } ?>
</body>
</html>