<?php

namespace App\Controllers;

class AuthController {

    /**
     * GET /login (Giữ nguyên hiển thị View)
     */
    public function showLoginForm() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    /**
     * POST /login (XỬ LÝ ĐĂNG NHẬP AN TOÀN VÀ ĐỒNG BỘ TIMEOUT)
     */
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $adminEmail = 'student@example.com';
        $adminPass  = '123456'; 

        if (empty($email) || empty($password)) {
            $errorText = 'Vui lòng điền đầy đủ cả Email và Mật khẩu.';
        } elseif ($email !== $adminEmail || $password !== $adminPass) {
            $errorText = 'Email hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại.';
        } else {
            $errorText = '';
        }

        // Nhóm test: Login sai -> Redirect về login, flash lỗi đúng
        if (!empty($errorText)) {
            if (function_exists('flash_set')) {
                flash_set('error', $errorText);
            } else {
                $_SESSION['error'] = $errorText;
            }
            
            if (function_exists('old_set')) {
                old_set(['email' => $email]);
            } else {
                $_SESSION['old'] = ['email' => $email];
            }

            header("Location: /login");
            exit;
        }

        // --- TIÊU CHÍ: PHÁ HỦY TOÀN BỘ CƠ HỘI CỦA TẤN CÔNG SESSION FIXATION ---
        // Nhóm test: Login đúng -> Regenerate session, redirect dashboard, flash success
        session_regenerate_id(true); 

        // Thiết lập Context phiên an toàn
        $_SESSION['user_id']          = 1;
        $_SESSION['user_email']       = $email;
        $_SESSION['user_role']        = 'Admin';
        $_SESSION['login_at']         = date('Y-m-d H:i:s');
        $_SESSION['user_agent']       = $_SERVER['HTTP_USER_AGENT'] ?? ''; // Đóng băng vân trình duyệt

        // Khởi tạo mốc thời gian hoạt động ngay tại đây
        $_SESSION['last_activity_at'] = time(); 

        // Xử lý tính năng Remember Me (nếu có)
        if (isset($_POST['remember_me'])) {
            $rememberToken = bin2hex(random_bytes(32)); 
            setcookie(
                'remember_token',
                $rememberToken,
                time() + (86400 * 30), 
                '/',
                '',
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 
                true                                      
            );
        }

        if (function_exists('flash_set')) {
            flash_set('success', 'Đăng nhập hệ thống quản trị thành công.');
        } else {
            $_SESSION['success'] = 'Đăng nhập hệ thống quản trị thành công.';
        }
        
        header("Location: /dashboard");
        exit;
    }

    /**
     * POST /logout (XÓA SẠCH DẤU VẾT COOKIE & PHIÊN)
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $logoutNotice = 'Bạn đã hoàn tất phiên làm việc và đăng xuất an toàn.';

        // 1. Giải phóng bộ nhớ $_SESSION
        $_SESSION = [];

        // 2. Ép trình duyệt hủy bỏ hoàn toàn Cookie định danh SessionID
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Xóa tệp tin vật lý chứa thông tin phiên trên Server
        session_destroy();

        // 4. Khởi chạy một phiên tạm thời mới duy nhất để chứa thông điệp điều hướng về trang Login
        session_start();
        if (function_exists('flash_set')) {
            flash_set('logout_notice', $logoutNotice);
        } else {
            $_SESSION['logout_notice'] = $logoutNotice;
        }

        header("Location: /login");
        exit;
    }
}