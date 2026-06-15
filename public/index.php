<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Thiết lập thời gian thực chính xác theo múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\EventController;
use App\Controllers\AuthController;

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

// Thiết lập cookie phiên an toàn chống tấn công XSS và CSRF mức nền tảng
session_name('CONSULTATION_SESSID');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,      
    'httponly' => true,        // Ngăn chặn mã độc JavaScript đọc cookie (Chống XSS)
    'samesite' => 'Lax',       // Cơ chế giảm thiểu rủi ro tấn công giả mạo CSRF
]);
session_start();

// Chạy các bộ lọc kiểm tra Timeout phiên làm việc và Ngữ cảnh Session (nếu có)
if (function_exists('check_session_timeout')) {
    check_session_timeout();
}
if (function_exists('check_session_context')) {
    check_session_context();
}

$router = new Router();

// =========================================================================
// 1. PHÂN HỆ ĐIỀU HƯỚNG TƯ VẤN KHÓA HỌC (SECURE FORM, VALIDATION & ANTI-SPAM)
// =========================================================================

// Khi mở trang web lên ban đầu -> Hiển thị trang Home giới thiệu, tách biệt hoàn toàn
$router->get('/', [EventController::class, 'home']);

// Nhóm chức năng tạo Form riêng biệt khi bấm vào tab "Secure Form"
$router->get('/consultations/create', [EventController::class, 'create']);

// Điểm tiếp nhận submit form đăng ký thông qua phương thức POST an toàn
$router->post('/consultations', [EventController::class, 'store']);

// =========================================================================
// 2. PHÂN HỆ XÁC THỰC QUẢN TRỊ VIÊN & DASHBOARD (LOGIN / SESSION FLOW)
// =========================================================================

// ĐÃ SỬA: GET /login -> Trỏ đúng về hàm showLoginForm để chỉ hiển thị giao diện View
$router->get('/login', [AuthController::class, 'showLoginForm']);

// ĐÃ SỬA: POST /login -> Trỏ đúng về hàm login để xử lý xác thực tài khoản khi submit form
$router->post('/login', [AuthController::class, 'login']);

// Đăng xuất an toàn bằng phương thức POST, xóa sạch session (Giữ nguyên)
$router->post('/logout', [AuthController::class, 'logout']);

// Dashboard bảo vệ - Gọi tường minh đến hàm dashboard của EventController (Giữ nguyên)
$router->get('/dashboard', [EventController::class, 'dashboard']);

// =========================================================================
// 3. THỰC THI ĐỊNH TUYẾN (DISPATCHER)
// =========================================================================
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));