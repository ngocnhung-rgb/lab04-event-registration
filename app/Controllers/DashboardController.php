<?php

namespace App\Controllers;

class DashboardController {

    public function index() {
        // 1. LỚP BẢO VỆ (Auth Guard): Nếu chưa đăng nhập session, lập tức đá văng ra trang Login
        if (!isset($_SESSION['user_id'])) {
            if (function_exists('flash_set')) {
                flash_set('error', 'Khu vực hạn chế! Vui lòng đăng nhập tài khoản quản trị trước.');
            }
            header("Location: /login");
            exit;
        }

        // 2. Đọc danh sách dữ liệu đăng ký từ file JSON
        // Từ app/Controllers/ lùi 2 tầng ra gốc, rồi vào storage/
        $storageFile = __DIR__ . '/../../storage/events.json';
        
        // Cơ chế tự sửa đường dẫn nếu bị tính lệch tầng
        if (!file_exists($storageFile)) {
            $storageFile = __DIR__ . '/../storage/events.json';
        }

        $registrations = [];
        if (file_exists($storageFile)) {
            $registrations = json_decode(file_get_contents($storageFile), true) ?? [];
        }

        // 3. Nạp file view giao diện Dashboard
        $viewPath = __DIR__ . '/../../views/events/dashboard.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Lỗi hệ thống: Không tìm thấy file giao diện dashboard.php tại: " . $viewPath;
        }
    }
}