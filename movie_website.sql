-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 05, 2026 lúc 12:21 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `movie_website`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `showtime_id`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 3, 240000.00, 'paid', '2026-07-05 06:47:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_details`
--

CREATE TABLE `booking_details` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_details`
--

INSERT INTO `booking_details` (`id`, `booking_id`, `seat_id`, `price`) VALUES
(1, 1, 36, 80000.00),
(2, 1, 37, 80000.00),
(3, 1, 38, 80000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `image`, `video`, `year`, `category`, `views`, `created_at`) VALUES
(1, 'Avengers: Endgame', 'Sau sự kiện Thanos tiêu diệt một nửa sinh linh trong vũ trụ, các Avengers còn lại tập hợp để thực hiện kế hoạch cuối cùng nhằm đảo ngược hành động của hắn.', 'avengers.jpg', 'avengers.mp4', '2019', 'Hành động', 15806, '2026-07-05 06:44:08'),
(2, 'Spider-Man: No Way Home', 'Peter Parker đề nghị Tiến sĩ Strange xóa ký ức của mọi người về danh tính của mình, nhưng một phép thuật sai lầm đã mở ra đa vũ trụ đầy nguy hiểm.', 'spiderman.jpg', 'spiderman.mp4', '2021', 'Hành động', 14201, '2026-07-05 06:44:08'),
(3, 'Interstellar', 'Một nhóm phi hành gia du hành qua lỗ sâu băng không gian để tìm kiếm hành tinh mới có thể cứu nhân loại khỏi sự tuyệt chủng trên Trái Đất.', 'interstellar.jpg', 'interstellar.mp4', '2014', 'Khoa học viễn tưởng', 12500, '2026-07-05 06:44:08'),
(4, 'Titanic', 'Câu chuyện tình yêu bi thương giữa Jack và Rose trên con tàu sang trọng Titanic trong chuyến hành trình định mệnh vào năm 1912.', 'titanic.jpg', 'titanic.mp4', '1997', 'Tình cảm', 11000, '2026-07-05 06:44:08'),
(5, 'The Dark Knight', 'Batman đối mặt với Joker – kẻ hắc ám muốn nhấn chìm Gotham City vào hỗn loạn. Cuộc chiến giữa trật tự và vô chính phủ bắt đầu.', 'darkknight.jpg', 'darkknight.mp4', '2008', 'Hành động', 13400, '2026-07-05 06:44:08'),
(6, 'Inception', 'Dom Cobb là kẻ trộm kỳ tài có khả năng xâm nhập vào giấc mơ của người khác để đánh cắp bí mật. Anh nhận nhiệm vụ nguy hiểm nhất: cấy ghép ý tưởng.', 'inception.jpg', 'inception.mp4', '2010', 'Khoa học viễn tưởng', 10800, '2026-07-05 06:44:08'),
(7, 'Doraemon: Nobita và Mặt Trăng Thám Hiểm', 'Nobita và nhóm bạn cùng Doraemon bắt đầu cuộc phiêu lưu đến Mặt Trăng, nơi họ phát hiện ra một nền văn minh bí ẩn đang gặp nguy hiểm.', 'doraemon.jpg', 'doraemon.mp4', '2019', 'Hoạt hình', 9501, '2026-07-05 06:44:08'),
(8, 'Lật Mặt 7: Một Điều Ước', 'Bộ phim hài hành động Việt Nam kể về hành trình của một gia đình với những tình huống hài hước và cảm động, đầy ắp tiếng cười.', 'latmat7.jpg', 'latmat7.mp4', '2024', 'Hài', 8700, '2026-07-05 06:44:08'),
(9, 'The Conjuring', 'Dựa trên câu chuyện có thật về hai nhà ngoại cảm Ed và Lorraine Warren điều tra vụ ám ảnh kinh hoàng tại một trang trại ở Rhode Island.', 'conjuring.jpg', 'conjuring.mp4', '2013', 'Kinh dị', 9200, '2026-07-05 06:44:08'),
(10, 'Your Name', 'Hai học sinh trung học ở các vùng khác nhau của Nhật Bản kỳ lạ thay đổi cơ thể cho nhau qua những giấc mơ và dần nảy sinh tình cảm.', 'yourname.jpg', 'yourname.mp4', '2016', 'Tình cảm', 11500, '2026-07-05 06:44:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `movie_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 5, 'Nguyễn Gia Huy', 5, 'Phim quá hay! Hành động mãn nhãn.', '2026-07-05 06:47:24'),
(2, 3, 5, 'Minh Anh', 4, 'Nội dung hấp dẫn, diễn xuất tốt.', '2026-07-05 06:47:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `room` varchar(50) NOT NULL,
  `row_label` char(1) NOT NULL,
  `col_number` int(11) NOT NULL,
  `seat_type` enum('normal','vip') DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `seats`
--

INSERT INTO `seats` (`id`, `room`, `row_label`, `col_number`, `seat_type`) VALUES
(1, 'Phòng 05', 'A', 1, 'normal'),
(2, 'Phòng 05', 'A', 2, 'normal'),
(3, 'Phòng 05', 'A', 3, 'normal'),
(4, 'Phòng 05', 'A', 4, 'normal'),
(5, 'Phòng 05', 'A', 5, 'normal'),
(6, 'Phòng 05', 'A', 6, 'normal'),
(7, 'Phòng 05', 'A', 7, 'normal'),
(8, 'Phòng 05', 'A', 8, 'normal'),
(9, 'Phòng 05', 'A', 9, 'normal'),
(10, 'Phòng 05', 'A', 10, 'normal'),
(11, 'Phòng 05', 'B', 1, 'normal'),
(12, 'Phòng 05', 'B', 2, 'normal'),
(13, 'Phòng 05', 'B', 3, 'normal'),
(14, 'Phòng 05', 'B', 4, 'normal'),
(15, 'Phòng 05', 'B', 5, 'normal'),
(16, 'Phòng 05', 'B', 6, 'normal'),
(17, 'Phòng 05', 'B', 7, 'normal'),
(18, 'Phòng 05', 'B', 8, 'normal'),
(19, 'Phòng 05', 'B', 9, 'normal'),
(20, 'Phòng 05', 'B', 10, 'normal'),
(21, 'Phòng 05', 'C', 1, 'normal'),
(22, 'Phòng 05', 'C', 2, 'normal'),
(23, 'Phòng 05', 'C', 3, 'normal'),
(24, 'Phòng 05', 'C', 4, 'normal'),
(25, 'Phòng 05', 'C', 5, 'normal'),
(26, 'Phòng 05', 'C', 6, 'normal'),
(27, 'Phòng 05', 'C', 7, 'normal'),
(28, 'Phòng 05', 'C', 8, 'normal'),
(29, 'Phòng 05', 'C', 9, 'normal'),
(30, 'Phòng 05', 'C', 10, 'normal'),
(31, 'Phòng 05', 'D', 1, 'normal'),
(32, 'Phòng 05', 'D', 2, 'normal'),
(33, 'Phòng 05', 'D', 3, 'normal'),
(34, 'Phòng 05', 'D', 4, 'normal'),
(35, 'Phòng 05', 'D', 5, 'normal'),
(36, 'Phòng 05', 'D', 6, 'normal'),
(37, 'Phòng 05', 'D', 7, 'normal'),
(38, 'Phòng 05', 'D', 8, 'normal'),
(39, 'Phòng 05', 'D', 9, 'normal'),
(40, 'Phòng 05', 'D', 10, 'normal'),
(41, 'Phòng 05', 'E', 1, 'normal'),
(42, 'Phòng 05', 'E', 2, 'normal'),
(43, 'Phòng 05', 'E', 3, 'normal'),
(44, 'Phòng 05', 'E', 4, 'normal'),
(45, 'Phòng 05', 'E', 5, 'normal'),
(46, 'Phòng 05', 'E', 6, 'normal'),
(47, 'Phòng 05', 'E', 7, 'normal'),
(48, 'Phòng 05', 'E', 8, 'normal'),
(49, 'Phòng 05', 'E', 9, 'normal'),
(50, 'Phòng 05', 'E', 10, 'normal'),
(51, 'Phòng 05', 'F', 1, 'normal'),
(52, 'Phòng 05', 'F', 2, 'normal'),
(53, 'Phòng 05', 'F', 3, 'normal'),
(54, 'Phòng 05', 'F', 4, 'normal'),
(55, 'Phòng 05', 'F', 5, 'normal'),
(56, 'Phòng 05', 'F', 6, 'normal'),
(57, 'Phòng 05', 'F', 7, 'normal'),
(58, 'Phòng 05', 'F', 8, 'normal'),
(59, 'Phòng 05', 'F', 9, 'normal'),
(60, 'Phòng 05', 'F', 10, 'normal'),
(61, 'Phòng 05', 'G', 1, 'normal'),
(62, 'Phòng 05', 'G', 2, 'normal'),
(63, 'Phòng 05', 'G', 3, 'normal'),
(64, 'Phòng 05', 'G', 4, 'normal'),
(65, 'Phòng 05', 'G', 5, 'normal'),
(66, 'Phòng 05', 'G', 6, 'normal'),
(67, 'Phòng 05', 'G', 7, 'normal'),
(68, 'Phòng 05', 'G', 8, 'normal'),
(69, 'Phòng 05', 'G', 9, 'normal'),
(70, 'Phòng 05', 'G', 10, 'normal'),
(71, 'Phòng 05', 'H', 1, 'vip'),
(72, 'Phòng 05', 'H', 2, 'vip'),
(73, 'Phòng 05', 'H', 3, 'vip'),
(74, 'Phòng 05', 'H', 4, 'vip'),
(75, 'Phòng 05', 'H', 5, 'vip'),
(76, 'Phòng 05', 'H', 6, 'vip'),
(77, 'Phòng 05', 'H', 7, 'vip'),
(78, 'Phòng 05', 'H', 8, 'vip'),
(79, 'Phòng 05', 'H', 9, 'vip'),
(80, 'Phòng 05', 'H', 10, 'vip'),
(81, 'Phòng 05', 'I', 1, 'vip'),
(82, 'Phòng 05', 'I', 2, 'vip'),
(83, 'Phòng 05', 'I', 3, 'vip'),
(84, 'Phòng 05', 'I', 4, 'vip'),
(85, 'Phòng 05', 'I', 5, 'vip'),
(86, 'Phòng 05', 'I', 6, 'vip'),
(87, 'Phòng 05', 'I', 7, 'vip'),
(88, 'Phòng 05', 'I', 8, 'vip'),
(89, 'Phòng 05', 'I', 9, 'vip'),
(90, 'Phòng 05', 'I', 10, 'vip');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `cinema` varchar(100) NOT NULL,
  `room` varchar(50) NOT NULL,
  `format` varchar(20) DEFAULT '2D Phụ đề',
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 80000.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `cinema`, `room`, `format`, `show_date`, `show_time`, `price`) VALUES
(1, 5, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-01', '10:00:00', 80000.00),
(2, 5, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-01', '16:00:00', 80000.00),
(3, 5, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-01', '19:00:00', 80000.00),
(4, 5, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-01', '22:00:00', 80000.00),
(5, 5, 'Galaxy Nguyễn Du', 'Phòng 02', '2D Phụ đề', '2026-07-01', '10:30:00', 75000.00),
(6, 1, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(7, 2, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(8, 3, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(9, 4, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(10, 5, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(11, 6, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(12, 7, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(13, 8, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(14, 9, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(15, 10, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '14:00:00', 80000.00),
(21, 1, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(22, 2, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(23, 3, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(24, 4, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(25, 5, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(26, 6, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(27, 7, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(28, 8, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(29, 9, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00),
(30, 10, 'CGV Vincom', 'Phòng 05', '2D Phụ đề', '2026-07-02', '19:00:00', 80000.00);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `showtime_id` (`showtime_id`);

--
-- Chỉ mục cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Chỉ mục cho bảng `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Chỉ mục cho bảng `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seat` (`room`,`row_label`,`col_number`);

--
-- Chỉ mục cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`);

--
-- Các ràng buộc cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`);

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
