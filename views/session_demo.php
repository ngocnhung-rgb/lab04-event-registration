<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Session Demo Debug - HCMUS PORTAL</title>
    <link rel="stylesheet" href="/css/style.css"> <!-- Sử dụng tệp CSS chung của bạn -->
    <style>
        .debug-container { max-width: 800px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .debug-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .debug-table th, .debug-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .debug-table th { background-color: #f4f6f9; color: #333; }
        .badge { padding: 4px 8px; background: #28a745; color: #fff; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="debug-container">
        <h2>🛠️ Khu vực Kiểm thử & Gỡ lỗi Phiên (Session Debug)</h2>
        <p>Thông tin ngữ cảnh và trạng thái phiên làm việc hiện tại lưu trên Server:</p>
        
        <table class="debug-table">
            <tr>
                <th>Tham số kiểm tra</th>
                <th>Giá trị hiện tại trong $_SESSION</th>
            </tr>
            <tr>
                <td><strong>Mã định danh phiên (Session ID)</strong></td>
                <td><code><?php echo session_id(); ?></code></td>
            </tr>
            <tr>
                <td><strong>Tài khoản quản trị</strong></td>
                <td><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><strong>Vai trò hệ thống</strong></td>
                <td><span class="badge"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span></td>
            </tr>
            <tr>
                <td><strong>Thời gian đăng nhập</strong></td>
                <td><?php echo htmlspecialchars($_SESSION['login_at'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><strong>Dấu vết thiết bị (User Agent)</strong></td>
                <td><small><code><?php echo htmlspecialchars($_SESSION['user_agent'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></code></small></td>
            </tr>
        </table>
        
        <p style="margin-top: 20px;"><a href="/dashboard" class="btn">⬅️ Quay lại Dashboard</a></p>
    </div>
</body>
</html>