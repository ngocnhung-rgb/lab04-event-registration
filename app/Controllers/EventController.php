<?php

namespace App\Controllers;

class EventController {

    // 1. GET / -> Hiển thị trang chủ giới thiệu ban đầu
    public function home() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include dirname(__DIR__, 2) . '/views/home.php';
    }

    // 2. GET /consultations/create -> Hiển thị mục chứa Form đăng ký riêng biệt
    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include dirname(__DIR__, 2) . '/views/consultations/create.php';
    }

    // 2.5 GET /consultations -> Tiếp nhận luồng PRG sau khi submit thành công
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include dirname(__DIR__, 2) . '/views/consultations/create.php';
    }

    // 3. POST /consultations -> Xử lý lưu Form đăng ký an toàn (Áp dụng chuẩn điều hướng PRG)
    public function store() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // --- LAYER 1: KIỂM TRA HONEYPOT ANTI-SPAM ---
        if (!empty($_POST['website'])) {
            if (function_exists('flash_set')) {
                flash_set('error', 'Hệ thống phát hiện hành vi Spam tự động (Bot). Yêu cầu bị chặn.');
            } else {
                $_SESSION['error'] = 'Hệ thống phát hiện hành vi Spam tự động (Bot). Yêu cầu bị chặn.';
            }
            header("Location: /consultations/create");
            exit;
        }

        // --- LAYER 2: BỔ SUNG CƠ CHẾ RATE LIMIT ---
        $secondsLimit = 5; 
        $currentTime = time();
        if (isset($_SESSION['last_submit_time'])) {
            $timeElapsed = $currentTime - $_SESSION['last_submit_time'];
            if ($timeElapsed < $secondsLimit) {
                $timeLeft = $secondsLimit - $timeElapsed;
                $rateLimitError = "Thao tác quá nhanh! Cơ chế Rate Limit đã kích hoạt. Vui lòng đợi {$timeLeft} giây trước khi gửi lại đơn.";
                
                if (function_exists('flash_set')) {
                    flash_set('errors', ['phone' => 'Gửi đơn quá nhanh, vui lòng đợi.']);
                    $_SESSION['error'] = $rateLimitError; 
                } else {
                    $_SESSION['errors'] = ['phone' => 'Gửi đơn quá nhanh, vui lòng đợi.'];
                    $_SESSION['error'] = $rateLimitError;
                }
                
                header("Location: /consultations/create");
                exit;
            }
        }
        $_SESSION['last_submit_time'] = $currentTime;

        // --- LUỒNG VALIDATION DỮ LIỆU ---
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $course  = $_POST['course'] ?? '';
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        $allowedCourses = ['laravel_workshop', 'cyber_security', 'ai_prompting'];

        if (empty($name)) {
            $errors['name'] = 'Họ và tên không được để trống.';
        }
        if (empty($email)) {
            $errors['email'] = 'Email liên hệ không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Định dạng Email không hợp lệ.';
        }
        if (empty($phone)) {
            $errors['phone'] = 'Số điện thoại không được để trống.';
        } elseif (!preg_match('/^[0-9]{9,11}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại phải từ 9 đến 11 ký tự số.';
        }
        
        if (empty($course)) {
            $errors['course'] = 'Vui lòng chọn một khóa học muốn tư vấn.';
        } elseif (!in_array($course, $allowedCourses)) {
            $errors['course'] = 'Khóa học được chọn không hợp lệ hoặc không tồn tại trong hệ thống.';
        }

        if (!empty($errors)) {
            if (function_exists('flash_set')) {
                flash_set('errors', $errors);
            } else {
                $_SESSION['errors'] = $errors;
            }
            
            if (function_exists('old_set')) {
                old_set([
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'course'  => $course,
                    'message' => $message
                ]);
            } else {
                $_SESSION['old'] = [
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'course'  => $course,
                    'message' => $message
                ];
            }
            header("Location: /consultations/create");
            exit;
        }

        // --- XỬ LÝ LƯU THÀNH FILE JSON ---
        $storageDir = dirname(__DIR__, 2) . '/storage';
        $filePath   = $storageDir . '/consultations.json';

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }

        $registrations = [];
        if (file_exists($filePath)) {
            $jsonContent = file_get_contents($filePath);
            $registrations = json_decode($jsonContent, true) ?? [];
        }

        $newRegistration = [
            'id'            => count($registrations) + 1,
            'name'          => $name,  
            'email'         => $email,
            'phone'         => $phone,
            'course'        => $course, 
            'message'       => $message,
            'registered_at' => date('Y-m-d H:i:s')
        ];

        $registrations[] = $newRegistration;
        file_put_contents($filePath, json_encode($registrations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $_SESSION['success'] = 'Đăng ký tư vấn khóa học thành công! Hệ thống đã ghi nhận thông tin của bạn.';
        header("Location: /consultations");
        exit;
    }

    // 4. GET /dashboard -> Quản lý phân hệ Dashboard và bảo mật dữ liệu nâng cao
    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CHỐT CHẶN 1: Chưa đăng nhập tài khoản -> Từ chối truy cập (Auth guard)
        if (!isset($_SESSION['user_id'])) {
            $errorText = 'Vui lòng đăng nhập tài khoản quản trị để truy cập phân hệ Dashboard.';
            if (function_exists('flash_set')) {
                flash_set('error', $errorText);
            } else {
                $_SESSION['error'] = $errorText;
            }
            header("Location: /login");
            exit;
        }

        // CHỐT CHẶN 2: Kiểm tra chống trộm Session (Session Hijacking)
        if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $_SESSION = [];
            session_destroy();
            session_start();
            if (function_exists('flash_set')) {
                flash_set('error', 'Cảnh báo an ninh: Phát hiện dấu hiệu giả mạo phiên làm việc. Vui lòng đăng nhập lại.');
            } else {
                $_SESSION['error'] = 'Cảnh báo an ninh: Phát hiện dấu hiệu giả mạo phiên làm việc. Vui lòng đăng nhập lại.';
            }
            header("Location: /login");
            exit;
        }

        // 🌟 SAU KHI ĐI QUA CÁC CHỐT CHẶN AN TOÀN: Cập nhật lại thời gian tương tác hợp lệ mới nhất cho request kế tiếp
        $_SESSION['last_activity_at'] = time();

        $filePath = dirname(__DIR__, 2) . '/storage/consultations.json';
        $registrations = [];
        if (file_exists($filePath)) {
            $registrations = json_decode(file_get_contents($filePath), true) ?? [];
        }

        include dirname(__DIR__, 2) . '/views/dashboard.php';
    }

    // 5. GET /session-demo -> Đọc cấu trúc mảng session phục vụ debug bài Lab
    public function sessionDemo() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $_SESSION['last_activity_at'] = time();
        include dirname(__DIR__, 2) . '/views/session_demo.php';
    }
}