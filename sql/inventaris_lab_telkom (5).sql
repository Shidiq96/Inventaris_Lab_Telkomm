-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 07:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventaris_lab_telkom`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL,
  `status_sistem` enum('aktif','nonaktif') DEFAULT 'aktif',
  `ajaran_semester` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `status_sistem`, `ajaran_semester`) VALUES
(4, 'nonaktif', '2026/2027 Ganjil'),
(5, 'nonaktif', '2026/2027 Genap'),
(6, 'aktif', '2027/2028 Ganjil'),
(7, 'nonaktif', '2027/2028 Genap'),
(8, 'nonaktif', '2028/2029 Ganjil'),
(9, 'nonaktif', '2028/2029 Genap'),
(10, 'nonaktif', '2029/2030 Ganjil'),
(11, 'nonaktif', '2029/2030 Genap'),
(12, 'nonaktif', '2030/2031 Ganjil'),
(13, 'nonaktif', '2030/2031 Genap'),
(14, 'nonaktif', '2031/2032 Ganjil'),
(15, 'nonaktif', '2031/2032 Genap'),
(16, 'nonaktif', '2032/2033 Ganjil'),
(17, 'nonaktif', '2032/2033 Genap'),
(18, 'nonaktif', '2033/2034 Ganjil'),
(19, 'nonaktif', '2033/2034 Genap'),
(20, 'nonaktif', '2034/2035 Ganjil'),
(21, 'nonaktif', '2034/2035 Genap'),
(22, 'nonaktif', '2035/2036 Ganjil'),
(23, 'nonaktif', '2035/2036 Genap'),
(24, 'nonaktif', '2036/2037 Ganjil'),
(25, 'nonaktif', '2036/2037 Genap'),
(26, 'nonaktif', '2037/2038 Ganjil'),
(27, 'nonaktif', '2037/2038 Genap'),
(28, 'nonaktif', '2038/2039 Ganjil'),
(29, 'nonaktif', '2038/2039 Genap'),
(30, 'nonaktif', '2039/2040 Ganjil'),
(31, 'nonaktif', '2039/2040 Genap');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `nomor_unik` varchar(50) NOT NULL,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat','Perbaikan') DEFAULT 'Baik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `nomor_unik`, `kondisi`) VALUES
(983, 'Switch TP-Link TL-SF1005D', 'ST-001', 'Baik'),
(984, 'Switch TP-Link TL-SF1005D', 'ST-002', 'Baik'),
(985, 'Switch TP-Link TL-SF1005D', 'ST-003', 'Baik'),
(986, 'Switch TP-Link TL-SF1005D', 'ST-004', 'Baik'),
(987, 'Switch TP-Link TL-SF1005D', 'ST-005', 'Baik'),
(988, 'Switch TP-Link TL-SF1005D', 'ST-006', 'Baik'),
(989, 'Switch TP-Link TL-SF1008D', 'ST-001', 'Baik'),
(990, 'Switch TP-Link TL-SF1008D', 'ST-002', 'Baik'),
(991, 'Switch D-Link DES-1008D', 'SD-001', 'Baik'),
(992, 'Switch D-Link DES-1008D', 'SD-002', 'Baik'),
(994, 'Switch BL-S98', 'SB-001', 'Baik'),
(996, 'Link SYS WRT54GL', 'LS-001', 'Baik'),
(997, 'Link SYS WRT54GL', 'LS-002', 'Baik'),
(998, 'Link SYS WRT54GL', 'LS-003', 'Baik'),
(999, 'Link SYS WRT54GL', 'LS-004', 'Baik'),
(1000, 'Link SYS WRT54GL', 'LS-005', 'Baik'),
(1001, 'Link SYS WRT54GL', 'LS-006', 'Baik'),
(1002, 'Link SYS WRT54GL', 'LS-007', 'Baik'),
(1003, 'Link SYS WRT54GL', 'LS-008', 'Baik'),
(1004, 'Link SYS WRT54GL', 'LS-009', 'Baik'),
(1005, 'Link SYS WRT54GL', 'LS-010', 'Baik'),
(1006, 'Link SYS WRT54GL', 'LS-011', 'Baik'),
(1007, 'Link SYS WRT54GL', 'LS-012', 'Baik'),
(1008, 'Link SYS WRT54GL', 'LS-013', 'Baik'),
(1009, 'Link SYS WRT54GL', 'LS-014', 'Baik'),
(1010, 'Link SYS WRT54GL', 'LS-015', 'Baik'),
(1011, 'Link SYS WRT54GL', 'LS-016', 'Baik'),
(1012, 'Link SYS WRT54GL', 'LS-017', 'Baik'),
(1013, 'Link SYS WRT54GL', 'LS-018', 'Baik'),
(1014, 'Link SYS WRT54GL', 'LS-019', 'Baik'),
(1015, 'Link SYS WRT54GL', 'LS-020', 'Baik'),
(1016, 'Link SYS WRT54GL', 'LS-021', 'Baik'),
(1017, 'Link SYS WRT54GL', 'LS-022', 'Baik'),
(1018, 'Link SYS WRT54GL', 'LS-023', 'Baik'),
(1019, 'Link SYS WRT54GL', 'LS-024', 'Baik'),
(1020, 'Link SYS WRT54GL', 'LS-025', 'Baik'),
(1021, 'Link SYS WRT54GL', 'LS-026', 'Baik'),
(1022, 'Link SYS WRT54GL', 'LS-027', 'Baik'),
(1023, 'Link SYS WRT54GL', 'LS-028', 'Baik'),
(1024, 'Link SYS WRT54GL', 'LS-029', 'Baik'),
(1025, 'Link SYS WRT54GL', 'LS-030', 'Baik'),
(1026, 'Link SYS WRT54GL', 'LS-031', 'Baik'),
(1027, 'Link SYS WRT54GL', 'LS-032', 'Baik'),
(1028, 'Link SYS WRT54GL', 'LS-033', 'Baik'),
(1029, 'Link SYS WRT54GL', 'LS-034', 'Baik'),
(1030, 'Link SYS WRT54GL', 'LS-035', 'Baik'),
(1031, 'Link SYS WRT54GL', 'LS-036', 'Baik'),
(1032, 'Link SYS WRT54GL', 'LS-037', 'Baik'),
(1033, 'Link SYS WRT54GL', 'LS-038', 'Baik'),
(1034, 'Link SYS WRT54GL', 'LS-039', 'Baik'),
(1035, 'Link SYS WRT54GL', 'LS-040', 'Baik'),
(1036, 'Link SYS WRT54GL', 'LS-041', 'Baik'),
(1037, 'Link SYS WRT54GL', 'LS-042', 'Baik'),
(1038, 'Link SYS WRT54GL', 'LS-043', 'Baik'),
(1039, 'Link SYS WRT54GL', 'LS-044', 'Baik'),
(1040, 'Link SYS WRT54GL', 'LS-045', 'Baik'),
(1041, 'Link SYS WRT54GL', 'LS-046', 'Baik'),
(1042, 'Link SYS WRT54GL', 'LS-047', 'Baik'),
(1043, 'Link SYS WRT54GL', 'LS-048', 'Baik'),
(1044, 'Link SYS WRT54GL', 'LS-049', 'Baik'),
(1045, 'Link SYS WRT54GL', 'LS-050', 'Baik'),
(1046, 'Link SYS WRT54GL', 'LS-051', 'Baik'),
(1047, 'Link SYS WRT54GL', 'LS-052', 'Baik'),
(1048, 'Link SYS WRT54GL', 'LS-053', 'Baik'),
(1049, 'Link SYS WRT54GL', 'LS-054', 'Baik'),
(1050, 'Link SYS WRT54GL', 'LS-055', 'Baik'),
(1051, 'Link SYS WRT54GL', 'LS-056', 'Baik'),
(1052, 'Link SYS WRT54GL', 'LS-057', 'Baik'),
(1053, 'Link SYS WRT54GL', 'LS-058', 'Baik'),
(1054, 'Link SYS WRT54GL', 'LS-059', 'Baik'),
(1055, 'Link SYS WRT54GL', 'LS-060', 'Baik'),
(1056, 'Link SYS WRT54GL', 'LS-061', 'Baik'),
(1057, 'Link SYS WRT54GL', 'LS-062', 'Baik'),
(1058, 'Link SYS WRT54GL', 'LS-063', 'Baik'),
(1059, 'Link SYS WRT54GL', 'LS-064', 'Baik'),
(1060, 'Link SYS WRT54GL', 'LS-065', 'Baik'),
(1061, 'Link SYS WRT54GL', 'LS-066', 'Baik'),
(1062, 'Tang Crimping DL-686R', 'TC-001', 'Baik'),
(1063, 'Tang Crimping DL-686R', 'TC-002', 'Baik'),
(1064, 'Tang Crimping DL-686R', 'TC-003', 'Baik'),
(1065, 'Tang Crimping DL-686R', 'TC-004', 'Baik'),
(1066, 'Tang Crimping DL-686R', 'TC-005', 'Baik'),
(1067, 'Tang Crimping DL-686R', 'TC-006', 'Baik'),
(1068, 'Tang Crimping DL-686R', 'TC-007', 'Baik'),
(1069, 'Tang Crimping DL-686R', 'TC-008', 'Baik'),
(1070, 'Tang Crimping DL-686R', 'TC-009', 'Baik'),
(1071, 'Tang Crimping DL-686R', 'TC-010', 'Baik'),
(1072, 'Tang Crimping DL-686R', 'TC-011', 'Baik'),
(1073, 'Tang Crimping DL-686', 'TC-001', 'Baik'),
(1074, 'Tang Crimping DL-686', 'TC-002', 'Baik'),
(1075, 'Tang Crimping DL-686', 'TC-003', 'Baik'),
(1076, 'Tang Crimping DL-686', 'TC-004', 'Baik'),
(1077, 'Tang Crimping DL-686', 'TC-005', 'Baik'),
(1078, 'Tang Crimping DL-686', 'TC-006', 'Baik'),
(1079, 'Tang GBTOOL Hijau', 'TG-001', 'Baik'),
(1080, 'Tang GBTOOL Hijau', 'TG-002', 'Baik'),
(1081, 'Tang GBTOOL Hijau', 'TG-003', 'Baik'),
(1082, 'Tang GBTOOL Hijau', 'TG-004', 'Baik'),
(1083, 'Tang GBTOOL Hijau', 'TG-005', 'Baik'),
(1084, 'Tang GBTOOL Hijau', 'TG-006', 'Baik'),
(1085, 'Tang GBTOOL Hijau', 'TG-007', 'Baik'),
(1086, 'Tang GBTOOL Hijau', 'TG-008', 'Baik'),
(1087, 'Tang GBTOOL Hijau', 'TG-009', 'Baik'),
(1088, 'Tang GBTOOL Hijau', 'TG-010', 'Baik'),
(1089, 'Tang GBTOOL Hijau', 'TG-011', 'Baik'),
(1090, 'Tang GBTOOL Hijau', 'TG-012', 'Baik'),
(1158, 'Router Cisco 4200S/4221', 'RC-001', 'Baik'),
(1159, 'Router Cisco 4200S/4221', 'RC-002', 'Baik'),
(1160, 'Router Cisco 4300S/4321', 'RC-001', 'Baik'),
(1161, 'Router Cisco 4300S/4321', 'RC-002', 'Baik'),
(1162, 'Router Cisco Cataliyst 2960 Plus Series', 'RC-001', 'Baik'),
(1163, 'Router Cisco Cataliyst 2960 Plus Series', 'RC-002', 'Baik'),
(1164, 'Router Cisco Cataliyst 2960 Plus Series', 'RC-003', 'Baik'),
(1165, 'Router Cisco Cataliyst 2960 Plus Series', 'RC-004', 'Baik'),
(1166, 'Router Cisco Cataliyst 2960 Plus Series', 'RC-005', 'Baik'),
(1167, 'Router Cisco Cataliyst 2960 Plus Series', 'RC-006', 'Baik'),
(1168, 'Router TP-Link TL-SL2218WEB', 'RT-001', 'Baik'),
(1169, 'Router Cisco 2811', 'RC-001', 'Baik'),
(1170, 'Router Cisco 2811', 'RC-002', 'Baik'),
(1171, 'Router Cisco 2811', 'RC-003', 'Baik'),
(1172, 'Router Cisco 2811', 'RC-004', 'Baik'),
(1173, 'Router Cisco 2811', 'RC-005', 'Baik'),
(1174, 'Router Cisco 2811', 'RC-006', 'Baik'),
(1175, 'Router D-Link DES-1026 G', 'RD-001', 'Baik'),
(1176, 'Router Cisco 1841', 'RC-001', 'Baik'),
(1177, 'Kabel Serial USB', 'KS-001', 'Baik'),
(1178, 'Kabel Serial USB', 'KS-002', 'Baik'),
(1179, 'Kabel Serial USB', 'KS-003', 'Baik'),
(1180, 'Kabel Serial USB', 'KS-004', 'Baik'),
(1181, 'Kabel Serial USB', 'KS-005', 'Baik'),
(1182, 'Kabel Serial USB', 'KS-006', 'Baik'),
(1183, 'Kabel Serial USB', 'KS-007', 'Baik'),
(1184, 'Kabel Serial USB', 'KS-008', 'Baik'),
(1185, 'Kabel Serial USB', 'KS-009', 'Baik'),
(1186, 'Kabel Serial USB', 'KS-010', 'Baik'),
(1187, 'Kabel Serial USB', 'KS-011', 'Baik'),
(1188, 'Kabel Serial USB', 'KS-012', 'Baik'),
(1189, 'Kabel Serial USB', 'KS-013', 'Baik'),
(1190, 'Kabel Console', 'KC-001', 'Baik'),
(1191, 'Kabel Console', 'KC-002', 'Baik'),
(1192, 'Kabel Console', 'KC-003', 'Baik'),
(1193, 'Kabel Console', 'KC-004', 'Baik'),
(1194, 'Kabel Console', 'KC-005', 'Baik'),
(1195, 'Kabel Console', 'KC-006', 'Baik'),
(1196, 'Kabel Console', 'KC-007', 'Baik'),
(1197, 'Kabel Console', 'KC-008', 'Baik'),
(1198, 'Kabel Console', 'KC-009', 'Baik'),
(1199, 'Kabel Console', 'KC-010', 'Baik'),
(1200, 'Kabel Console', 'KC-011', 'Baik'),
(1229, 'Commscope RUCKUS R5050 S', 'CR-001', 'Baik'),
(1230, 'Commscope RUCKUS R5050 S', 'CR-002', 'Baik'),
(1231, 'Commscope RUCKUS R5050 S', 'CR-003', 'Baik'),
(1232, 'Commscope RUCKUS R5050 S', 'CR-004', 'Baik'),
(1233, 'Commscope RUCKUS R5050 S', 'CR-005', 'Baik'),
(1234, 'Commscope RUCKUS R5050 S', 'CR-006', 'Baik'),
(1235, 'Commscope RUCKUS R5050 S', 'CR-007', 'Baik'),
(1236, 'Commscope RUCKUS R5050 S', 'CR-008', 'Baik'),
(1237, 'RUCKUS ICX 7150-CI2P', 'RI-001', 'Baik'),
(1238, 'RUCKUS ICX 7150-CI2P', 'RI-002', 'Baik'),
(1239, 'RUCKUS ICX 7150-CI2P', 'RI-003', 'Baik'),
(1240, 'RUCKUS ICX 7150-CI2P', 'RI-004', 'Baik'),
(1241, 'RUCKUS ICX 7150-CI2P', 'RI-005', 'Baik'),
(1242, 'RUCKUS ICX 7150-CI2P', 'RI-006', 'Baik'),
(1243, 'RUCKUS ICX 7150-CI2P', 'RI-007', 'Baik'),
(1244, 'RUCKUS ICX 7150-CI2P', 'RI-008', 'Baik'),
(1245, 'RUCKUS ICX 7150-CI2P', 'RI-009', 'Baik'),
(1253, 'D-Link DIR-655 Router', 'DL-001', 'Baik'),
(1254, 'D-Link DIR-655 Router', 'DL-002', 'Baik'),
(1255, 'D-Link DIR-655 Router', 'DL-003', 'Baik'),
(1256, 'Router TL-WR941ND', 'RT-001', 'Baik'),
(1257, 'Router TL-WR941ND', 'RT-002', 'Baik'),
(1258, 'Router D-Link DSL-526B', 'RD-001', 'Baik'),
(1259, 'Router TP-Link TL-WR340G', 'RT-001', 'Baik'),
(1260, 'Router Planet WAP-1966', 'RP-001', 'Baik'),
(1261, 'Router Planet WAP-1966', 'RP-002', 'Baik'),
(1262, 'Router TP-Link TL-WR740N', 'RT-001', 'Baik'),
(1263, 'Router I-240 W-A', 'RI-001', 'Baik'),
(1264, 'Router Mavistar ', 'RM-001', 'Baik'),
(1265, 'TP-Link Box Hijau', 'TL-001', 'Baik'),
(1266, 'TP-Link Box Hijau', 'TL-002', 'Baik'),
(1267, 'TP-Link Box Hijau', 'TL-003', 'Baik'),
(1268, 'TP-Link Box Hijau', 'TL-004', 'Baik'),
(1269, 'TP-Link Box Hijau', 'TL-005', 'Baik'),
(1270, 'TP-Link Box Hijau', 'TL-006', 'Baik'),
(1271, 'TP-Link Box Putih', 'TL-001', 'Baik'),
(1272, 'T0T0 Link N300RT', 'TL-001', 'Baik'),
(1273, 'T0T0 Link N300RT', 'TL-002', 'Baik'),
(1274, 'Router AC15', 'RA-001', 'Baik'),
(1275, 'Router AC15', 'RA-002', 'Baik'),
(1276, 'Router AC15', 'RA-003', 'Baik'),
(1277, 'Router AC15', 'RA-004', 'Baik'),
(1278, 'Router AC15', 'RA-005', 'Baik'),
(1279, 'Router Blue-Link BL-R3IN', 'RB-001', 'Baik'),
(1602, 'ET Hernet Adapter', 'EH-001', 'Baik'),
(1603, 'ET Hernet Adapter', 'EH-002', 'Baik'),
(1604, 'ET Hernet Adapter', 'EH-003', 'Baik'),
(1605, 'ET Hernet Adapter', 'EH-004', 'Baik'),
(1606, 'ET Hernet Adapter', 'EH-005', 'Baik'),
(1607, 'ET Hernet Adapter', 'EH-006', 'Baik'),
(1608, 'ET Hernet Adapter', 'EH-007', 'Baik'),
(1609, 'ET Hernet Adapter', 'EH-008', 'Baik'),
(1610, 'ET Hernet Adapter', 'EH-009', 'Baik'),
(1611, 'ET Hernet Adapter', 'EH-010', 'Baik'),
(1612, 'ET Hernet Adapter', 'EH-011', 'Baik'),
(1613, 'ET Hernet Adapter', 'EH-012', 'Baik'),
(1614, 'ET Hernet Adapter', 'EH-013', 'Baik'),
(1615, 'ET Hernet Adapter', 'EH-014', 'Baik'),
(1616, 'ET Hernet Adapter', 'EH-015', 'Baik'),
(1617, 'ET Hernet Adapter', 'EH-016', 'Baik'),
(1618, 'ET Hernet Adapter', 'EH-017', 'Baik'),
(1619, 'ET Hernet Adapter', 'EH-018', 'Baik'),
(1620, 'ET Hernet Adapter', 'EH-019', 'Baik'),
(1621, 'ET Hernet Adapter', 'EH-020', 'Baik'),
(1622, 'ET Hernet Adapter', 'EH-021', 'Baik'),
(1623, 'ET Hernet Adapter', 'EH-022', 'Baik'),
(1624, 'ET Hernet Adapter', 'EH-023', 'Baik'),
(1625, 'ET Hernet Adapter', 'EH-024', 'Baik'),
(1626, 'ET Hernet Adapter', 'EH-025', 'Baik'),
(1627, 'ET Hernet Adapter', 'EH-026', 'Baik'),
(1628, 'ET Hernet Adapter', 'EH-027', 'Baik'),
(1629, 'ET Hernet Adapter', 'EH-028', 'Baik'),
(1630, 'ET Hernet Adapter', 'EH-029', 'Baik'),
(1631, 'ET Hernet Adapter', 'EH-030', 'Baik'),
(1632, 'Cable Tester', 'CT-001', 'Baik'),
(1633, 'Cable Tester', 'CT-002', 'Baik'),
(1634, 'Cable Tester', 'CT-003', 'Baik'),
(1635, 'Cable Tester', 'CT-004', 'Baik'),
(1636, 'Cable Tester', 'CT-005', 'Baik'),
(1637, 'Cable Tester', 'CT-006', 'Baik'),
(1638, 'Cable Tester', 'CT-007', 'Baik'),
(1639, 'Cable Tester', 'CT-008', 'Baik'),
(1640, 'Cable Tester', 'CT-009', 'Baik'),
(1641, 'Cable Tester', 'CT-010', 'Baik'),
(1642, 'Cable Tester', 'CT-011', 'Baik'),
(1643, 'Multimeter Analog', 'MA-001', 'Baik'),
(1644, 'Multimeter Analog', 'MA-002', 'Baik'),
(1645, 'Multimeter Analog', 'MA-003', 'Baik'),
(1646, 'Multimeter Digital', 'MD-001', 'Baik'),
(1647, 'Multimeter Digital', 'MD-002', 'Baik'),
(1648, 'Multimeter Digital', 'MD-003', 'Baik'),
(1649, 'Multimeter Digital', 'MD-004', 'Baik'),
(1650, 'Multimeter Digital', 'MD-005', 'Baik'),
(1651, 'Multimeter Digital', 'MD-006', 'Baik'),
(1652, 'Multimeter Digital', 'MD-007', 'Baik'),
(1653, 'Mega Series 2560', 'MS-001', 'Baik'),
(1654, 'Mega Series 2560', 'MS-002', 'Baik'),
(1655, 'Mega Series 2560', 'MS-003', 'Baik'),
(1656, 'Mega Series 2560', 'MS-004', 'Baik'),
(1657, 'Mega Series 2560', 'MS-005', 'Baik'),
(1658, 'Mega Series 2560', 'MS-006', 'Baik'),
(1659, 'Mega Series 2560', 'MS-007', 'Baik'),
(1660, 'Mega Series 2560', 'MS-008', 'Baik'),
(1661, 'Mega Series 2560', 'MS-009', 'Baik'),
(1662, 'Lcd Qapass', 'LQ-001', 'Baik'),
(1663, 'Lcd Qapass 1602A', 'LQ-001', 'Baik'),
(1664, 'Lcd Qapass 1602A', 'LQ-002', 'Baik'),
(1665, 'Lcd Qapass 1602A', 'LQ-003', 'Baik'),
(1666, 'Lcd Qapass 1602A', 'LQ-004', 'Baik'),
(1667, 'Lcd Qapass 1602A', 'LQ-005', 'Baik'),
(1668, 'Lcd Qapass 1602A', 'LQ-006', 'Baik'),
(1669, 'Lcd Qapass 1602A', 'LQ-007', 'Baik'),
(1670, 'Lcd Qapass 1602A', 'LQ-008', 'Baik'),
(1671, 'Lcd Qapass 1602A', 'LQ-009', 'Baik'),
(1672, 'Lcd Qapass 1602A', 'LQ-010', 'Baik'),
(1673, 'Lcd Qapass 1602A', 'LQ-011', 'Baik'),
(1674, 'Lcd Qapass 1602A', 'LQ-012', 'Baik'),
(1675, 'Lcd Qapass 1602A', 'LQ-013', 'Baik'),
(1676, 'Lcd Qapass 1602A', 'LQ-014', 'Baik'),
(1677, 'Lcd Qapass 1602A', 'LQ-015', 'Baik'),
(1678, 'Lcd Qapass 1602A', 'LQ-016', 'Baik'),
(1679, 'Lcd Qapass 1602A', 'LQ-017', 'Baik'),
(1680, 'Lcd Qapass 1602A', 'LQ-018', 'Baik'),
(1681, 'Lcd Qapass 1602A', 'LQ-019', 'Baik'),
(1682, 'Lcd Qapass 1602A', 'LQ-020', 'Baik'),
(1683, 'Lcd Qapass 1602A', 'LQ-021', 'Baik'),
(1684, 'Lcd Qapass 1602A', 'LQ-022', 'Baik'),
(1685, 'Lcd Qapass 1602A', 'LQ-023', 'Baik'),
(1686, 'Lcd Qapass 1602A', 'LQ-024', 'Baik'),
(1687, 'Lcd Qapass 1602A', 'LQ-025', 'Baik'),
(1688, 'Lcd Qapass 1602A', 'LQ-026', 'Baik'),
(1689, 'Lcd Qapass 1602A', 'LQ-027', 'Baik'),
(1690, 'Lcd Qapass 1602A', 'LQ-028', 'Baik'),
(1691, 'Lcd Qapass 1602A', 'LQ-029', 'Baik'),
(1692, 'Lcd Qapass 1602A', 'LQ-030', 'Baik'),
(1693, 'Lcd Qapass 1602A', 'LQ-031', 'Baik'),
(1694, 'Lcd Qapass 1602A', 'LQ-032', 'Baik'),
(1695, 'Lcd Qapass 1602A', 'LQ-033', 'Baik'),
(1696, 'Lcd Qapass 1602A', 'LQ-034', 'Baik'),
(1697, 'Lcd Qapass 1602A', 'LQ-035', 'Baik'),
(1698, 'Lcd Qapass 1602A', 'LQ-036', 'Baik'),
(1699, 'Lcd Qapass 1602A', 'LQ-037', 'Baik'),
(1700, 'Lcd Qapass 1602A', 'LQ-038', 'Baik'),
(1701, 'Lcd Qapass 1602A', 'LQ-039', 'Baik'),
(1702, 'Lcd Qapass 1602F', 'LQ-001', 'Baik'),
(1703, 'Lcd Qapass 1602F', 'LQ-002', 'Baik'),
(1704, 'Lcd Qapass 1602F', 'LQ-003', 'Baik'),
(1705, 'Lcd Qapass 1602F', 'LQ-004', 'Baik'),
(1706, 'Lcd Qapass 1602F', 'LQ-005', 'Baik'),
(1707, 'Lcd Sunstar 2004A', 'LS-001', 'Baik'),
(1708, 'Uno Hw-277', 'UH-001', 'Baik'),
(1709, 'Uno Hw-277', 'UH-002', 'Baik'),
(1710, 'Uno Hw-277', 'UH-003', 'Baik'),
(1711, 'Uno Arduino', 'UA-001', 'Baik'),
(1712, 'Uno Arduino', 'UA-002', 'Baik'),
(1713, 'Uno Arduino', 'UA-003', 'Baik'),
(1714, 'Uno Arduino', 'UA-004', 'Baik'),
(1715, 'Uno Arduino', 'UA-005', 'Baik'),
(1716, 'Konektor Rj45', 'KR-001', 'Baik'),
(1717, 'Konektor Rj45', 'KR-002', 'Baik'),
(1718, 'Konektor Rj45', 'KR-003', 'Baik'),
(1719, 'Konektor Rj45', 'KR-004', 'Baik'),
(1720, 'Konektor Rj45', 'KR-005', 'Baik'),
(1721, 'Konektor Rj45', 'KR-006', 'Baik'),
(1722, 'Konektor Rj45', 'KR-007', 'Baik'),
(1723, 'Konektor Rj45', 'KR-008', 'Baik'),
(1724, 'Konektor Rj45', 'KR-009', 'Baik'),
(1725, 'Konektor Rj45', 'KR-010', 'Baik'),
(1726, 'Konektor Rj45', 'KR-011', 'Baik'),
(1727, 'Konektor Rj45', 'KR-012', 'Baik'),
(1728, 'Konektor Rj45', 'KR-013', 'Baik'),
(1729, 'Konektor Rj45', 'KR-014', 'Baik'),
(1730, 'Konektor Rj45', 'KR-015', 'Baik'),
(1731, 'Konektor Rj45 Modular Plug', 'KP-001', 'Baik'),
(1732, 'Konektor Rj45 Modular Plug', 'KP-002', 'Baik'),
(1733, 'Konektor Rj45 Modular Plug', 'KP-003', 'Baik'),
(1734, 'Konektor Rj45 Modular Plug', 'KP-004', 'Baik'),
(1735, 'Konektor Rj45 Modular Plug', 'KP-005', 'Baik'),
(1736, 'Konektor Rj45 Modular Plug', 'KP-006', 'Baik'),
(1737, 'Konektor Rj45 Modular Plug', 'KP-007', 'Baik'),
(1738, 'Konektor Rj45 Modular Plug', 'KP-008', 'Baik'),
(1739, 'Konektor Rj45 Modular Plug', 'KP-009', 'Baik'),
(1740, 'Konektor Rj45 Modular Plug', 'KP-010', 'Baik'),
(1741, 'Konektor Rj45 Modular Plug', 'KP-011', 'Baik'),
(1742, 'Konektor Rj45 Modular Plug', 'KP-012', 'Baik'),
(1743, 'Konektor Rj45 Modular Plug', 'KP-013', 'Baik'),
(1744, 'Konektor Rj45 Modular Plug', 'KP-014', 'Baik'),
(1745, 'Konektor Rg45 AMP', 'KA-001', 'Baik'),
(1746, 'Konektor Rg45 AMP', 'KA-002', 'Baik'),
(1747, 'Konektor Rg45 AMP', 'KA-003', 'Baik'),
(1748, 'Konektor Rg45 AMP', 'KA-004', 'Baik'),
(1749, 'Konektor Rg45 AMP', 'KA-005', 'Baik'),
(1750, 'Konektor Rg45 AMP', 'KA-006', 'Baik'),
(1751, 'Konektor Rg45 AMP', 'KA-007', 'Baik'),
(1752, 'Konektor Rg45 AMP', 'KA-008', 'Baik'),
(1753, 'Konektor Rj45 TN', 'KT-001', 'Baik'),
(1754, 'Konektor Rj45 TN', 'KT-002', 'Baik'),
(1755, 'Konektor Rj45 TN', 'KT-003', 'Baik'),
(1756, 'Konektor Rj45 TN', 'KT-004', 'Baik'),
(1757, 'Konektor Rj45 TE', 'KE-001', 'Baik'),
(1785, 'Konektor DB9/SET (50)', 'KD-001', 'Baik'),
(1786, 'Konektor DB9/SET (50)', 'KD-002', 'Baik'),
(1787, 'Konektor DB9/SET (50)', 'KD-003', 'Baik'),
(1788, 'Konektor DB9/SET (50)', 'KD-004', 'Baik'),
(1789, 'Konektor DB9/SET (50)', 'KD-005', 'Baik'),
(1790, 'Konektor DB9/SET (50)', 'KD-006', 'Baik'),
(1791, 'Konektor DB9/SET (50)', 'KD-007', 'Baik'),
(1792, 'Konektor DB9/SET (50)', 'KD-008', 'Baik'),
(1793, 'Konektor DB9/SET (50)', 'KD-009', 'Baik'),
(1794, 'Konektor DB9/SET (50)', 'KD-010', 'Baik'),
(1795, 'Konektor DB9/SET (50)', 'KD-011', 'Baik'),
(1796, 'Konektor DB9/SET (50)', 'KD-012', 'Baik'),
(1797, 'Konektor DB9/SET (50)', 'KD-013', 'Baik'),
(1798, 'Konektor DB9/SET (50)', 'KD-014', 'Baik'),
(1799, 'Konektor DB9/SET (50)', 'KD-015', 'Baik'),
(1800, 'Konektor DB9/SET (50)', 'KD-016', 'Baik'),
(1801, 'Konektor DB9/SET (50)', 'KD-017', 'Baik'),
(1802, 'Konektor TYCO KT', 'KT-001', 'Baik'),
(1803, 'Konektor TYCO KT', 'KT-001', 'Baik'),
(1804, 'PWM dan PULSE', 'PP-001', 'Baik'),
(1805, 'PWM dan PULSE', 'PP-002', 'Baik'),
(1806, 'PWM dan PULSE', 'PP-003', 'Baik'),
(1807, 'PWM dan PULSE', 'PP-004', 'Baik'),
(1808, 'PWM dan PULSE', 'PP-005', 'Baik'),
(1809, 'PWM dan PULSE', 'PP-006', 'Baik'),
(1810, 'PWM dan PULSE', 'PP-007', 'Baik'),
(1811, 'PWM dan PULSE', 'PP-008', 'Baik'),
(1812, 'PWM dan PULSE', 'PP-009', 'Baik'),
(1813, 'PWM dan PULSE', 'PP-010', 'Baik'),
(1814, 'Project Board PB', 'PB-001', 'Baik'),
(1815, 'Project Board PB', 'PB-002', 'Baik'),
(1816, 'Project Board PB', 'PB-003', 'Baik'),
(1817, 'Project Board PB', 'PB-004', 'Baik'),
(1818, 'Project Board PB', 'PB-005', 'Baik'),
(1819, 'Project Board PB', 'PB-006', 'Baik'),
(1820, 'Project Board PB', 'PB-007', 'Baik'),
(1821, 'Project Board PB', 'PB-008', 'Baik'),
(1822, 'Project Board PB', 'PB-009', 'Baik'),
(1823, 'Project Board PB', 'PB-010', 'Baik'),
(1824, 'Project Board PB', 'PB-011', 'Baik'),
(1825, 'Project Board PB', 'PB-012', 'Baik'),
(1826, 'Project Board PB', 'PB-013', 'Baik'),
(1827, 'Project Board PB', 'PB-014', 'Baik'),
(1828, 'Project Board PB', 'PB-015', 'Baik'),
(1829, 'Project Board PB', 'PB-016', 'Baik'),
(1830, 'Project Board PB', 'PB-017', 'Baik'),
(1831, 'Project Board PB', 'PB-018', 'Baik'),
(1832, 'Project Board PB', 'PB-019', 'Baik'),
(1833, 'Project Board PB', 'PB-020', 'Baik'),
(1834, 'Project Board PB', 'PB-021', 'Baik'),
(1835, 'Project Board PB', 'PB-022', 'Baik'),
(1836, 'Project Board PB', 'PB-023', 'Baik'),
(1837, 'Project Board PB', 'PB-024', 'Baik'),
(1838, 'Project Board PB', 'PB-025', 'Baik'),
(1839, 'Project Board PB', 'PB-026', 'Baik'),
(1840, 'Project Board PB', 'PB-027', 'Baik'),
(1841, 'Project Board PB', 'PB-028', 'Baik'),
(1842, 'Project Board PB', 'PB-029', 'Baik'),
(1843, 'Project Board PB', 'PB-030', 'Baik'),
(1844, 'Project Board PB', 'PB-031', 'Baik'),
(1845, 'Project Board PB', 'PB-032', 'Baik'),
(1846, 'Project Board PB', 'PB-033', 'Baik'),
(1847, 'Project Board PB', 'PB-034', 'Baik'),
(1848, 'Project Board PB', 'PB-035', 'Baik'),
(1849, 'Project Board PB', 'PB-036', 'Baik'),
(1850, 'Project Board PB', 'PB-037', 'Baik'),
(1851, 'Project Board PB', 'PB-038', 'Baik'),
(1852, 'Project Board PB', 'PB-039', 'Baik'),
(1853, 'Project Board PB', 'PB-040', 'Baik'),
(1854, 'Project Board PB', 'PB-041', 'Baik'),
(1855, 'Project Board PB', 'PB-042', 'Baik'),
(1856, 'Project Board PB', 'PB-043', 'Baik'),
(1857, 'Project Board PB', 'PB-044', 'Baik'),
(1858, 'Project Board PB', 'PB-045', 'Baik'),
(1859, 'Project Board PB', 'PB-046', 'Baik'),
(1860, 'Project Board PB', 'PB-047', 'Baik'),
(1861, 'Project Board PB', 'PB-048', 'Baik'),
(1862, 'Project Board PB', 'PB-049', 'Baik'),
(1863, 'Project Board PB', 'PB-050', 'Baik'),
(1864, 'Project Board PB', 'PB-051', 'Baik'),
(1865, 'Project Board PB', 'PB-052', 'Baik'),
(1866, 'Project Board PB', 'PB-053', 'Baik'),
(1867, 'Project Board PB', 'PB-054', 'Baik'),
(1868, 'Project Board PB', 'PB-055', 'Baik'),
(1869, 'Project Board PB', 'PB-056', 'Baik'),
(1870, 'Project Board PB', 'PB-057', 'Baik'),
(1871, 'Usbserial Converter', 'UC-001', 'Baik'),
(1872, 'Usbserial Converter', 'UC-002', 'Baik'),
(1873, 'Usbserial Converter', 'UC-003', 'Baik'),
(1874, 'Switch BPRO (Unmanaged Switch)', 'SU-001', 'Baik'),
(1875, 'Switch D-Link DES-1008A', 'SA-001', 'Baik'),
(1876, 'Cable LAN Cross', 'CC-001', 'Baik'),
(1877, 'Cable LAN Straight', 'CS-001', 'Baik'),
(1878, 'Cable LAN Straight', 'CS-002', 'Baik'),
(1879, 'Cable LAN Straight', 'CS-003', 'Baik'),
(1880, 'Cable LAN Straight', 'CS-004', 'Baik'),
(1881, 'Cable LAN Straight', 'CS-005', 'Baik'),
(1882, 'Cable LAN Straight', 'CS-006', 'Baik'),
(1883, 'Cable LAN Straight', 'CS-007', 'Baik'),
(1884, 'Cable LAN Straight', 'CS-008', 'Baik'),
(1885, 'Cable LAN Straight', 'CS-009', 'Baik'),
(1886, 'Cable LAN Straight', 'CS-010', 'Baik'),
(1887, 'Cable LAN Straight', 'CS-011', 'Baik'),
(1888, 'Cable LAN Straight', 'CS-012', 'Baik'),
(1889, 'Cable LAN Straight', 'CS-013', 'Baik'),
(1890, 'Cable LAN Straight', 'CS-014', 'Baik'),
(1891, 'Cable LAN Straight', 'CS-015', 'Baik'),
(1892, 'Cable LAN Straight', 'CS-016', 'Baik'),
(1893, 'Cable LAN Straight', 'CS-017', 'Baik'),
(1894, 'Cable LAN Straight', 'CS-018', 'Baik'),
(1895, 'Cable LAN Straight', 'CS-019', 'Baik'),
(1896, 'Cable LAN Straight', 'CS-020', 'Baik'),
(1897, 'Cable LAN Straight', 'CS-021', 'Baik'),
(1898, 'Cable LAN Straight', 'CS-022', 'Baik'),
(1899, 'Cable LAN Straight', 'CS-023', 'Baik'),
(1900, 'Cable LAN Straight', 'CS-024', 'Baik'),
(1901, 'Cable LAN Straight', 'CS-025', 'Baik'),
(1902, 'Cable LAN Straight', 'CS-026', 'Baik'),
(1903, 'Cable LAN Straight', 'CS-027', 'Baik'),
(1904, 'Cable LAN Straight', 'CS-028', 'Baik'),
(1905, 'Cable LAN Straight', 'CS-029', 'Baik'),
(1906, 'Cable LAN Straight', 'CS-030', 'Baik'),
(1907, 'Cable LAN Straight', 'CS-031', 'Baik'),
(1908, 'Cable LAN Straight', 'CS-032', 'Baik'),
(1909, 'Cable LAN Straight', 'CS-033', 'Baik'),
(1910, 'Cable LAN Straight', 'CS-034', 'Baik'),
(1911, 'Cable LAN Straight', 'CS-035', 'Baik'),
(1912, 'Cable LAN Straight', 'CS-036', 'Baik'),
(1913, 'Cable LAN Straight', 'CS-037', 'Baik'),
(1914, 'Cable LAN Straight', 'CS-038', 'Baik'),
(1915, 'Cable LAN Straight', 'CS-039', 'Baik'),
(1916, 'Kabel DCE', 'KC-001', 'Baik'),
(1917, 'Kabel DCE', 'KC-002', 'Baik'),
(1918, 'Kabel DCE', 'KC-003', 'Baik'),
(1919, 'Kabel DCE', 'KC-004', 'Baik'),
(1920, 'Kabel DCE', 'KC-005', 'Baik'),
(1921, 'Kabel DCE', 'KC-006', 'Baik'),
(1922, 'Kabel DCE', 'KC-007', 'Baik'),
(1923, 'Kabel DCE', 'KC-008', 'Baik'),
(1924, 'Kabel DCE', 'KC-009', 'Baik'),
(1925, 'Kabel DCE', 'KC-010', 'Baik'),
(1926, 'Kabel DCE', 'KC-011', 'Baik'),
(1927, 'Kabel DCE', 'KC-012', 'Baik'),
(1928, 'Kabel DCE', 'KC-013', 'Baik'),
(1929, 'Kabel DCE', 'KC-014', 'Baik'),
(1930, 'Kabel DTE', 'KT-001', 'Baik'),
(1931, 'Kabel DTE', 'KT-002', 'Baik'),
(1932, 'Kabel DTE', 'KT-003', 'Baik'),
(1933, 'Kabel DTE', 'KT-004', 'Baik'),
(1934, 'Kabel DTE', 'KT-005', 'Baik'),
(1935, 'Kabel DTE', 'KT-006', 'Baik'),
(1936, 'Kabel DTE', 'KT-007', 'Baik'),
(1937, 'Kabel DTE', 'KT-008', 'Baik'),
(1938, 'Kabel DTE', 'KT-009', 'Baik'),
(1939, 'Kabel DTE', 'KT-010', 'Baik'),
(1940, 'Kabel DTE', 'KT-011', 'Baik'),
(1941, 'Kabel DTE', 'KT-012', 'Baik'),
(1942, 'Kabel DTE', 'KT-013', 'Baik'),
(1943, 'Kabel DTE', 'KT-014', 'Baik');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_ruang`
--

CREATE TABLE `jadwal_ruang` (
  `id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat') NOT NULL,
  `jam_ke` int(11) NOT NULL COMMENT 'Contoh: 1-2',
  `waktu_id` int(11) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `matkul_id` int(11) DEFAULT NULL,
  `ruang_id` int(11) DEFAULT NULL,
  `dosen_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_ruang`
--

INSERT INTO `jadwal_ruang` (`id`, `hari`, `jam_ke`, `waktu_id`, `kelas_id`, `matkul_id`, `ruang_id`, `dosen_id`) VALUES
(7, 'Senin', 1, 1, 29, 8, 7, 5),
(12, 'Rabu', 2, 1, 29, 8, 7, 5),
(13, 'Senin', 2, 2, 29, 8, 7, 5),
(14, 'Selasa', 1, 1, 29, 8, 7, 5),
(18, 'Senin', 1, 1, NULL, NULL, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`) VALUES
(29, 'TE-1A'),
(37, 'TE-1B'),
(32, 'TE-1C'),
(31, 'TE-1D'),
(34, 'TE-1E'),
(14, 'TE-2A'),
(54, 'TE-2B'),
(6, 'TE-2C'),
(56, 'TE-2D'),
(9, 'TE-2E'),
(15, 'TE-3A'),
(53, 'TE-3B'),
(7, 'TE-3C'),
(21, 'TE-3D'),
(28, 'TE-3E'),
(17, 'TE-4A'),
(23, 'TE-4B'),
(20, 'TE-4C'),
(18, 'TE-4D'),
(24, 'TE-4E (RPL)'),
(33, 'TK-1A'),
(26, 'TK-1B'),
(25, 'TK-1C'),
(36, 'TK-1D'),
(10, 'TK-2A'),
(38, 'TK-2B'),
(8, 'TK-2C'),
(40, 'TK-2D'),
(50, 'TK-3E');

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id` int(11) NOT NULL,
  `nama_matkul` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`id`, `nama_matkul`) VALUES
(8, 'Bengkel Teknik'),
(6, 'Jaringan Komputer I'),
(4, 'Jaringan Komputer Lanjut I'),
(5, 'Jaringan Komputer Lanjut II'),
(3, 'Komunikasi Data'),
(1, 'Medan Elektromagnetik'),
(9, 'Perangkat Listrik dan Elektronik'),
(2, 'Sistem Komunikasi Satelit'),
(7, 'Sistem Operasi Komputer');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nama_mahasiswa` varchar(50) NOT NULL,
  `matkul_id` int(11) DEFAULT NULL,
  `barang_id` int(11) NOT NULL,
  `waktu_pinjam` time DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `waktu_kembali` time DEFAULT NULL,
  `tanggal_kembali` datetime DEFAULT NULL,
  `status_peminjaman` enum('dipinjam','dikembalikan') NOT NULL,
  `foto_ktm` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ajaran_semester` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `user_id`, `kelas_id`, `nama_mahasiswa`, `matkul_id`, `barang_id`, `waktu_pinjam`, `tanggal_pinjam`, `waktu_kembali`, `tanggal_kembali`, `status_peminjaman`, `foto_ktm`, `created_at`, `ajaran_semester`) VALUES
(32, NULL, 29, 'dymas', 8, 1876, '09:03:06', '2026-03-02', NULL, NULL, 'dikembalikan', 'KTM_unknown_1772416986.jpg', '2026-03-02 02:03:06', '2027/2028 Ganjil'),
(33, NULL, 31, 'shidiq', 4, 1916, '10:00:04', '2026-03-02', NULL, '2026-03-02 11:44:10', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(34, NULL, 31, 'shidiq', 4, 1917, '10:00:04', '2026-03-02', NULL, '2026-03-02 11:44:10', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(35, NULL, 31, 'shidiq', 4, 1918, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(36, NULL, 31, 'shidiq', 4, 1919, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(37, NULL, 31, 'shidiq', 4, 1920, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(38, NULL, 31, 'shidiq', 4, 1921, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(39, NULL, 31, 'shidiq', 4, 1922, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(40, NULL, 31, 'shidiq', 4, 1923, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(41, NULL, 31, 'shidiq', 4, 1924, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(42, NULL, 31, 'shidiq', 4, 1925, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(43, NULL, 31, 'shidiq', 4, 1926, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:18', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(44, NULL, 31, 'shidiq', 4, 1177, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(45, NULL, 31, 'shidiq', 4, 1178, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(46, NULL, 31, 'shidiq', 4, 1179, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(47, NULL, 31, 'shidiq', 4, 1180, '10:00:04', '2026-03-02', NULL, NULL, 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(48, NULL, 31, 'shidiq', 4, 1181, '10:00:04', '2026-03-02', NULL, NULL, 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(49, NULL, 31, 'shidiq', 4, 1182, '10:00:04', '2026-03-02', NULL, NULL, 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(50, NULL, 31, 'shidiq', 4, 1183, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(51, NULL, 31, 'shidiq', 4, 1184, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(52, NULL, 31, 'shidiq', 4, 1185, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(53, NULL, 31, 'shidiq', 4, 1186, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(54, NULL, 31, 'shidiq', 4, 1187, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(55, NULL, 31, 'shidiq', 4, 1188, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil'),
(56, NULL, 31, 'shidiq', 4, 1189, '10:00:04', '2026-03-02', NULL, '2026-03-02 10:13:39', 'dikembalikan', 'KTM_unknown_1772420404.jpg', '2026-03-02 03:00:04', '2027/2028 Ganjil');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(11) NOT NULL,
  `ruang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `ruang`) VALUES
(5, 'Lab. TK Timur II/01'),
(7, 'Lab. TK Timur I/01'),
(10, 'Lab. TK Timur I/02'),
(11, 'Lab. TK Timur II/02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dosen','super_admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `password`, `role`) VALUES
(5, 'Dosen', 'Dosen Kece', '$2y$10$lpSOG.HRuQrRrDyqZkWoxe7p5DXyqDX38dWawFrQWfJFx/s3nqdjG', 'dosen'),
(6, 'admin123', 'Administrator Sistem', '$2y$10$VevwtTfMAumlybcHv9bV1.bdre5kqzijQ0TSyfy44Frgt/gYGcHQS', 'super_admin'),
(10, 'Admin', 'Admin Keren', '$2y$10$eeH2x1CvNbfvPaENhnxjIufa1syzGTBprTkyG8LH6p/KwaKJktBFu', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `waktu`
--

CREATE TABLE `waktu` (
  `id` int(11) NOT NULL,
  `jam_ke` int(11) NOT NULL,
  `waktu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waktu`
--

INSERT INTO `waktu` (`id`, `jam_ke`, `waktu`) VALUES
(1, 0, '07.00 - 08.00'),
(2, 0, '08.00 - 09.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal_ruang`
--
ALTER TABLE `jadwal_ruang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `matkul_id` (`matkul_id`),
  ADD KEY `waktu_id` (`waktu_id`),
  ADD KEY `ruang_id` (`ruang_id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`);

--
-- Indexes for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_matkul` (`nama_matkul`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`,`matkul_id`,`barang_id`),
  ADD KEY `matkul_id` (`matkul_id`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `waktu`
--
ALTER TABLE `waktu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1967;

--
-- AUTO_INCREMENT for table `jadwal_ruang`
--
ALTER TABLE `jadwal_ruang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `waktu`
--
ALTER TABLE `waktu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal_ruang`
--
ALTER TABLE `jadwal_ruang`
  ADD CONSTRAINT `jadwal_ruang_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ruang_ibfk_2` FOREIGN KEY (`matkul_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ruang_ibfk_3` FOREIGN KEY (`waktu_id`) REFERENCES `waktu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_ruang_ibfk_4` FOREIGN KEY (`ruang_id`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_ruang_ibfk_5` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`matkul_id`) REFERENCES `mata_kuliah` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
