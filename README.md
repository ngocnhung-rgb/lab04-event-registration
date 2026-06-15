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

3. Hướng dẫn cài đặt & Chạy dự án
Dự án này được thiết kế để chạy trên môi trường GitHub Codespaces. Hãy làm theo các bước sau:

Khởi động Server: Mở Terminal trong Codespace.
Chạy lệnh sau để khởi động PHP Server:

php -S 0.0.0.0:8000 -t public

Sau khi chạy lệnh trên, VS Code/Codespace sẽ hiện một thông báo ở góc dưới bên phải màn hình: "Your application running on port 8000 is available".

Bạn hãy nhấn nút "Open in Browser" trên thông báo đó để truy cập trang web.

Lưu ý: Nếu thông báo không hiện, hãy vào tab Ports trong panel phía dưới, tìm cổng 8000, chuột phải vào đó và chọn "Open in Browser" (hoặc copy link https://didactic-space-succotash-5vg5749p9j5x34jw9-8000.app.github.dev/).
Project sử dụng cơ chế kiểm soát truy cập qua cổng, đảm bảo rằng trong tab Ports, cổng 8000 được để ở chế độ Public.


Tài khoản demo: student@example.com / 123456

Chú thích: Dự án này ưu tiên chạy trực tiếp trên GitHub Codespaces để trải nghiệm ngay. Nếu Codespaces hết hạn hoặc không truy cập được, giảng viên có thể clone mã nguồn về máy cục bộ và chạy bằng lệnh php -S localhost:8000 -t public trong thư mục gốc.