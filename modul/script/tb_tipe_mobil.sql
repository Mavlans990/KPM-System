-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2021 at 04:58 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_coolplus`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_tipe_mobil`
--

CREATE TABLE `tb_tipe_mobil` (
  `id_tipe` varchar(40) NOT NULL,
  `merk_mobil` varchar(40) NOT NULL,
  `tipe_mobil` varchar(50) NOT NULL,
  `tahun` int(11) NOT NULL,
  `kategori` varchar(20) NOT NULL,
  `kaca_depan` float NOT NULL,
  `kaca_skkb` float NOT NULL,
  `lainnya` float NOT NULL,
  `total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_tipe_mobil`
--

INSERT INTO `tb_tipe_mobil` (`id_tipe`, `merk_mobil`, `tipe_mobil`, `tahun`, `kategori`, `kaca_depan`, `kaca_skkb`, `lainnya`, `total`) VALUES
('TIP000001', '22', 'AGYA', 2000, 'small', 75, 150, 0, 225),
('TIP000002', '38', 'ATOZ', 0, 'small', 75, 155, 0, 230),
('TIP000003', '27', 'AYLA', 0, 'small', 75, 150, 0, 225),
('TIP000004', '31', 'BRIO', 0, 'small', 85, 155, 0, 240),
('TIP000005', '28', 'CARRY PICKUP', 0, 'small', 75, 90, 0, 165),
('TIP000006', '42', 'DATSUN GO 2 SEAT', 0, 'small', 77, 150, 0, 227),
('TIP000007', '22', 'ETIOS', 0, 'small', 75, 155, 0, 230),
('TIP000008', '22', 'ETIOS VALCO', 0, 'small', 75, 155, 0, 230),
('TIP000009', '27', 'FEROZA', 0, 'small', 55, 165, 0, 220),
('TIP000010', '27', 'GRANDMAX PICKUP', 0, 'small', 75, 90, 0, 165),
('TIP000011', '28', 'IGNIS', 0, 'small', 77, 150, 0, 227),
('TIP000012', '28', 'KATANA', 0, 'small', 50, 135, 0, 185),
('TIP000013', '33', 'L300', 0, 'small', 75, 90, 0, 165),
('TIP000014', '36', 'MARCH', 0, 'small', 75, 155, 0, 230),
('TIP000015', '26', 'PICANTO', 0, 'small', 87, 160, 0, 247),
('TIP000016', '22', 'STARLET KOTAK', 0, 'medium', 75, 150, 0, 225),
('TIP000017', '28', 'SWIFT', 0, 'small', 85, 150, 0, 235),
('TIP000018', '33', 'TRITON / COLORADO /DOUBLE CABIN', 0, 'small', 75, 135, 0, 210),
('TIP000019', '28', 'WAGON R', 0, 'small', 77, 155, 0, 232),
('TIP000020', '38', 'GETZ', 0, 'small', 0, 0, 0, 0),
('TIP000021', '33', 'STRADA', 0, 'small', 0, 0, 0, 0),
('TIP000022', '33', 'MIRAGE', 0, 'small', 0, 0, 0, 0),
('TIP000023', '28', 'AERIO', 0, 'medium', 90, 165, 0, 255),
('TIP000024', '38', 'ACCENT', 0, 'medium', 0, 0, 0, 0),
('TIP000025', '22', 'AVANZA', 0, 'medium', 75, 175, 0, 250),
('TIP000026', '38', 'AVEGA', 0, 'medium', 0, 0, 0, 0),
('TIP000027', '44', 'AVEO', 0, 'small', 85, 150, 0, 235),
('TIP000028', '28', 'BALENO', 0, 'medium', 85, 160, 0, 245),
('TIP000029', '28', 'BALENO NEW', 0, 'medium', 75, 150, 0, 225),
('TIP000030', '31', 'BRV', 0, 'medium', 90, 175, 0, 265),
('TIP000031', '22', 'CALYA', 0, 'medium', 75, 155, 0, 230),
('TIP000032', '44', 'CAPTIVA', 0, 'large', 90, 165, 0, 255),
('TIP000033', '31', 'CITY', 0, 'medium', 90, 165, 0, 255),
('TIP000034', '42', 'DATSUN GO 3 SEAT', 0, 'medium', 75, 165, 0, 240),
('TIP000035', '28', 'ERTIGA', 0, 'medium', 82, 165, 0, 247),
('TIP000036', '28', 'ESCUDO 2.0 PETAK', 0, 'medium', 75, 165, 0, 240),
('TIP000037', '39', 'FIESTA', 0, 'medium', 90, 160, 0, 250),
('TIP000038', '43', 'GEELY EMGRAND', 0, 'medium', 0, 0, 0, 0),
('TIP000039', '36', 'GRAND LIVINA', 0, 'medium', 90, 175, 0, 265),
('TIP000040', '36', 'GRAND VITARA', 0, 'large', 77, 165, 0, 242),
('TIP000041', '22', 'GREAT COROLLA', 0, 'medium', 75, 165, 0, 240),
('TIP000042', '22', 'HARTOP', 0, 'medium', 35, 180, 0, 215),
('TIP000043', '31', 'HRV', 0, 'medium', 90, 175, 0, 265),
('TIP000044', '31', 'JAZZ', 0, 'medium', 95, 165, 0, 260),
('TIP000045', '36', 'JUKE', 0, 'medium', 77, 150, 0, 227),
('TIP000046', '25', 'MAZDA 2', 0, 'medium', 87, 160, 0, 247),
('TIP000047', '31', 'MOBILIO', 0, 'medium', 90, 175, 0, 265),
('TIP000048', '41', 'CAYMAN', 0, 'medium', 75, 165, 0, 240),
('TIP000049', '26', 'RIO', 0, 'medium', 85, 160, 0, 245),
('TIP000050', '29', 'RUBICON', 0, 'medium', 70, 165, 0, 235),
('TIP000051', '22', 'RUSH', 0, 'medium', 77, 175, 0, 252),
('TIP000052', '38', 'SANTAFE', 0, 'medium', 0, 0, 0, 0),
('TIP000053', '22', 'SIENTA', 0, 'medium', 85, 175, 0, 260),
('TIP000054', '27', 'SIGRA', 0, 'medium', 75, 155, 0, 230),
('TIP000055', '27', 'SIRION', 0, 'small', 77, 165, 0, 242),
('TIP000056', '28', 'SPIN', 0, 'medium', 90, 165, 0, 255),
('TIP000057', '28', 'SPLASH', 0, 'medium', 0, 0, 0, 0),
('TIP000058', '22', 'STARLET KAPSUL', 0, 'medium', 75, 160, 0, 235),
('TIP000059', '27', 'TAFF GT', 0, 'medium', 55, 170, 0, 225),
('TIP000060', '28', 'TARUNA', 0, 'medium', 75, 165, 0, 240),
('TIP000061', '36', 'TEANA', 0, 'large', 0, 0, 0, 0),
('TIP000062', '27', 'TERIOS', 0, 'medium', 77, 175, 0, 252),
('TIP000063', '39', 'TITANIUM', 0, 'medium', 87, 165, 0, 252),
('TIP000064', '22', 'VIOS', 0, 'medium', 85, 165, 0, 250),
('TIP000065', '28', 'X CROSS', 0, 'medium', 85, 165, 0, 250),
('TIP000066', '27', 'XENIA', 0, 'medium', 75, 175, 0, 250),
('TIP000067', '28', 'XL 7', 0, 'medium', 0, 0, 0, 0),
('TIP000068', '36', 'XPANDER', 0, 'medium', 90, 175, 0, 265),
('TIP000069', '22', 'YARIS', 0, 'medium', 85, 165, 0, 250),
('TIP000070', '44', 'ORLANDO', 0, 'large', 0, 0, 0, 0),
('TIP000071', '0', 'PEUGEOT', 0, '', 0, 0, 0, 0),
('TIP000072', '28', 'SX4', 0, 'medium', 0, 0, 0, 0),
('TIP000073', '40', 'DFSK GLORY 580', 0, 'large', 0, 0, 0, 0),
('TIP000074', '22', 'CORONA ABSOLUTE', 0, 'large', 0, 0, 0, 0),
('TIP000075', '26', 'TIMOR S515 I', 0, 'medium', 0, 0, 0, 0),
('TIP000076', '31', 'ACCORD CIELO', 0, 'large', 85, 200, 0, 285),
('TIP000077', '34', 'ALMAZ', 0, 'large', 85, 165, 0, 250),
('TIP000078', '22', 'ALTIS', 0, 'large', 90, 195, 0, 285),
('TIP000079', '28', 'APV', 0, 'large', 75, 200, 0, 275),
('TIP000080', '21', 'BMW E36', 0, 'large', 75, 200, 0, 275),
('TIP000081', '22', 'CAMRY', 0, 'large', 85, 200, 0, 285),
('TIP000082', '28', 'CARRY FUTURA MINIBUS', 0, 'large', 65, 210, 0, 275),
('TIP000083', '31', 'CIVIC', 0, 'large', 90, 200, 0, 290),
('TIP000084', '34', 'CONFERO', 0, 'large', 80, 200, 0, 280),
('TIP000085', '34', 'CORTEZ', 0, 'large', 85, 175, 0, 260),
('TIP000086', '31', 'CRV', 0, 'large', 90, 165, 0, 255),
('TIP000087', '25', 'CX-5', 0, 'medium', 90, 175, 0, 265),
('TIP000088', '28', 'ESCUDO 1.6', 0, 'large', 75, 210, 0, 285),
('TIP000089', '36', 'EVALIA', 0, 'large', 90, 200, 0, 290),
('TIP000090', '39', 'EVEREST', 0, 'large', 75, 200, 0, 275),
('TIP000091', '31', 'FERIO', 0, 'large', 85, 200, 0, 285),
('TIP000092', '22', 'FORTUNER', 0, 'large', 75, 200, 0, 275),
('TIP000093', '31', 'FREED', 0, 'large', 75, 200, 0, 275),
('TIP000094', '27', 'GRANDMAX MINIBUS', 0, 'large', 75, 200, 0, 275),
('TIP000095', '22', 'HARIER', 0, 'large', 90, 165, 0, 255),
('TIP000096', '22', 'INNOVA', 0, 'large', 85, 200, 0, 285),
('TIP000097', '22', 'KIJANG KAPSUL', 0, 'large', 75, 210, 0, 285),
('TIP000098', '33', 'KUDA', 0, 'large', 85, 200, 0, 285),
('TIP000099', '33', 'LANCER', 0, 'large', 0, 0, 0, 0),
('TIP000100', '22', 'LAND CRUISER', 0, 'large', 75, 200, 0, 275),
('TIP000101', '22', 'LEXUS', 0, 'large', 90, 175, 0, 265),
('TIP000102', '27', 'LUXIO', 0, 'large', 75, 200, 0, 275),
('TIP000103', '37', 'MERCY BOXER W-124', 0, 'large', 75, 200, 0, 275),
('TIP000104', '32', 'MU-X', 0, 'large', 85, 200, 0, 285),
('TIP000105', '33', 'OUTLANDER', 0, 'medium', 0, 0, 0, 0),
('TIP000106', '22', 'PAJERO LAMA', 0, 'large', 75, 200, 0, 275),
('TIP000107', '22', 'PAJERO NEW', 0, 'large', 75, 175, 0, 250),
('TIP000108', '22', 'PANTHER', 0, 'large', 75, 210, 0, 285),
('TIP000109', '39', 'RANGER', 0, 'large', 75, 200, 0, 275),
('TIP000110', '22', 'ROCKY', 0, 'large', 55, 210, 0, 265),
('TIP000111', '22', 'SOLUNA', 0, 'large', 85, 200, 0, 285),
('TIP000112', '36', 'XTRAIL', 0, 'large', 85, 175, 0, 260),
('TIP000113', '22', 'CROWN', 0, 'large', 0, 0, 0, 0),
('TIP000114', '38', 'ELANTRA', 0, 'large', 0, 0, 0, 0),
('TIP000115', '31', 'STREAM', 0, 'large', 0, 0, 0, 0),
('TIP000116', '38', 'SANTAFE', 0, 'large', 0, 0, 0, 0),
('TIP000117', '22', 'ALPHARD', 0, 'large', 100, 265, 0, 365),
('TIP000118', '33', 'COLT 120PS', 0, 'Xtra Large', 77, 320, 0, 397),
('TIP000119', '22', 'NAV1', 0, 'large', 95, 225, 0, 320),
('TIP000120', '36', 'SERENA', 0, 'large', 95, 225, 0, 320),
('TIP000121', '0', 'TRAJET', 0, '', 0, 0, 0, 0),
('TIP000122', '', 'Bajai', 0, '', 0, 0, 0, 0),
('TIP000123', '22', 'Custom', 2000, 'small', 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_tipe_mobil`
--
ALTER TABLE `tb_tipe_mobil`
  ADD PRIMARY KEY (`id_tipe`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
