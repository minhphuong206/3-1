-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th1 03, 2026 lúc 05:26 PM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `phuongstore`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `ma_admin` int NOT NULL AUTO_INCREMENT,
  `ten_dang_nhap` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `anh_admin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'logo.png',
  PRIMARY KEY (`ma_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`ma_admin`, `ten_dang_nhap`, `mat_khau`, `ho_ten`, `anh_admin`) VALUES
(1, 'admin', '123456', 'Quản Trị Viên Chính', 'logo.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `anhsanpham`
--

DROP TABLE IF EXISTS `anhsanpham`;
CREATE TABLE IF NOT EXISTS `anhsanpham` (
  `ma_anh` int NOT NULL AUTO_INCREMENT,
  `ma_san_pham` int NOT NULL,
  `url_anh` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `la_anh_chinh` tinyint(1) DEFAULT '0',
  `mau_sac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_bien_the` int DEFAULT NULL,
  PRIMARY KEY (`ma_anh`),
  KEY `ma_san_pham` (`ma_san_pham`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `anhsanpham`
--

INSERT INTO `anhsanpham` (`ma_anh`, `ma_san_pham`, `url_anh`, `la_anh_chinh`, `mau_sac`, `ma_bien_the`) VALUES
(2, 1, 'iphone15pro.png', 1, 'Cam', NULL),
(3, 2, 'laptopacer.png', 1, 'Đen Đỏ', NULL),
(4, 3, 'laptoplenovo.png', 1, 'Storm Grey', NULL),
(5, 4, 'laptopdell.png', 1, 'Platinum Silver', NULL),
(6, 1, 'iphone17promaxbac.png', 0, 'Bac', NULL),
(7, 1, 'iphone17promaxxam.png', 0, 'Xam', NULL),
(8, 1, 'iphone17promax2.png', 0, NULL, NULL),
(9, 1, 'iphone17promax3.png', 0, NULL, NULL),
(10, 1, 'iphone17promax4.png', 0, NULL, NULL),
(11, 5, 'asus_vivos.png', 1, NULL, NULL),
(12, 6, 'msi_katana.png', 1, NULL, NULL),
(13, 7, 'hp_pavilion.png', 1, NULL, NULL),
(14, 8, 'gigabyte_g5.png', 1, NULL, NULL),
(15, 9, 'macbook_m3.png', 1, NULL, NULL),
(16, 14, 'iphone16promax.png', 1, NULL, NULL),
(17, 15, 's24ultra.png', 1, NULL, NULL),
(18, 17, 'zfold6.png', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bienthesanpham`
--

DROP TABLE IF EXISTS `bienthesanpham`;
CREATE TABLE IF NOT EXISTS `bienthesanpham` (
  `ma_bien_the` int NOT NULL AUTO_INCREMENT,
  `ma_san_pham` int NOT NULL,
  `mau_sac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dung_luong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia_ban` decimal(15,0) NOT NULL,
  `muc_giam_gia` int DEFAULT '0' COMMENT 'Phần trăm giảm (VD: 10 là 10%)',
  `so_luong_ton` int DEFAULT '0',
  PRIMARY KEY (`ma_bien_the`),
  KEY `ma_san_pham` (`ma_san_pham`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bienthesanpham`
--

INSERT INTO `bienthesanpham` (`ma_bien_the`, `ma_san_pham`, `mau_sac`, `ram`, `dung_luong`, `gia_ban`, `muc_giam_gia`, `so_luong_ton`) VALUES
(2, 2, 'Đen Đỏ', NULL, '8GB/512GB', 19990000, 50, 50),
(3, 3, 'Storm Grey', NULL, '32GB/512GB', 28990000, 0, 20),
(4, 4, 'Platinum Silver', NULL, '16GB/1TB', 23490000, 0, 30),
(5, 1, 'Cam', NULL, '256GB', 37990000, 100, 20),
(6, 1, 'Bạc', NULL, '256GB', 37990000, 100, 20),
(7, 1, 'Xám', NULL, '256GB', 37990000, 100, 19),
(22, 1, 'Cam', NULL, '512GB', 37990000, 100, 20),
(23, 1, 'Bạc', NULL, '512GB', 37990000, 100, 20),
(24, 1, 'Xám', NULL, '512GB', 37990000, 100, 10),
(25, 5, 'Bạc', NULL, '16GB/512GB', 15990000, 10, 20),
(26, 6, 'Đen', NULL, '16GB/512GB', 24990000, 15, 15),
(27, 7, 'Vàng', NULL, '8GB/512GB', 14500000, 5, 30),
(28, 8, 'Đen', NULL, '16GB/512GB', 18990000, 20, 10),
(29, 9, 'Xám', NULL, '8GB/256GB', 27990000, 0, 25),
(30, 14, 'Titan Sa Mạc', NULL, '256GB', 34990000, 100, 20),
(31, 15, 'Xám Titan', NULL, '256GB', 29990000, 15, 15),
(32, 17, 'Xám', NULL, '256GB', 41990000, 20, 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
CREATE TABLE IF NOT EXISTS `chitietdonhang` (
  `ma_ct_don` int NOT NULL AUTO_INCREMENT,
  `ma_don_hang` int NOT NULL,
  `ma_bien_the` int DEFAULT NULL,
  `so_luong` int NOT NULL,
  `don_gia` decimal(15,0) NOT NULL,
  PRIMARY KEY (`ma_ct_don`),
  KEY `ma_don_hang` (`ma_don_hang`),
  KEY `ma_bien_the` (`ma_bien_the`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`ma_ct_don`, `ma_don_hang`, `ma_bien_the`, `so_luong`, `don_gia`) VALUES
(1, 1, 2, 4, 9995000),
(3, 3, 5, 1, 3799000),
(4, 4, 5, 1, 3799000),
(5, 5, 5, 1, 3799000),
(6, 6, 5, 1, 3799000),
(7, 7, 5, 1, 3799000),
(8, 8, 5, 1, 3799000),
(9, 9, 5, 1, 3799000),
(10, 10, 2, 1, 9995000),
(11, 11, 5, 2, 379900),
(12, 12, 5, 1, 379900),
(13, 13, 2, 1, 9995000),
(14, 14, NULL, 1, 11111),
(15, 14, 30, 1, 0),
(16, 15, 7, 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietgiohang`
--

DROP TABLE IF EXISTS `chitietgiohang`;
CREATE TABLE IF NOT EXISTS `chitietgiohang` (
  `ma_ct_gio` int NOT NULL AUTO_INCREMENT,
  `ma_gio_hang` int NOT NULL,
  `ma_bien_the` int NOT NULL,
  `so_luong` int NOT NULL,
  PRIMARY KEY (`ma_ct_gio`),
  KEY `ma_gio_hang` (`ma_gio_hang`),
  KEY `ma_bien_the` (`ma_bien_the`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhgia`
--

DROP TABLE IF EXISTS `danhgia`;
CREATE TABLE IF NOT EXISTS `danhgia` (
  `ma_danh_gia` int NOT NULL AUTO_INCREMENT,
  `ma_khach_hang` int NOT NULL,
  `ma_san_pham` int NOT NULL,
  `so_sao` int DEFAULT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_danh_gia`),
  KEY `ma_khach_hang` (`ma_khach_hang`),
  KEY `ma_san_pham` (`ma_san_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

DROP TABLE IF EXISTS `danhmuc`;
CREATE TABLE IF NOT EXISTS `danhmuc` (
  `ma_danh_muc` int NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thu_tu` int NOT NULL DEFAULT '0',
  `hien_thi` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ma_danh_muc`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`ma_danh_muc`, `ten_danh_muc`, `thu_tu`, `hien_thi`) VALUES
(1, 'Điện thoại', 2, 1),
(2, 'Laptop', 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

DROP TABLE IF EXISTS `donhang`;
CREATE TABLE IF NOT EXISTS `donhang` (
  `ma_don_hang` int NOT NULL AUTO_INCREMENT,
  `ma_don_hang_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ma_khach_hang` int DEFAULT NULL,
  `ho_ten_nguoi_nhan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdt_nguoi_nhan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_dat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tong_tien` decimal(15,0) NOT NULL,
  `pt_thanh_toan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tt_thanh_toan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tt_don_hang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi_giao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ghi_chu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ma_gd_momo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ly_do_huy` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ma_admin_duyet` int DEFAULT NULL,
  PRIMARY KEY (`ma_don_hang`),
  KEY `ma_khach_hang` (`ma_khach_hang`),
  KEY `ma_admin_duyet` (`ma_admin_duyet`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`ma_don_hang`, `ma_don_hang_code`, `ma_khach_hang`, `ho_ten_nguoi_nhan`, `sdt_nguoi_nhan`, `ngay_dat`, `tong_tien`, `pt_thanh_toan`, `tt_thanh_toan`, `tt_don_hang`, `dia_chi_giao`, `ghi_chu`, `ma_gd_momo`, `ly_do_huy`, `ma_admin_duyet`) VALUES
(1, 'ORD_1764994491', NULL, 'as', '0369451564', '2025-12-06 11:14:51', 39980000, 'momo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(3, 'ORD_1764998252', NULL, 'as', '0369451564', '2025-12-06 12:17:32', 3799000, 'momo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(4, 'ORD_1764998261', NULL, 'as', '0369451564', '2025-12-06 12:17:41', 3799000, 'momo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(5, 'ORD_1764998332', NULL, 'as', '0369451564', '2025-12-06 12:18:52', 3799000, 'momo', 'Chưa thanh toán', 'Hoàn tất', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(6, 'ORD_1764998449', NULL, 'as', '0369451564', '2025-12-06 12:20:49', 3799000, 'momo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(7, 'DH1764999772776', NULL, 'as', '0369451564', '2025-12-06 12:42:52', 3799000, 'MoMo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(8, 'DH1765001258274', NULL, 'as', '0369451564', '2025-12-06 13:07:38', 3799000, 'MoMo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(9, 'ORD_1765002091', NULL, 'as', '0369451564', '2025-12-06 13:21:31', 3799000, 'momo', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(10, 'ORD_1766054546', NULL, 'as', '0369451564', '2025-12-18 17:42:26', 9995000, 'COD', 'Chưa thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(11, 'ORD_1766054688', NULL, 'phương', '0369451564', '2025-12-18 17:44:48', 759800, 'COD', 'Chưa thanh toán', 'Hoàn tất', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(12, 'ORD_1766058356', NULL, 'phương', '0369451564', '2025-12-18 18:46:23', 379900, 'MoMo', 'Đã thanh toán', 'Chờ duyệt', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(13, 'ORD_1766058901', NULL, 'phương', '0369451564', '2025-12-18 18:55:35', 9995000, 'MoMo', 'Đã thanh toán', 'Hoàn tất', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(14, 'ORD_1767436833', NULL, 'as', '0369451564', '2026-01-03 17:41:55', 11111, 'MoMo', 'Đã thanh toán', 'Hoàn tất', 'Nhận tại cửa hàng', '', NULL, NULL, NULL),
(15, 'ORD_1767439270', 8, 'as', '0369451564', '2026-01-03 18:21:10', 0, 'COD', 'Chưa thanh toán', 'Đang giao', 'Nhận tại cửa hàng', '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dulieuhuanluyen`
--

DROP TABLE IF EXISTS `dulieuhuanluyen`;
CREATE TABLE IF NOT EXISTS `dulieuhuanluyen` (
  `ma_du_lieu` int NOT NULL AUTO_INCREMENT,
  `ma_admin` int DEFAULT NULL,
  `cau_hoi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tra_loi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ma_du_lieu`),
  KEY `ma_admin` (`ma_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

DROP TABLE IF EXISTS `giohang`;
CREATE TABLE IF NOT EXISTS `giohang` (
  `ma_gio_hang` int NOT NULL AUTO_INCREMENT,
  `ma_khach_hang` int NOT NULL,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_gio_hang`),
  KEY `ma_khach_hang` (`ma_khach_hang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
CREATE TABLE IF NOT EXISTS `khachhang` (
  `ma_khach_hang` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdt` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bi_khoa` tinyint(1) DEFAULT '0',
  `token_khoi_phuc` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_admin_khoa` int DEFAULT NULL,
  `han_token` datetime DEFAULT NULL,
  PRIMARY KEY (`ma_khach_hang`),
  UNIQUE KEY `email` (`email`),
  KEY `ma_admin_khoa` (`ma_admin_khoa`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`ma_khach_hang`, `email`, `mat_khau`, `ho_ten`, `sdt`, `dia_chi`, `bi_khoa`, `token_khoi_phuc`, `ma_admin_khoa`, `han_token`) VALUES
(1, 'khach@gmail.com', '123456', 'Nguyễn Văn A', NULL, NULL, 1, NULL, NULL, NULL),
(2, 'tranhaitri92@gmail.com', '$2y$10$gxHLTpnY5Yk9Sd0PT4OqkO/SmJL1swdnlIpiu1iz.Mdub3qkme7Im', 'trần hải Trí', '0123456789', NULL, 1, NULL, NULL, NULL),
(8, 'pn2062004@gmail.com', '$2y$10$9I85sqBFGLAU6V.l/2PkeO6EC.hS3WkmYsbukENAFrLm7h7.NITau', 'minh phương', '0369451564', NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichsuchat`
--

DROP TABLE IF EXISTS `lichsuchat`;
CREATE TABLE IF NOT EXISTS `lichsuchat` (
  `ma_chat` int NOT NULL AUTO_INCREMENT,
  `ma_khach_hang` int DEFAULT NULL,
  `tin_nhan_khach` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phan_hoi_bot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thoi_gian` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_chat`),
  KEY `ma_khach_hang` (`ma_khach_hang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
CREATE TABLE IF NOT EXISTS `sanpham` (
  `ma_san_pham` int NOT NULL AUTO_INCREMENT,
  `ten_san_pham` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thong_so_ky_thuat` json DEFAULT NULL,
  `ma_danh_muc` int DEFAULT NULL,
  `ma_thuong_hieu` int DEFAULT NULL,
  `ma_admin_tao` int DEFAULT NULL,
  `kich_hoat` tinyint(1) DEFAULT '1',
  `tag` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kich_thuoc_man_hinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cong_nghe_man_hinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camera_sau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camera_truoc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chip_set` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfc` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `the_sim` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phan_giai_man_hinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trong_luong` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ma_san_pham`),
  KEY `ma_danh_muc` (`ma_danh_muc`),
  KEY `ma_thuong_hieu` (`ma_thuong_hieu`),
  KEY `ma_admin_tao` (`ma_admin_tao`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`ma_san_pham`, `ten_san_pham`, `mo_ta`, `thong_so_ky_thuat`, `ma_danh_muc`, `ma_thuong_hieu`, `ma_admin_tao`, `kich_hoat`, `tag`, `kich_thuoc_man_hinh`, `cong_nghe_man_hinh`, `camera_sau`, `camera_truoc`, `chip_set`, `nfc`, `pin`, `the_sim`, `phan_giai_man_hinh`, `cpu`, `trong_luong`) VALUES
(1, 'iPhone 17 Pro Max 256GB', 'hehehee', '{\"cpu\": \"Apple A19 Pro (3nm thế hệ 2)\", \"ram\": \"12GB LPDDR5X\", \"o_cung\": \"\", \"man_hinh\": \"\"}', 1, 1, NULL, 1, 'hot', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Laptop Gaming NitroV 15 ', 'Thiết kế đậm chất Gaming, tản nhiệt 2 quạt mát mẻ, bàn phím LED RGB.', '{\"cpu\": \"Intel Core i5-12500H\", \"ram\": \"8GB DDR4 3200MHz\", \"o_cung\": \"\", \"man_hinh\": \"\"}', 2, 2, NULL, 1, 'hot', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Laptop Lenovo Gaming LOQ 15IRX10 AI', 'Chiến binh AI Gaming thế hệ mới. Chip i7 dòng HX hiệu năng cao kết hợp card đồ họa RTX 5050 series.', '{\"cpu\": \"Intel Core i7-13650HX\", \"pin\": \"60Wh\", \"ram\": \"32GB DDR5 5600MHz\", \"vga\": \"NVIDIA GeForce RTX 5050 8GB\", \"o_cung\": \"512GB SSD NVMe PCIe Gen4\", \"man_hinh\": \"15.6 inch FHD 144Hz 100% sRGB\", \"cong_nghe\": \"Lenovo AI Engine+, MUX Switch\", \"trong_luong\": \"2.4 kg\"}', 2, 3, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Laptop Dell Inspiron 16 DC16250', 'Laptop văn phòng màn hình lớn 16 inch, tích hợp sẵn Windows 11 và Office Home 2024 bản quyền vĩnh viễn.', '{\"cpu\": \"Intel Core 5-120U (10 nhân, 12 luồng)\", \"ram\": \"16GB DDR5 5200MHz\", \"vga\": \"Intel Graphics\", \"o_cung\": \"1TB SSD NVMe PCIe\", \"vo_may\": \"Vỏ nhôm màu bạc (Platinum Silver)\", \"man_hinh\": \"16 inch FHD+ (1920 x 1200) Anti-Glare\", \"phan_mem\": \"Windows 11 + Office Home 2024 + Microsoft 365\", \"trong_luong\": \"1.87 kg\"}', 2, 4, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Laptop ASUS Vivobook 16', 'Laptop văn phòng mỏng nhẹ, màn hình 16 inch.', '{\"cpu\": \"Intel Core i5-1235U\", \"pin\": \"3-cell, 42Wh\", \"ram\": \"16GB DDR4\", \"vga\": \"Intel Iris Xe Graphics\", \"o_cung\": \"512GB SSD NVMe PCIe\", \"man_hinh\": \"16 inch WUXGA (1920 x 1200)\", \"trong_luong\": \"1.88 kg\"}', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Laptop MSI Katana 15', 'Laptop Gaming hiệu năng cao, card đồ họa RTX 4050.', '{\"cpu\": \"Intel Core i7-13620H\", \"pin\": \"3-cell, 53.5Wh\", \"ram\": \"16GB DDR5 5200MHz\", \"vga\": \"NVIDIA GeForce RTX 4050 6GB\", \"o_cung\": \"512GB SSD NVMe PCIe Gen4\", \"man_hinh\": \"15.6 inch FHD (1920 x 1080) 144Hz\", \"trong_luong\": \"2.25 kg\"}', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Laptop HP Pavilion 15', 'Thiết kế sang trọng, phù hợp sinh viên và văn phòng.', '{\"cpu\": \"Intel Core i5-1335U\", \"pin\": \"3-cell, 41Wh\", \"ram\": \"8GB DDR4 3200MHz\", \"vga\": \"Intel Iris Xe Graphics\", \"o_cung\": \"512GB SSD NVMe PCIe\", \"man_hinh\": \"15.6 inch FHD (1920 x 1080) IPS\", \"trong_luong\": \"1.74 kg\"}', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Laptop Gigabyte G5', 'Dòng laptop gaming giá rẻ nhưng cấu hình mạnh mẽ.', '{\"cpu\": \"Intel Core i5-12500H\", \"pin\": \"54Wh\", \"ram\": \"16GB DDR4 3200MHz\", \"vga\": \"NVIDIA GeForce RTX 4050 6GB\", \"o_cung\": \"512GB SSD NVMe PCIe\", \"man_hinh\": \"15.6 inch FHD 144Hz\", \"trong_luong\": \"1.9 kg\"}', 2, 1, NULL, 1, 'hot', '', '', '', '', '', '', '', '', '', '', ''),
(9, 'Laptop MacBook Air M3', 'Siêu mỏng nhẹ, pin cực lâu cho dân văn phòng.', '{\"cpu\": \"Apple M3 chip (8-core CPU)\", \"pin\": \"52.6Wh (Lên đến 18 giờ)\", \"ram\": \"8GB Unified Memory\", \"vga\": \"8-core GPU\", \"o_cung\": \"256GB SSD\", \"man_hinh\": \"13.6 inch Liquid Retina (2560 x 1664)\", \"trong_luong\": \"1.24 kg\"}', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'iPhone 16 Pro Max 256GB', 'Siêu phẩm mới nhất từ Apple với nút Camera Control và chip A18 Pro.', '{\"cpu\": \"Apple A18 Pro\", \"pin\": \"4685 mAh\", \"ram\": \"8GB\", \"man_hinh\": \"6.9 inch OLED\"}', 1, NULL, NULL, 1, 'hot', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Samsung Galaxy S24 Ultra', 'Flagship mạnh mẽ nhất của Samsung với bút S-Pen và AI tích hợp.', '{\"cpu\": \"Snapdragon 8 Gen 3\", \"pin\": \"5000 mAh\", \"ram\": \"12GB\", \"man_hinh\": \"6.8 inch Dynamic AMOLED\"}', 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Samsung Galaxy Z Fold 6', 'Điện thoại màn hình gập đỉnh cao, hỗ trợ đa nhiệm chuyên nghiệp.', '{\"cpu\": \"Snapdragon 8 Gen 3\", \"pin\": \"4400 mAh\", \"ram\": \"12GB\", \"man_hinh\": \"7.6 inch Foldable\"}', 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuonghieu`
--

DROP TABLE IF EXISTS `thuonghieu`;
CREATE TABLE IF NOT EXISTS `thuonghieu` (
  `ma_thuong_hieu` int NOT NULL AUTO_INCREMENT,
  `ten_thuong_hieu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ma_danh_muc` int DEFAULT NULL,
  PRIMARY KEY (`ma_thuong_hieu`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thuonghieu`
--

INSERT INTO `thuonghieu` (`ma_thuong_hieu`, `ten_thuong_hieu`, `ma_danh_muc`) VALUES
(1, 'Apple', 1),
(2, 'Acer', 2),
(3, 'Lenovo', 2),
(4, 'Dell', 2),
(5, 'SAMSUNG', 1);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `anhsanpham`
--
ALTER TABLE `anhsanpham`
  ADD CONSTRAINT `anhsanpham_ibfk_1` FOREIGN KEY (`ma_san_pham`) REFERENCES `sanpham` (`ma_san_pham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `bienthesanpham`
--
ALTER TABLE `bienthesanpham`
  ADD CONSTRAINT `bienthesanpham_ibfk_1` FOREIGN KEY (`ma_san_pham`) REFERENCES `sanpham` (`ma_san_pham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`ma_don_hang`) REFERENCES `donhang` (`ma_don_hang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`ma_bien_the`) REFERENCES `bienthesanpham` (`ma_bien_the`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD CONSTRAINT `chitietgiohang_ibfk_1` FOREIGN KEY (`ma_gio_hang`) REFERENCES `giohang` (`ma_gio_hang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietgiohang_ibfk_2` FOREIGN KEY (`ma_bien_the`) REFERENCES `bienthesanpham` (`ma_bien_the`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`ma_khach_hang`) REFERENCES `khachhang` (`ma_khach_hang`) ON DELETE CASCADE,
  ADD CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`ma_san_pham`) REFERENCES `sanpham` (`ma_san_pham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`ma_khach_hang`) REFERENCES `khachhang` (`ma_khach_hang`) ON DELETE SET NULL,
  ADD CONSTRAINT `donhang_ibfk_2` FOREIGN KEY (`ma_admin_duyet`) REFERENCES `admin` (`ma_admin`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `dulieuhuanluyen`
--
ALTER TABLE `dulieuhuanluyen`
  ADD CONSTRAINT `dulieuhuanluyen_ibfk_1` FOREIGN KEY (`ma_admin`) REFERENCES `admin` (`ma_admin`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`ma_khach_hang`) REFERENCES `khachhang` (`ma_khach_hang`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `khachhang_ibfk_1` FOREIGN KEY (`ma_admin_khoa`) REFERENCES `admin` (`ma_admin`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `lichsuchat`
--
ALTER TABLE `lichsuchat`
  ADD CONSTRAINT `lichsuchat_ibfk_1` FOREIGN KEY (`ma_khach_hang`) REFERENCES `khachhang` (`ma_khach_hang`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danhmuc` (`ma_danh_muc`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`ma_thuong_hieu`) REFERENCES `thuonghieu` (`ma_thuong_hieu`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanpham_ibfk_3` FOREIGN KEY (`ma_admin_tao`) REFERENCES `admin` (`ma_admin`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
