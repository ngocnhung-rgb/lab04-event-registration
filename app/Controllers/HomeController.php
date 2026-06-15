<?php

namespace App\Controllers;

class HomeController {
    
    public function index() {
        // Đọc dữ liệu từ file JSON ra để hiển thị trên trang chủ
        $storageFile = __DIR__ . '/../../storage/events.json';
        $registrations = [];
        
        if (file_exists($storageFile)) {
            $registrations = json_decode(file_get_contents($storageFile), true) ?? [];
        }

        // Tạo giao diện HTML trang chủ trực tiếp ở đây hoặc include view
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <title>Cổng Thông Tin Sự Kiện</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; line-height: 1.6; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                th { background-color: #f4f4f4; }
                .btn { display: inline-block; padding: 10px 15px; background: #28a745; color: #fff; text-decoration: none; border-radius: 4px; }
                .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
            </style>
        </head>
        <body>
            <h1>🏛️ HỆ THỐNG ĐĂNG KÝ SỰ KIỆN CÔNG NGHỆ</h1>
            
            <?php if ($successMessage = flash_get('success')): ?>
                <div class="alert-success"><?= h($successMessage) ?></div>
            <?php endif; ?>

            <p>Chào mừng bạn đến với cổng thông tin sự kiện khoa Toán - Tin.</p>
            <p>
                <a href="/events/create" class="btn">➕ Đăng ký tham gia sự kiện mới</a>
                <a href="/login" style="margin-left: 15px; color: #007bff;">Đăng nhập hệ thống Quản trị →</a>
            </p>

            <h3>Danh sách người vừa đăng ký mới nhất:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Sự kiện</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888;">Chưa có lượt đăng ký nào. Hãy là người đầu tiên!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_reverse($registrations) as $reg): ?>
                            <tr>
                                <td><?= h($reg['fullname']) ?></td>
                                <td><?= h($reg['email']) ?></td>
                                <td><?= h($reg['phone']) ?></td>
                                <td>
                                    <?php
                                        switch($reg['event_type']) {
                                            case 'laravel_workshop': echo 'Workshop Làm chủ Laravel 11'; break;
                                            case 'cyber_security': echo 'Tọa đàm An ninh mạng 2026'; break;
                                            case 'ai_prompting': echo 'Khóa học Prompt Engineering'; break;
                                            default: echo h($reg['event_type']);
                                        }
                                    ?>
                                </td>
                                <td><?= h($reg['registered_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
    }
}