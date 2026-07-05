-- =============================================
-- TẠO DATABASE VÀ BẢNG CHO WEBSITE XEM PHIM
-- =============================================

-- Bước 1: Tạo database
CREATE DATABASE IF NOT EXISTS movie_website
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Bước 2: Chọn database vừa tạo
USE movie_website;

-- Bước 3: Tạo bảng movies
-- Bảng này lưu toàn bộ thông tin phim
CREATE TABLE IF NOT EXISTS movies (
    id       INT AUTO_INCREMENT PRIMARY KEY,  -- Khóa chính, tự tăng
    title    VARCHAR(255) NOT NULL,           -- Tên phim (bắt buộc)
    description TEXT,                         -- Mô tả nội dung phim
    image    VARCHAR(255),                    -- Tên file ảnh poster
    video    VARCHAR(255),                    -- Tên file video hoặc link
    year     YEAR,                            -- Năm phát hành
    category VARCHAR(100),                    -- Thể loại phim
    views    INT DEFAULT 0,                   -- Lượt xem (mặc định 0)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Ngày thêm vào
);

-- =============================================
-- DỮ LIỆU MẪU (10 phim)
-- =============================================
INSERT INTO movies (title, description, image, video, year, category, views) VALUES

('Avengers: Endgame',
 'Sau sự kiện Thanos tiêu diệt một nửa sinh linh trong vũ trụ, các Avengers còn lại tập hợp để thực hiện kế hoạch cuối cùng nhằm đảo ngược hành động của hắn.',
 'avengers.jpg', 'avengers.mp4', 2019, 'Hành động', 15800),

('Spider-Man: No Way Home',
 'Peter Parker đề nghị Tiến sĩ Strange xóa ký ức của mọi người về danh tính của mình, nhưng một phép thuật sai lầm đã mở ra đa vũ trụ đầy nguy hiểm.',
 'spiderman.jpg', 'spiderman.mp4', 2021, 'Hành động', 14200),

('Interstellar',
 'Một nhóm phi hành gia du hành qua lỗ sâu băng không gian để tìm kiếm hành tinh mới có thể cứu nhân loại khỏi sự tuyệt chủng trên Trái Đất.',
 'interstellar.jpg', 'interstellar.mp4', 2014, 'Khoa học viễn tưởng', 12500),

('Titanic',
 'Câu chuyện tình yêu bi thương giữa Jack và Rose trên con tàu sang trọng Titanic trong chuyến hành trình định mệnh vào năm 1912.',
 'titanic.jpg', 'titanic.mp4', 1997, 'Tình cảm', 11000),

('The Dark Knight',
 'Batman đối mặt với Joker – kẻ hắc ám muốn nhấn chìm Gotham City vào hỗn loạn. Cuộc chiến giữa trật tự và vô chính phủ bắt đầu.',
 'darkknight.jpg', 'darkknight.mp4', 2008, 'Hành động', 13400),

('Inception',
 'Dom Cobb là kẻ trộm kỳ tài có khả năng xâm nhập vào giấc mơ của người khác để đánh cắp bí mật. Anh nhận nhiệm vụ nguy hiểm nhất: cấy ghép ý tưởng.',
 'inception.jpg', 'inception.mp4', 2010, 'Khoa học viễn tưởng', 10800),

('Doraemon: Nobita và Mặt Trăng Thám Hiểm',
 'Nobita và nhóm bạn cùng Doraemon bắt đầu cuộc phiêu lưu đến Mặt Trăng, nơi họ phát hiện ra một nền văn minh bí ẩn đang gặp nguy hiểm.',
 'doraemon.jpg', 'doraemon.mp4', 2019, 'Hoạt hình', 9500),

('Lật Mặt 7: Một Điều Ước',
 'Bộ phim hài hành động Việt Nam kể về hành trình của một gia đình với những tình huống hài hước và cảm động, đầy ắp tiếng cười.',
 'latmat7.jpg', 'latmat7.mp4', 2024, 'Hài', 8700),

('The Conjuring',
 'Dựa trên câu chuyện có thật về hai nhà ngoại cảm Ed và Lorraine Warren điều tra vụ ám ảnh kinh hoàng tại một trang trại ở Rhode Island.',
 'conjuring.jpg', 'conjuring.mp4', 2013, 'Kinh dị', 9200),

('Your Name',
 'Hai học sinh trung học ở các vùng khác nhau của Nhật Bản kỳ lạ thay đổi cơ thể cho nhau qua những giấc mơ và dần nảy sinh tình cảm.',
 'yourname.jpg', 'yourname.mp4', 2016, 'Tình cảm', 11500);
