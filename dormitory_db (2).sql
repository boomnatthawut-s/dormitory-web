-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2026 at 07:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dormitory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(10) NOT NULL,
  `admin_username` varchar(50) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `admin_fname` varchar(50) DEFAULT NULL,
  `admin_lname` varchar(50) DEFAULT NULL,
  `admin_phone` varchar(10) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_password`, `admin_fname`, `admin_lname`, `admin_phone`, `admin_email`) VALUES
('ADM01', 'admin', '123', 'วิชัย', 'บริหาร', '0801111111', 'admin1@dorm.com'),
('ADM02', 'admin2', '123', 'นารี', 'จัดการ', '0802222222', 'admin2@dorm.com'),
('ADM03', 'admin3', '123', 'กิตติ', 'ดูแล', '0803333333', 'admin3@dorm.com'),
('ADM04', 'admin4', '123', 'สุดา', 'หอพัก', '0804444444', 'admin4@dorm.com'),
('ADM05', 'admin5', '123', 'ธนวัฒน์', 'สุขสบาย', '0805555555', 'admin5@dorm.com'),
('ADM06', 'admin6', '123', 'ภัทร', 'วงศ์ใหญ่', '0806666666', 'admin6@dorm.com'),
('ADM07', 'admin7', '123', 'ศิริพร', 'งามสม', '0807777777', 'admin7@dorm.com'),
('ADM08', 'admin8', '123', 'เอกชัย', 'ร่ำรวย', '0808888888', 'admin8@dorm.com'),
('ADM09', 'admin9', '123', 'จันทرا', 'ใจดี', '0809999999', 'admin9@dorm.com'),
('ADM10', 'admin10', '123', 'มงคล', 'มั่นคง', '0800000001', 'admin10@dorm.com');

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `bill_id` varchar(10) NOT NULL,
  `bill_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `room_rent` decimal(10,2) DEFAULT NULL,
  `electric_total` decimal(10,2) DEFAULT NULL,
  `water_total` decimal(10,2) DEFAULT NULL,
  `penalty_total` decimal(10,2) DEFAULT NULL,
  `net_total` decimal(10,2) DEFAULT NULL,
  `bill_status` varchar(20) DEFAULT NULL,
  `record_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill`
--

INSERT INTO `bill` (`bill_id`, `bill_date`, `due_date`, `room_rent`, `electric_total`, `water_total`, `penalty_total`, `net_total`, `bill_status`, `record_id`) VALUES
('BIL01', '2026-08-02', '2026-08-10', 3500.00, 700.00, 180.00, 0.00, 4380.00, 'paid', 'REC01'),
('BIL02', '2026-08-02', '2026-08-10', 3500.00, 840.00, 200.00, 0.00, 4540.00, 'pending', 'REC02'),
('BIL03', '2026-08-02', '2026-08-10', 3500.00, 770.00, 150.00, 0.00, 4420.00, 'pending', 'REC03'),
('BIL04', '2026-08-02', '2026-08-10', 3500.00, 910.00, 220.00, 50.00, 4680.00, 'pending', 'REC04'),
('BIL05', '2026-08-02', '2026-08-10', 3500.00, 630.00, 160.00, 0.00, 4290.00, 'paid', 'REC05'),
('BIL06', '2026-08-02', '2026-08-10', 2500.00, 560.00, 130.00, 0.00, 3190.00, 'paid', 'REC06'),
('BIL07', '2026-08-02', '2026-08-10', 2500.00, 490.00, 140.00, 0.00, 3130.00, 'pending', 'REC07'),
('BIL08', '2026-08-02', '2026-08-10', 2500.00, 700.00, 190.00, 0.00, 3390.00, 'paid', 'REC08'),
('BIL09', '2026-08-02', '2026-08-10', 4000.00, 1050.00, 250.00, 0.00, 5300.00, 'paid', 'REC09'),
('BIL10', '2026-08-02', '2026-08-10', 4000.00, 980.00, 210.00, 100.00, 5290.00, 'pending', 'REC10'),
('BIL11', '2026-09-26', '2026-10-05', 2800.00, 0.00, 180.00, 0.00, 2980.00, 'pending', 'REC11'),
('BIL12', '2026-09-26', '2026-10-05', 3200.00, 80.00, 120.00, 0.00, 3400.00, 'paid', 'REC12'),
('BIL13', '2026-09-26', '2026-09-20', 3200.00, 320.00, 120.00, 350.00, 3990.00, 'paid', 'REC13'),
('BIL14', '2026-09-27', '2026-10-05', 3200.00, 176.00, 120.00, 0.00, 3496.00, 'pending', 'REC14');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `contract_id` varchar(10) NOT NULL,
  `signed_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `deposit_total` decimal(10,2) DEFAULT NULL,
  `contract_status` varchar(20) DEFAULT NULL,
  `room_id` varchar(10) DEFAULT NULL,
  `tenant_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`contract_id`, `signed_date`, `start_date`, `end_date`, `deposit_total`, `contract_status`, `room_id`, `tenant_id`) VALUES
('', '2026-09-27', '2026-09-27', '2027-09-27', 5000.00, 'pending', 'A04', 'T011'),
('CNT01', '2026-01-01', '2026-01-01', '2027-01-01', 7000.00, 'active', 'A01', 'T001'),
('CNT02', '2026-01-05', '2026-01-05', '2027-01-05', 7000.00, 'active', 'A03', 'T002'),
('CNT03', '2026-02-01', '2026-02-01', '2027-02-01', 7000.00, 'active', 'A05', 'T003'),
('CNT04', '2026-02-10', '2026-02-10', '2027-02-10', 5000.00, 'active', 'B01', 'T004'),
('CNT05', '2026-03-01', '2026-03-01', '2027-03-01', 5000.00, 'active', 'B03', 'T005'),
('CNT06', '2026-03-15', '2026-03-15', '2027-03-15', 8000.00, 'active', 'C01', 'T006'),
('CNT07', '2025-01-01', '2025-01-01', '2026-01-01', 7000.00, 'terminated', 'A02', 'T007'),
('CNT08', '2025-02-01', '2025-02-01', '2026-02-01', 7000.00, 'terminated', 'A04', 'T008'),
('CNT09', '2025-03-01', '2025-03-01', '2026-03-01', 5000.00, 'terminated', 'B02', 'T009'),
('CNT10', '2025-04-01', '2025-04-01', '2026-04-01', 8000.00, 'terminated', 'C02', 'T010');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_request`
--

CREATE TABLE `maintenance_request` (
  `request_id` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `issue_image` varchar(255) DEFAULT NULL,
  `preferred_time` varchar(50) DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `spare_part_cost` decimal(10,2) DEFAULT NULL,
  `repair_status` varchar(20) DEFAULT NULL,
  `tenant_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_request`
--

INSERT INTO `maintenance_request` (`request_id`, `description`, `issue_image`, `preferred_time`, `request_date`, `spare_part_cost`, `repair_status`, `tenant_id`) VALUES
('REQ01', 'แอร์ไม่เย็น', 'img1.jpg', 'ช่วงบ่าย', '2026-08-01', 150.00, 'เสร็จสิ้น', 'T001'),
('REQ02', 'หลอดไฟห้องน้ำขาด', 'img2.jpg', 'ช่วงเช้า', '2026-08-05', 50.00, 'เสร็จสิ้น', 'T002'),
('REQ03', 'ก๊อกน้ำรั่ว', 'img3.jpg', 'สะดวกตลอด', '2026-08-10', 80.00, 'กำลังดำเนินการ', 'T003'),
('REQ04', 'พัดลมเพดานมีเสียงดัง', 'img4.jpg', 'ช่วงเย็น', '2026-08-12', 0.00, 'รอคิวช่าง', 'T004'),
('REQ05', 'ลูกบิดประตูห้องนอนฝืด', 'img5.jpg', 'ช่วงบ่าย', '2026-08-15', 120.00, 'รอดำเนินการ', 'T005'),
('REQ06', 'ระบายน้ำในห้องน้ำช้า', 'img6.jpg', 'ช่วงเช้า', '2026-08-18', 0.00, 'รอดำเนินการ', 'T006'),
('REQ07', 'ปลั๊กไฟชำรุด', 'img7.jpg', 'ช่วงบ่าย', '2026-08-20', 60.00, 'เสร็จสิ้น', 'T007'),
('REQ08', 'หน้าต่างปิดไม่สนิท', 'img8.jpg', 'ช่วงเช้า', '2026-08-22', 0.00, 'รอคิวช่าง', 'T008'),
('REQ09', 'โคมไฟโต๊ะทำงานเสีย', 'img9.jpg', 'ช่วงเย็น', '2026-08-25', 90.00, 'กำลังดำเนินการ', 'T009'),
('REQ10', 'กลอนประตูระเบียงหลวม', 'img10.jpg', 'ช่วงบ่าย', '2026-08-28', 40.00, 'รอดำเนินการ', 'T010'),
('REQ11', 'test', 'issue_1790483616.webp', 'วันเสาร์ เช้า', '2026-09-27', 999.00, 'เสร็จสิ้น', 'T001'),
('REQ12', 'testtt', 'issue_1790484854.jpg', 'ตอนไหนก้ได้', '2026-09-27', NULL, 'รอดำเนินการ', 'T001');

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `parent_id` varchar(10) NOT NULL,
  `tenant_id` varchar(10) NOT NULL,
  `parent_fname` varchar(50) DEFAULT NULL,
  `parent_lname` varchar(50) DEFAULT NULL,
  `relation_tenant` varchar(50) DEFAULT NULL,
  `parent_phone` varchar(10) DEFAULT NULL,
  `parent_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parent_id`, `tenant_id`, `parent_fname`, `parent_lname`, `relation_tenant`, `parent_phone`, `parent_address`) VALUES
('P001', 'T001', 'ประเสริฐ', 'ใจดี', 'พ่อ', '0810000001', '123 ถนนสุขุมวิท กรุงเทพฯ'),
('P002', 'T002', 'มาลี', 'รักเรียน', 'แม่', '0810000002', '45 ถนนพหลโยธิน กรุงเทพฯ'),
('P003', 'T003', 'วิรัช', 'มั่นคง', 'พ่อ', '0810000003', '78 ถนนมิตรภาพ ขอนแก่น'),
('P004', 'T004', 'สมศรี', 'มานะ', 'แม่', '0810000004', '99 ถนนเพชรเกษม นครปฐม'),
('P005', 'T005', 'บัญชา', 'กล้าหาญ', 'พ่อ', '0810000005', '12 ถนนนิมมานเหมินท์ เชียงใหม่'),
('P006', 'T006', 'ดวงใจ', 'เรืองรอง', 'แม่', '0810000006', '55 ถนนศรีโสธร ฉะเชิงเทรา'),
('P007', 'T007', 'สมเกียรติ', 'วงษ์สว่าง', 'พ่อ', '0810000007', '88 ถนนสุขสวัสดิ์ สมุทรปราการ'),
('P008', 'T008', 'ลำไย', 'พรหมมา', 'แม่', '0810000008', '33 ถนนสุรนารี นครราชสีมา'),
('P009', 'T009', 'ธนา', 'ดีงาม', 'พ่อ', '0810000009', '67 ถนนศรีจันทร์ ขอนแก่น'),
('P010', 'T010', 'นงเยาว์', 'แจ่มใส', 'แม่', '0810000010', '101 ถนนเพชรบุรี กรุงเทพฯ');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` varchar(10) NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `total_paid` decimal(10,2) DEFAULT NULL,
  `slip_image` varchar(255) DEFAULT NULL,
  `verify_status` varchar(20) DEFAULT NULL,
  `bill_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `payment_date`, `total_paid`, `slip_image`, `verify_status`, `bill_id`) VALUES
('PAY01', '2026-08-03 10:30:00', 4380.00, 'slip01.jpg', 'verified', 'BIL01'),
('PAY02', '2026-08-04 11:15:00', 4420.00, 'slip03.jpg', 'verified', 'BIL03'),
('PAY03', '2026-08-05 14:20:00', 4290.00, 'slip05.jpg', 'verified', 'BIL05'),
('PAY04', '2026-08-06 09:00:00', 3190.00, 'slip06.jpg', 'verified', 'BIL06'),
('PAY05', '2026-08-07 16:45:00', 3390.00, 'slip08.jpg', 'verified', 'BIL08'),
('PAY06', '2026-08-08 12:10:00', 5300.00, 'slip09.jpg', 'verified', 'BIL09'),
('PAY07', '2026-08-05 15:30:00', 4540.00, 'slip02.jpg', 'pending', 'BIL02'),
('PAY08', '2026-08-06 13:00:00', 4680.00, 'slip04.jpg', 'pending', 'BIL04'),
('PAY09', '2026-08-07 11:20:00', 3130.00, 'slip07.jpg', 'pending', 'BIL07'),
('PAY10', '2026-08-08 17:00:00', 5290.00, 'slip10.jpg', 'pending', 'BIL10'),
('PAY11', '2026-09-27 05:52:04', 3990.00, 'slip_1790481124.png', 'verified', 'BIL13');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` varchar(10) NOT NULL,
  `floor` int(11) DEFAULT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `monthly_rent` decimal(10,2) DEFAULT NULL,
  `room_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `floor`, `room_type`, `monthly_rent`, `room_status`) VALUES
('A01', 1, 'ห้องแอร์', 3200.00, 'ไม่ว่าง'),
('A02', 1, 'ห้องแอร์', 3200.00, 'ว่าง'),
('A03', 1, 'ห้องแอร์', 3200.00, 'ไม่ว่าง'),
('A04', 1, 'ห้องแอร์', 3200.00, 'ว่าง'),
('A05', 1, 'ห้องแอร์', 3200.00, 'ไม่ว่าง'),
('B01', 2, 'ห้องพัดลม', 2800.00, 'ไม่ว่าง'),
('B02', 2, 'ห้องพัดลม', 2800.00, 'ว่าง'),
('B03', 2, 'ห้องพัดลม', 2800.00, 'ไม่ว่าง'),
('C01', 3, 'ห้องพัดลม', 2800.00, 'ไม่ว่าง'),
('C02', 3, 'ห้องพัดลม', 2800.00, 'ว่าง');

-- --------------------------------------------------------

--
-- Table structure for table `room_asset`
--

CREATE TABLE `room_asset` (
  `asset_id` varchar(10) NOT NULL,
  `room_id` varchar(10) NOT NULL,
  `asset_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `asset_condition` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_asset`
--

INSERT INTO `room_asset` (`asset_id`, `room_id`, `asset_name`, `quantity`, `asset_condition`) VALUES
('167', 'B02', 'ตู้เสื้อผ้า', 1, 'ปกติ'),
('168', 'B03', 'ตู้เสื้อผ้า', 1, 'ปกติ'),
('176', 'C01', 'ตู้เสื้อผ้า', 1, 'ปกติ'),
('177', 'C02', 'ตู้เสื้อผ้า', 1, 'ปกติ'),
('187', 'A01', 'เครื่องปรับอากาศ', 1, 'ปกติ'),
('188', 'A02', 'เครื่องปรับอากาศ', 1, 'ปกติ'),
('189', 'A03', 'เครื่องปรับอากาศ', 1, 'ปกติ'),
('190', 'A04', 'เครื่องปรับอากาศ', 1, 'ปกติ'),
('191', 'A05', 'เครื่องปรับอากาศ', 1, 'ปกติ'),
('2', 'A02', 'เตียง 5 ฟุต', 1, 'ปกติ'),
('21', 'C01', 'เตียง 5 ฟุต', 1, 'ปกติ'),
('22', 'C02', 'เตียง 5 ฟุต', 1, 'ปกติ'),
('3', 'A03', 'เตียง 5 ฟุต', 1, 'ปกติ'),
('32', 'A01', 'พัดลมติดผนัง', 1, 'ปกติ'),
('33', 'A02', 'พัดลมติดผนัง', 1, 'ปกติ'),
('34', 'A03', 'พัดลมติดผนัง', 1, 'ปกติ'),
('35', 'A04', 'พัดลมติดผนัง', 1, 'ปกติ'),
('36', 'A05', 'พัดลมติดผนัง', 1, 'ปกติ'),
('4', 'A04', 'เตียง 5 ฟุต', 1, 'ปกติ'),
('42', 'B01', 'พัดลมติดผนัง', 1, 'ปกติ'),
('43', 'B02', 'พัดลมติดผนัง', 1, 'ปกติ'),
('44', 'B03', 'พัดลมติดผนัง', 1, 'ปกติ'),
('5', 'A05', 'เตียง 5 ฟุต', 1, 'ปกติ'),
('52', 'C01', 'พัดลมติดผนัง', 1, 'ปกติ'),
('53', 'C02', 'พัดลมติดผนัง', 1, 'ปกติ'),
('63', 'A01', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('64', 'A02', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('65', 'A03', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('66', 'A04', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('67', 'A05', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('73', 'B01', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('74', 'B02', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('75', 'B03', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('83', 'C01', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('84', 'C02', 'เครื่องทำน้ำอุ่น', 1, 'ปกติ'),
('94', 'A01', 'ชักโครก', 1, 'ปกติ'),
('95', 'A02', 'ชักโครก', 1, 'ปกติ'),
('96', 'A03', 'ชักโครก', 1, 'ปกติ'),
('97', 'A04', 'ชักโครก', 1, 'ปกติ'),
('98', 'A05', 'ชักโครก', 1, 'ปกติ');

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `tenant_id` varchar(10) NOT NULL,
  `tenant_username` varchar(50) DEFAULT NULL,
  `tenant_password` varchar(255) DEFAULT NULL,
  `tenant_fname` varchar(50) DEFAULT NULL,
  `tenant_lname` varchar(50) DEFAULT NULL,
  `tenant_phone` varchar(10) DEFAULT NULL,
  `tenant_lineid` varchar(50) DEFAULT NULL,
  `tenant_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`tenant_id`, `tenant_username`, `tenant_password`, `tenant_fname`, `tenant_lname`, `tenant_phone`, `tenant_lineid`, `tenant_status`) VALUES
('T001', 'tenant', '123', 'สมชาย', 'ใจดี', '0811111111', 'line01', 'active'),
('T002', 'user02', 'password123', 'สมหญิง', 'รักเรียน', '0822222222', 'line02', 'active'),
('T003', 'user03', 'password123', 'มานพ', 'มั่นคง', '0833333333', 'line03', 'active'),
('T004', 'user04', 'password123', 'กานดา', 'มานะ', '0844444444', 'line04', 'active'),
('T005', 'user05', 'password123', 'วิชัย', 'กล้าหาญ', '0855555555', 'line05', 'active'),
('T006', 'user06', 'password123', 'สุชาดา', '1234', '0866666666', 'line06', 'active'),
('T007', 'user07', 'password123', 'อนุชา', 'วงษ์สว่าง', '0877777777', 'line07', 'active'),
('T008', 'user08', 'password123', 'รัตนา', 'พรหมมา', '0888888888', 'line08', 'active'),
('T009', 'user09', 'password123', 'ธนพล', 'ดีงาม', '0899999999', 'line09', 'active'),
('T010', 'user10', 'password123', 'นภา', 'แจ่มใส', '0800000000', 'line10', 'active'),
('T011', 'mickey', '123', 'น้องมิก', 'กี้เมาส์', '0895556666', '12344', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `utility_record`
--

CREATE TABLE `utility_record` (
  `record_id` varchar(10) NOT NULL,
  `electric_meter` decimal(10,2) DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `electric_rate` decimal(10,2) DEFAULT NULL,
  `water_rate` decimal(10,2) DEFAULT NULL,
  `room_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utility_record`
--

INSERT INTO `utility_record` (`record_id`, `electric_meter`, `record_date`, `electric_rate`, `water_rate`, `room_id`) VALUES
('REC01', 150.00, '2026-08-01', 8.00, 120.00, 'A01'),
('REC02', 200.00, '2026-08-01', 8.00, 120.00, 'A02'),
('REC03', 180.00, '2026-08-01', 8.00, 120.00, 'A03'),
('REC04', 220.00, '2026-08-01', 8.00, 120.00, 'A04'),
('REC05', 160.00, '2026-08-01', 8.00, 120.00, 'A05'),
('REC06', 130.00, '2026-08-01', 8.00, 120.00, 'B01'),
('REC07', 140.00, '2026-08-01', 8.00, 120.00, 'B02'),
('REC08', 190.00, '2026-08-01', 8.00, 120.00, 'B03'),
('REC09', 250.00, '2026-08-01', 8.00, 120.00, 'C01'),
('REC10', 210.00, '2026-08-01', 8.00, 120.00, 'C02'),
('REC11', 233.00, '2026-09-26', 7.00, 18.00, 'C01'),
('REC12', 160.00, '2026-09-26', 8.00, 18.00, 'A01'),
('REC13', 200.00, '2026-09-26', 8.00, 18.00, 'A01'),
('REC14', 222.00, '2026-09-27', 8.00, 18.00, 'A01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `record_id` (`record_id`);

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `maintenance_request`
--
ALTER TABLE `maintenance_request`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`parent_id`,`tenant_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `room_asset`
--
ALTER TABLE `room_asset`
  ADD PRIMARY KEY (`asset_id`,`room_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `tenant`
--
ALTER TABLE `tenant`
  ADD PRIMARY KEY (`tenant_id`);

--
-- Indexes for table `utility_record`
--
ALTER TABLE `utility_record`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `utility_record` (`record_id`);

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`),
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `tenant` (`tenant_id`);

--
-- Constraints for table `maintenance_request`
--
ALTER TABLE `maintenance_request`
  ADD CONSTRAINT `maintenance_request_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant` (`tenant_id`);

--
-- Constraints for table `parent`
--
ALTER TABLE `parent`
  ADD CONSTRAINT `parent_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant` (`tenant_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`bill_id`);

--
-- Constraints for table `room_asset`
--
ALTER TABLE `room_asset`
  ADD CONSTRAINT `room_asset_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);

--
-- Constraints for table `utility_record`
--
ALTER TABLE `utility_record`
  ADD CONSTRAINT `utility_record_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
