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
session_name('LAB04SESSID');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,      
    'httponly' => true,        // Ngăn chặn mã độc JavaScript đọc cookie (Chống XSS)
    'samesite' => 'Lax',       // Cơ chế giảm thiểu rủi ro tấn công giả mạo CSRF
]);
session_start();

$timeoutDuration = 300; 

if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity_at'])) {
    $timeElapsed = time() - $_SESSION['last_activity_at'];
    
    if ($timeElapsed > $timeoutDuration) {
        $_SESSION = []; 
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy(); 
        
        // Khởi tạo lại phiên mới để giữ thông báo
        session_start();
        
        $noticeText = 'Phiên làm việc đã hết hạn do bạn không hoạt động quá thời gian quy định. Vui lòng đăng nhập lại.';
        if (function_exists('flash_set')) {
            flash_set('logout_notice', $noticeText);
        } else {
            $_SESSION['logout_notice'] = $noticeText;
        }
        
        // ghi đè Cookie định danh cho phiên thông báo mới trước khi Redirect
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), session_id(), 0,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        header("Location: /login");
        exit;
    }
}

$router = new Router();

// =========================================================================
// 1. PHÂN HỆ ĐIỀU HƯỚNG TƯ VẤN KHÓA HỌC (SECURE FORM, VALIDATION & ANTI-SPAM)
// =========================================================================

$router->get('/', [EventController::class, 'home']);
$router->get('/consultations/create', [EventController::class, 'create']);
$router->post('/consultations', [EventController::class, 'store']);
$router->get('/consultations', [EventController::class, 'index']);

// =========================================================================
// 2. PHÂN HỆ XÁC THỰC QUẢN TRỊ VIÊN & DASHBOARD (LOGIN / SESSION FLOW)
// =========================================================================

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [EventController::class, 'dashboard']);
$router->get('/session-demo', [EventController::class, 'sessionDemo']);

// =========================================================================
// 3. THỰC THI ĐỊNH TUYẾN 
// =========================================================================
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));