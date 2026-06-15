<?php

namespace App\Controllers;

class EventController {

    // 1. GET /
    public function home() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        include dirname(__DIR__, 2) . '/views/home.php';
    }

    // 2. GET /consultations/create
    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        include dirname(__DIR__, 2) . '/views/consultations/create.php';
    }

    // 3. GET /consultations (PRG Index)
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        include dirname(__DIR__, 2) . '/views/consultations/create.php';
    }

    // 4. POST /consultations (Xử lý lưu dữ liệu + Bảo mật)
    public function store() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // --- LAYER 1: HONEYPOT ---
        if (!empty($_POST['website'])) {
            flash_set('error', 'Hệ thống phát hiện hành vi Spam tự động (Bot).');
            header("Location: /consultations/create"); exit;
        }

        // --- LAYER 2: RATE LIMIT (5s) ---
        if (isset($_SESSION['last_submit_time']) && (time() - $_SESSION['last_submit_time'] < 5)) {
            flash_set('error', 'Thao tác quá nhanh! Vui lòng đợi 5 giây.');
            header("Location: /consultations/create"); exit;
        }
        $_SESSION['last_submit_time'] = time();

        // --- LAYER 3: VALIDATION ---
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $course = $_POST['course'] ?? '';
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if (empty($name)) $errors['name'] = 'Họ và tên không được để trống.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email không hợp lệ.';
        if (!preg_match('/^[0-9]{9,11}$/', $phone)) $errors['phone'] = 'Số điện thoại không hợp lệ.';
        if (!in_array($course, ['laravel_workshop', 'cyber_security', 'ai_prompting'])) $errors['course'] = 'Vui lòng chọn khóa học.';

        if (!empty($errors)) {
            flash_set('errors', $errors);
            old_set($_POST);
            header("Location: /consultations/create"); exit;
        }

        // --- LAYER 4: FILE LOCKING (An toàn dữ liệu) ---
        $storageDir = dirname(__DIR__, 2) . '/storage';
        $filePath = $storageDir . '/consultations.json';
        if (!is_dir($storageDir)) mkdir($storageDir, 0777, true);

        $fp = fopen($filePath, 'c+');
        if (flock($fp, LOCK_EX)) { // Khóa file độc quyền
            $content = stream_get_contents($fp);
            $registrations = !empty($content) ? json_decode($content, true) : [];
            
            $registrations[] = [
                'id' => count($registrations) + 1,
                'name' => $name, 'email' => $email, 'phone' => $phone,
                'course' => $course, 'message' => $message,
                'registered_at' => date('Y-m-d H:i:s')
            ];

            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($registrations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            fflush($fp);
            flock($fp, LOCK_UN); // Mở khóa
        }
        fclose($fp);

        $_SESSION['success'] = 'Đăng ký tư vấn thành công!';
        header("Location: /consultations"); exit;
    }

    // 5. GET /dashboard (Bảo mật phiên & Auth Guard)
    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            flash_set('error', 'Vui lòng đăng nhập.');
            header("Location: /login"); exit;
        }

        // Kiểm tra User Agent (Chống Session Hijacking)
        if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            session_destroy();
            header("Location: /login"); exit;
        }

        $_SESSION['last_activity_at'] = time();
        $registrations = json_decode(file_get_contents(dirname(__DIR__, 2) . '/storage/consultations.json'), true) ?? [];
        include dirname(__DIR__, 2) . '/views/dashboard.php';
    }

    public function sessionDemo() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header("Location: /login"); exit; }
        $_SESSION['last_activity_at'] = time();
        include dirname(__DIR__, 2) . '/views/session_demo.php';
    }
}