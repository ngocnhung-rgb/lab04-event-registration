<?php

// 1. Hàm mã hóa đầu ra chống tấn công XSS (Mục tiêu bảo mật bài Lab)
if (!function_exists('h')) {
    function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// 2. Hàm chuyển hướng trang (PRG Pattern)
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: " . $url);
        exit;
    }
}

// 3. Hàm thiết lập thông báo Flash Session (Biến mất sau 1 lần đọc)
if (!function_exists('flash_set')) {
    function flash_set($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }
}

// 4. Hàm đọc thông báo Flash Session
if (!function_exists('flash_get')) {
    function flash_get($key) {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // Đọc xong xóa luôn lập tức
            return $value;
        }
        return null;
    }
}

// 5. Hàm lưu lại dữ liệu cũ người dùng đã nhập
if (!function_exists('old_set')) {
    function old_set($data) {
        $_SESSION['old'] = $data;
    }
}

// 6. Hàm lấy lại dữ liệu cũ để đổ vào ô input
if (!function_exists('old_get')) {
    function old_get($key) {
        return $_SESSION['old'][$key] ?? '';
    }
}

// 7. Hàm xóa sạch dữ liệu cũ sau khi render xong form
if (!function_exists('old_clear')) {
    function old_clear() {
        unset($_SESSION['old']);
    }
}

// 8. Hàm kiểm tra phần tử nằm trong danh sách an toàn (In-list validation)
if (!function_exists('in_list')) {
    function in_list($value, $list) {
        return in_array($value, $list, true);
    }
}

// 9. Hàm kiểm tra Timeout phiên làm việc (Rubric: Khóa bảo mật Session)
if (!function_exists('check_session_timeout')) {
    function check_session_timeout() {
        if (isset($_SESSION['user_id'])) {
            $idleLimit = 300; // Phiên hết hạn sau 5 phút (300 giây) không tương tác
            $currentTime = time();
            
            if (isset($_SESSION['last_activity']) && ($currentTime - $_SESSION['last_activity'] > $idleLimit)) {
                // Hủy session sạch sẽ nếu quá thời gian
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
                session_destroy();
                
                // Tạo session mới để ném thông báo flash lỗi
                session_start();
                flash_set('error', 'Phiên làm việc đã hết hạn do bạn không hoạt động lâu. Vui lòng đăng nhập lại.');
                header("Location: /login");
                exit;
            }
            // Cập nhật lại thời gian tương tác mới nhất
            $_SESSION['last_activity'] = $currentTime;
        }
    }
}

// 10. Hàm chống Hijacking bằng cách kiểm tra User-Agent trùng khớp
if (!function_exists('check_session_context')) {
    function check_session_context() {
        if (isset($_SESSION['user_id'])) {
            $currentAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $currentAgent) {
                // Phát hiện User-Agent thay đổi đột ngột (Nghi ngờ hack session)
                $_SESSION = [];
                session_destroy();
                session_start();
                flash_set('error', 'Phát hiện thay đổi thiết bị đột ngột. Để bảo mật, vui lòng đăng nhập lại.');
                header("Location: /login");
                exit;
            }
        }
    }
}

/**
 * Chốt chặn Rate Limit - Ngăn chặn người dùng hoặc Bot nhấn gửi Form liên tục
 * @param int $seconds Khoảng thời gian giãn cách tối thiểu giữa 2 lần submit (Mặc định 5 giây)
 * @return void
 */
if (!function_exists('check_rate_limit')) {
    function check_rate_limit($seconds = 5) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $currentTime = time();
        
        if (isset($_SESSION['last_submit_time'])) {
            $timeElapsed = $currentTime - $_SESSION['last_submit_time'];
            
            if ($timeElapsed < $seconds) {
                $timeLeft = $seconds - $timeElapsed;
                
                // Thiết lập thông báo lỗi Flash gửi ngược về giao diện
                if (function_exists('flash_set')) {
                    flash_set('error', "Thao tác quá nhanh! Hệ thống đã kích hoạt cơ chế Rate Limit. Vui lòng đợi {$timeLeft} giây trước khi gửi lại.");
                } else {
                    $_SESSION['error'] = "Thao tác quá nhanh! Vui lòng đợi {$timeLeft} giây.";
                }
                
                // Điều hướng quay trở lại form đăng ký lập tức (PRG Pattern)
                header('Location: /consultations/create');
                exit;
            }
        }
        
        // Cập nhật mốc thời gian của lượt gửi đơn hợp lệ hiện tại
        $_SESSION['last_submit_time'] = $currentTime;
    }
}