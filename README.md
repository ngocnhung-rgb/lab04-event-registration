1. Giới thiệu: Hệ thống quản lý đăng ký tư vấn khóa học được xây dựng nhằm mục đích thực hành các kỹ thuật bảo mật web cơ bản và nâng cao trong môi trường PHP. Dự án tập trung vào việc bảo vệ dữ liệu người dùng và chống lại các lỗ hổng bảo mật phổ biến.

2. Tính năng bảo mật chính

Anti-Spam & Validation: Sử dụng kỹ thuật Honeypot để chặn bot và kiểm tra định dạng dữ liệu nghiêm ngặt phía Server-side.

PRG Pattern: Ngăn chặn gửi lại dữ liệu trùng lặp khi làm mới trang.

Session Security: - Regenerate Session ID khi đăng nhập để chống Session Fixation.
Kiểm tra User-Agent để ngăn chặn Session Hijacking.
Cấu hình Cookie an toàn (HttpOnly, SameSite=Lax).
Tự động đăng xuất sau 5 phút không hoạt động.

Data Protection: Sử dụng flock() để khóa file khi ghi dữ liệu, chống xung đột dữ liệu.

XSS Prevention: Sử dụng htmlspecialchars() để làm sạch dữ liệu hiển thị.

CSRF Prevention: Sử dụng phương thức POST cho các hành động quan trọng như đăng xuất.

3. Hướng dẫn chạy dự án

Cách 1: Chạy trực tiếp trên GitHub Codespaces
- Mở Terminal trong Codespace.
- Chạy lệnh sau để khởi động PHP Server: php -S 0.0.0.0:8000 -t public
- Nhấn vào thông báo "Open in Browser" hoặc vào tab Ports, chuột phải vào cổng 8000 chọn Port Visibility -> Public, sau đó mở link URL được cung cấp.

Cách 2: Chạy trên máy cục bộ
Nếu bạn muốn chạy trên máy cá nhân, hãy clone repository về và sử dụng lệnh:
php -S localhost:8000 -t public


Tài khoản demo: student@example.com / 123456

Chú thích: Dự án này ưu tiên chạy trực tiếp trên GitHub Codespaces để trải nghiệm ngay. Nếu Codespaces hết hạn hoặc không truy cập được, người dùng có thể clone mã nguồn về máy cục bộ và chạy bằng lệnh php -S localhost:8000 -t public trong thư mục gốc.