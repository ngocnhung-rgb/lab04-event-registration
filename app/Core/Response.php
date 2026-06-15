<?php

namespace App\Core;

class Response {
    /**
     * Thiết lập HTTP Status Code cho phản hồi
     */
    public static function setStatusCode(int $code) {
        http_response_code($code);
    }

    /**
     * Trả về dữ liệu dạng JSON (Phục vụ nếu làm API hoặc gọi AJAX)
     */
    public static function json($data, int $code = 200) {
        self::setStatusCode($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Điều hướng trang (Redirect) an toàn
     */
    public static function redirect(string $url) {
        header("Location: " . $url);
        exit;
    }
}