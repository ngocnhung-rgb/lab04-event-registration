<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cổng Đăng Ký Tư Vấn Khóa Học</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/" class="navbar-brand">🎓 HCMUS - PORTAL</a>
        <div class="navbar-nav">
            <a href="/">Home</a>
            <a href="/consultations/create" class="active">Secure Form</a>
            <a href="/dashboard">Login/Session</a>
        </div>
    </nav>

    <div class="container">
        <h2>🎉 ĐĂNG KÝ TƯ VẤN KHÓA HỌC</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <strong>Thành công!</strong> <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php 
        if (function_exists('flash_get')) { flash_get('error'); }
        if (isset($_SESSION['error'])) { unset($_SESSION['error']); }
        ?>

        <?php 
        // Đọc danh sách lỗi chi tiết từng trường
        $errors = [];
        if (function_exists('flash_get')) {
            $errors = flash_get('errors') ?? [];
        }
        if (empty($errors) && isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        // Xóa ngay bộ nhớ Session errors để thông báo biến mất ngay lập tức sau 1 lần load/nhấn nút
        if (isset($_SESSION['errors'])) { unset($_SESSION['errors']); }

        // Đọc lại dữ liệu cũ người dùng đã điền
        $old = [];
        if (function_exists('old_get')) {
            // Nếu hệ thống dùng hàm helper cũ thì lấy qua helper, hoặc đọc từ session dự phòng
            $old = $_SESSION['old'] ?? []; 
        } else {
            $old = $_SESSION['old'] ?? [];
        }
        if (isset($_SESSION['old'])) { unset($_SESSION['old']); }

        // Nếu mảng $errors có chứa lỗi validation, đổ hộp thông báo màu đỏ đúng chuẩn yêu cầu của Lab
        if (!empty($errors)): 
        ?>
            <div style="background-color: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; padding: 15px; margin-bottom: 22px; border-radius: 8px; font-size: 14px; text-align: left; font-weight: 500; box-shadow: 0 2px 4px rgba(229, 62, 62, 0.05);">
                ⚠️ <strong>Vui lòng kiểm tra lại thông tin. Dữ liệu cũ vẫn được giữ để sửa.</strong>
            </div>
        <?php endif; ?>

        <form action="/consultations" method="POST">
            <div class="form-group hidden-field">
                <label>Website:</label>
                <input type="text" name="website" value="" autocomplete="off">
            </div>

            <div class="form-group">
                <label>Họ và tên (Name) *</label>
                <input type="text" name="name" required placeholder="Nguyễn Văn A" 
                       value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email liên hệ *</label>
                <input type="email" name="email" required placeholder="sv@student.hcmus.edu.vn"
                       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Số điện thoại *</label>
                <input type="text" name="phone" required placeholder="0901234567"
                       value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Khóa học lựa chọn (Course) *</label>
                <select name="course" required>
                    <?php $selectedCourse = $old['course'] ?? ''; ?>
                    <option value="">-- Chọn khóa học tư vấn --</option>
                    <option value="laravel_workshop" <?= $selectedCourse === 'laravel_workshop' ? 'selected' : '' ?>>Workshop Làm chủ Laravel 11</option>
                    <option value="cyber_security" <?= $selectedCourse === 'cyber_security' ? 'selected' : '' ?>>Tọa đàm An ninh mạng 2026</option>
                    <option value="ai_prompting" <?= $selectedCourse === 'ai_prompting' ? 'selected' : '' ?>>Khóa học Prompt Engineering cấp tốc</option>
                </select>
                <?php if (isset($errors['course'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['course'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Lời nhắn kèm theo (Message)</label>
                <textarea name="message" rows="3" placeholder="Nhập câu hỏi hoặc lời nhắn của bạn..."><?= htmlspecialchars($old['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="btn-submit">Đăng ký</button>
        </form>
    </div>
    
    <?php if (function_exists('old_clear')) { old_clear(); } ?>
</body>
</html>