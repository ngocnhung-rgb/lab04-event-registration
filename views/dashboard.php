<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Dashboard Quản Trị</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="/" class="navbar-brand">🎓 HCMUS - PORTAL</a>
        <div class="navbar-nav">
            <a href="/">Home</a>
            <a href="/consultations/create">Secure Form</a>
            <a href="/dashboard" class="active">Login/Session</a>
            <form action="/logout" method="POST" style="margin: 0; display: inline;">
                <button type="submit" style="color: #fc8181; cursor: pointer;">Đăng xuất ↩</button>
            </form>
        </div>
    </nav>

    <div class="main-content" style="max-width: 1100px; margin: 40px auto; padding: 0 20px;">
        <?php if (function_exists('flash_get') && $success = flash_get('success')): ?>
            <div class="alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="debug-panel" style="background: #edf2f7; border: 1px solid #cbd5e0; border-radius: 8px; padding: 20px; margin-bottom: 30px; text-align: left;">
            <h4 style="margin-top: 0; margin-bottom: 12px; color: #2d3748;">🛠️ SESSION DEBUGGING INFORMATION (Mục tiêu bảo mật Lab04)</h4>
            <div class="debug-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; font-size: 14px;">
                <div><strong>• User (Email):</strong> <?= function_exists('h') ? h($_SESSION['user_email'] ?? 'N/A') : htmlspecialchars($_SESSION['user_email'] ?? 'N/A') ?></div>
                <div><strong>• Role Authority:</strong> <span style="color: #28a745; font-weight: bold;"><?= function_exists('h') ? h($_SESSION['user_role'] ?? 'Guest') : htmlspecialchars($_SESSION['user_role'] ?? 'Guest') ?></span></div>
                <div><strong>• Login At:</strong> <?= function_exists('h') ? h($_SESSION['login_at'] ?? 'N/A') : htmlspecialchars($_SESSION['login_at'] ?? 'N/A') ?></div>
                <div><strong>• Last Activity:</strong> <?= date('Y-m-d H:i:s', $_SESSION['last_activity'] ?? time()) ?></div>
                <div style="grid-column: span 2;"><strong>• Secure Session ID:</strong> <span style="color: #0056b3;"><?= session_id() ?></span> (Đã đổi qua <code>session_regenerate_id</code>)</div>
            </div>
        </div>

        <h3 style="color: #333; margin-bottom: 15px; text-align: left;">📋 Danh sách học viên đăng ký tư vấn khóa học</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Mã ID</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Khóa học lựa chọn</th>
                    <th>Thời gian đăng ký</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registrations)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 20px;">Hệ thống chưa ghi nhận lượt đăng ký nào từ tệp JSON.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    // Sắp xếp bản ghi mới nhất lên đầu bảng để quản trị viên dễ theo dõi
                    foreach (array_reverse($registrations) as $reg): 
                    ?>
                        <tr>
                            <td><span class="badge" style="background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;"><?= function_exists('h') ? h($reg['id']) : htmlspecialchars($reg['id']) ?></span></td>
                            <td><strong><?= function_exists('h') ? h($reg['name'] ?? $reg['fullname'] ?? '') : htmlspecialchars($reg['name'] ?? $reg['fullname'] ?? '') ?></strong></td>
                            <td><?= function_exists('h') ? h($reg['email']) : htmlspecialchars($reg['email']) ?></td>
                            <td><?= function_exists('h') ? h($reg['phone']) : htmlspecialchars($reg['phone']) ?></td>
                            <td>
                                <?php
                                    // ĐÃ SỬA: Thay event_type bằng course (Hỗ trợ dự phòng dữ liệu cũ)
                                    $type = $reg['course'] ?? $reg['event_type'] ?? '';
                                    if ($type === 'laravel_workshop') echo '🎓 Workshop Laravel 11';
                                    elseif ($type === 'cyber_security') echo '🛡️ Tọa đàm An ninh mạng';
                                    elseif ($type === 'ai_prompting') echo '🤖 Khóa học AI Prompting';
                                    else echo function_exists('h') ? h($type) : htmlspecialchars($type);
                                ?>
                            </td>
                            <td><?= function_exists('h') ? h($reg['registered_at'] ?? 'N/A') : htmlspecialchars($reg['registered_at'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>