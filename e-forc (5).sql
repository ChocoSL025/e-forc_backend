-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2020 at 11:33 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-forc`
--

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `idarea` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `gudang_idgudang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`idarea`, `nama`, `gudang_idgudang`) VALUES
(1, 'M1-1', 1),
(2, 'M1-2', 1),
(3, 'M1-3', 1),
(4, 'AS1', 2),
(5, 'AS2', 2),
(6, 'AS3', 2),
(7, 'M4', 1),
(8, 'SBY01', 3),
(9, 'SBY02', 3),
(10, 'SBY03', 3),
(11, 'KP1', 4),
(12, 'JYP1', 5),
(13, 'JY1', 5),
(14, 'JKT1', 6),
(16, 'AS2-1', 11),
(17, 'AS2-2', 11),
(38, 'M5', 1),
(39, 'M1-6', 1),
(40, 'AL-1', 12),
(41, 'SBY 1-1', 13),
(42, 'SBY 2', 13),
(43, 'A-1', 14),
(44, 'JK1', 15),
(45, 'JK2', 15),
(46, 'SEW-1', 16),
(47, 'SW-2', 16),
(48, 'SW-3', 16);

-- --------------------------------------------------------

--
-- Table structure for table `area_has_barang`
--

CREATE TABLE `area_has_barang` (
  `area_idarea` int(11) NOT NULL,
  `barang_idbarang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `area_has_barang`
--

INSERT INTO `area_has_barang` (`area_idarea`, `barang_idbarang`) VALUES
(1, 15),
(1, 16),
(1, 25),
(1, 36),
(1, 37),
(2, 15),
(2, 17),
(2, 25),
(2, 36),
(2, 37),
(2, 46),
(3, 16),
(3, 25),
(3, 47),
(3, 49),
(4, 16),
(4, 29),
(4, 47),
(5, 16),
(7, 15),
(8, 26),
(8, 31),
(8, 47),
(11, 28),
(12, 35),
(13, 35),
(14, 49),
(39, 47),
(44, 46),
(44, 49);

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `idbarang` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `safety_inventory` int(10) DEFAULT NULL,
  `reorder_point` int(10) DEFAULT NULL,
  `jumlah_barang` int(45) DEFAULT NULL,
  `harga_jual` int(45) DEFAULT NULL,
  `satuan_idsatuan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`idbarang`, `nama`, `safety_inventory`, `reorder_point`, `jumlah_barang`, `harga_jual`, `satuan_idsatuan`) VALUES
(15, 'paku payung', 2, 2, 2, 500, 18),
(16, 'pbesi', 100600, 100600, 100604, 500, 2),
(17, 'kertas', 0, 0, 505, 200, 1),
(25, 'pensil', 80, 80, 17, 3000, 1),
(26, 'cat tembok', 0, 0, 20, 50000, 4),
(27, 'Gayung', 0, 0, 20150, 11000, 1),
(28, 'kayu ulin', 0, 0, 100000, 30000, 22),
(29, 'keyboard', 0, 0, 600, 800000, 1),
(30, 'Mouse', 0, 0, 100, 90000, 1),
(31, 'headset', 0, 0, 40, 100000, 23),
(32, 'sendok', 0, 0, 100, 30000, 6),
(35, 'tisue', 0, 0, 1000, 3000, 18),
(36, 'paku', 0, 0, 0, 150000, 2),
(37, 'paku', 1008, 1008, 1008, 100, 2),
(38, 'Sampurna', 100, 100, 11100, 15000, 1),
(46, 'Biji Jagung AP 101', 500, 500, 1000, 15, 20),
(47, 'Kertas Fotocopy A4', 5, 5, 10, 65000, 25),
(48, 'lala', 50, 50, 100, 150000, 23),
(49, 'Korek kayu', 223, 223, 300, 5000, 6);

-- --------------------------------------------------------

--
-- Table structure for table `detail_nota_pembelian`
--

CREATE TABLE `detail_nota_pembelian` (
  `barang_idbarang` int(11) NOT NULL,
  `Nota_pembelian_idNota_pembelian` int(11) NOT NULL,
  `harga_beli` int(10) DEFAULT NULL,
  `jumlah` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `detail_nota_pembelian`
--

INSERT INTO `detail_nota_pembelian` (`barang_idbarang`, `Nota_pembelian_idNota_pembelian`, `harga_beli`, `jumlah`) VALUES
(15, 2, 150, 1000),
(16, 3, 300, 500),
(27, 3, 5000, 100),
(15, 101, 1, 1),
(37, 102, 200, 700),
(38, 102, 13500, 150),
(15, 103, 22, 3413),
(16, 1, 10, 10),
(49, 104, 4000, 200),
(15, 105, 200, 100);

-- --------------------------------------------------------

--
-- Table structure for table `detail_nota_pengiriman`
--

CREATE TABLE `detail_nota_pengiriman` (
  `pengiriman_idnota_pengiriman` int(11) NOT NULL,
  `barang_idbarang` int(11) NOT NULL,
  `biaya_transport` int(10) DEFAULT NULL,
  `jumlah_barang` int(10) DEFAULT NULL,
  `Nota_pembelian_id` varchar(45) DEFAULT NULL,
  `jumlah_bagus` int(10) DEFAULT NULL,
  `jumlah_rusak` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `detail_nota_pengiriman`
--

INSERT INTO `detail_nota_pengiriman` (`pengiriman_idnota_pengiriman`, `barang_idbarang`, `biaya_transport`, `jumlah_barang`, `Nota_pembelian_id`, `jumlah_bagus`, `jumlah_rusak`) VALUES
(25, 15, 50000, 100, '2', 90, 10),
(26, 15, 700000, 500, '3', 500, 0),
(26, 27, 60000, 20, '3', 20, 0),
(26, 15, 700000, 500, '3', 500, 0),
(26, 16, 0, 100, '3', 100, 0),
(26, 38, 0, 11, '102', 11, 0),
(31, 16, 2, 2, '1', 0, 2),
(31, 15, 100000, 100, '2', 98, 2),
(32, 15, 0, 1, '101', 1, 0),
(33, 49, 70000, 100, '104', 90, 10);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pemesanan`
--

CREATE TABLE `detail_pemesanan` (
  `barang_idbarang` int(11) NOT NULL,
  `pemesanan_idpemesanan` int(11) NOT NULL,
  `jumlah_barang` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `detail_pemesanan`
--

INSERT INTO `detail_pemesanan` (`barang_idbarang`, `pemesanan_idpemesanan`, `jumlah_barang`) VALUES
(15, 2, 1000),
(27, 3, 15),
(16, 12, 500),
(15, 13, 1234455),
(25, 13, 53513),
(46, 14, 150),
(15, 14, 423),
(15, 15, 200),
(25, 15, 900),
(49, 16, 800),
(15, 17, 100),
(15, 18, 100);

-- --------------------------------------------------------

--
-- Table structure for table `distributor`
--

CREATE TABLE `distributor` (
  `iddistributor` int(11) NOT NULL,
  `perusahaan` varchar(45) DEFAULT NULL,
  `kontak` varchar(45) DEFAULT NULL,
  `notlfn` varchar(45) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `distributor`
--

INSERT INTO `distributor` (`iddistributor`, `perusahaan`, `kontak`, `notlfn`, `alamat`) VALUES
(1, 'PT Sinar Bahagia', 'Asih', '09862343112', 'Jl. Panjang Jiwo'),
(2, 'Toko Mitra Utama', 'Bagus (Sales)', '087183637348', 'Jl.Ayani'),
(3, 'PT.Kembang', 'Taufik', '09723582413', 'Jl. Swamena Asmat'),
(13, 'PT.Kembang Obeng', 'KIKI', '09723582413', 'Jl. Pajajaran Semarang'),
(14, 'Toko Kereta Hidup', 'KIKI', '098623477364', 'JL. Sabanah'),
(15, 'Mitra 101', 'Pak ari', '0986383932', 'Jl. Ayani Surabaya'),
(16, 'Raja Seluler', 'Jaka', '097223', 'Jl. Ayani Surabaya'),
(30, 'Coke', 'Ari', '933413413', 'Jalan Spada No 3'),
(31, 'SI GERCEP', 'Kera Sakti', '5556748523', 'Jalan jalan'),
(32, 'SI GERCEP', 'Kera Sakti', '5556748523', 'Jalan jalan'),
(33, 'Ditributor Paku Payung and Seng', 'Rahman', '0923413513', 'Jalan Nusa Bangsa no 12');

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `idgudang` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `alamat` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gudang`
--

INSERT INTO `gudang` (`idgudang`, `nama`, `alamat`) VALUES
(1, 'Merauke', 'Jl. Ayani'),
(2, 'Asmat', 'Jl. Swamena No 12'),
(3, 'Surabaya', 'Jl. Gayungan'),
(4, 'Kepi', 'Jl. A.yani'),
(5, 'Jayapura', 'Jalan Sentani'),
(6, 'Jakarta', 'Jl. Tj Priok'),
(11, 'Asmat 2', 'Jalan Sawaerma'),
(12, 'USA', 'ALA+BAMA'),
(13, 'Surabaya', 'Jl A yani'),
(14, 'ASIKI', 'KM 19'),
(15, 'JKJK', 'Jalan. Kecanan'),
(16, 'Sweeden', 'Jl Soekarno');

-- --------------------------------------------------------

--
-- Table structure for table `gudang_has_histori_barang`
--

CREATE TABLE `gudang_has_histori_barang` (
  `gudang_idgudang` int(11) NOT NULL,
  `histori_barang_idhistori_barang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gudang_has_histori_barang`
--

INSERT INTO `gudang_has_histori_barang` (`gudang_idgudang`, `histori_barang_idhistori_barang`) VALUES
(1, 2),
(1, 3),
(1, 4),
(1, 9),
(1, 12),
(2, 8),
(3, 13),
(6, 14);

-- --------------------------------------------------------

--
-- Table structure for table `halaman`
--

CREATE TABLE `halaman` (
  `idhalaman` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `nama_halaman` varchar(100) DEFAULT NULL,
  `link` varchar(100) DEFAULT NULL,
  `link_web` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `halaman`
--

INSERT INTO `halaman` (`idhalaman`, `nama`, `nama_halaman`, `link`, `link_web`) VALUES
(1, 'gudang_area', 'Gudang', '/gudang', 'gudang'),
(2, 'barang', 'Barang', '/barang', 'barang'),
(3, 'notapengiriman', 'Nota Pengiriman', '/nota-pengiriman', 'notapengiriman'),
(4, 'notapembelian', 'Nota Pembelian', '/nota-pembelian', 'notapem'),
(5, 'pemesanan', 'Pemesanan Barang', '/pemesanan', 'pemesanan'),
(6, 'histori_barang', 'Barang Keluar', '/barang-keluar', 'barangkeluar'),
(7, 'register', 'Register', '/register', 'register'),
(8, 'jabatan', 'Jabatan', '/jabatan', 'jabatan'),
(9, 'transport', 'Transportasi', '/trans', 'trans'),
(10, 'distributor', 'Distributor', '/distributor', 'distri');

-- --------------------------------------------------------

--
-- Table structure for table `histori_barang`
--

CREATE TABLE `histori_barang` (
  `idhistori_barang` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `no_nota` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `histori_barang`
--

INSERT INTO `histori_barang` (`idhistori_barang`, `tanggal`, `no_nota`) VALUES
(2, '2020-05-11', '001/00001'),
(3, '2020-05-28', '001/22/0012'),
(4, '2020-06-04', '001/22/001'),
(8, '2020-09-17', '12334'),
(9, '2020-10-15', '1123'),
(12, '2020-10-20', '123Keluar'),
(13, '2020-10-20', 'Keluar-20-10'),
(14, '1970-01-01', '1123');

-- --------------------------------------------------------

--
-- Table structure for table `histori_barang_keluar`
--

CREATE TABLE `histori_barang_keluar` (
  `barang_idbarang` int(11) NOT NULL,
  `histori_barang_idhistori_barang` int(11) NOT NULL,
  `jumlah` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `histori_barang_keluar`
--

INSERT INTO `histori_barang_keluar` (`barang_idbarang`, `histori_barang_idhistori_barang`, `jumlah`) VALUES
(26, 2, 100),
(27, 2, 100),
(15, 3, 50),
(25, 9, 50),
(15, 12, 100),
(36, 12, 1000),
(36, 4, 8),
(26, 13, 20),
(31, 13, 10),
(49, 14, 11);

-- --------------------------------------------------------

--
-- Table structure for table `hubungan_nota`
--

CREATE TABLE `hubungan_nota` (
  `nota_pengiriman_idnota_pengiriman` int(11) NOT NULL,
  `Nota_pembelian_idNota_pembelian` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hubungan_nota`
--

INSERT INTO `hubungan_nota` (`nota_pengiriman_idnota_pengiriman`, `Nota_pembelian_idNota_pembelian`) VALUES
(25, 2),
(26, 2),
(26, 3),
(26, 101),
(26, 102),
(31, 1),
(31, 2),
(32, 101),
(33, 104);

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `idjabatan` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`idjabatan`, `nama`) VALUES
(1, 'USER'),
(3, 'ADMIN'),
(4, 'Kuli'),
(5, 'Penjaga');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan_has_halaman`
--

CREATE TABLE `jabatan_has_halaman` (
  `jabatan_idjabatan` int(11) NOT NULL,
  `halaman_idhalaman` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jabatan_has_halaman`
--

INSERT INTO `jabatan_has_halaman` (`jabatan_idjabatan`, `halaman_idhalaman`) VALUES
(1, 1),
(1, 2),
(1, 3),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jejak_barang`
--

CREATE TABLE `jejak_barang` (
  `barang_idbarang` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_barang`
--

INSERT INTO `jejak_barang` (`barang_idbarang`, `users_id`, `keterangan`, `tanggal`) VALUES
(15, 2, 'edit', NULL),
(26, 2, 'edit', '2020-09-02'),
(26, 3, 'create', '2020-09-01'),
(15, 2, 'edit', '2020-10-05'),
(17, 2, 'edit', '2020-10-06'),
(17, 2, 'edit', '2020-10-06'),
(46, 2, 'create', '2020-10-18'),
(46, 2, 'edit', '2020-10-18'),
(46, 2, 'edit', '2020-10-18'),
(46, 2, 'edit', '2020-10-18'),
(47, 2, 'create', '2020-10-20'),
(47, 2, 'edit', '2020-10-20'),
(48, 2, 'create', '2020-10-20'),
(49, 2, 'create', '2020-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_distributor`
--

CREATE TABLE `jejak_distributor` (
  `distributor_iddistributor` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_distributor`
--

INSERT INTO `jejak_distributor` (`distributor_iddistributor`, `users_id`, `keterangan`, `tanggal`) VALUES
(3, 3, 'create', '2020-09-01'),
(15, 3, 'create', '2020-09-01'),
(30, 2, 'create', '2020-10-06'),
(31, 2, 'create', '2020-10-06'),
(32, 2, 'create', '2020-10-06'),
(33, 2, 'create', '2020-10-20'),
(33, 2, 'edit', '2020-10-20'),
(33, 2, 'edit', '2020-10-20');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_gudang_area`
--

CREATE TABLE `jejak_gudang_area` (
  `gudang_idgudang` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` varchar(45) DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_gudang_area`
--

INSERT INTO `jejak_gudang_area` (`gudang_idgudang`, `users_id`, `keterangan`, `tanggal`) VALUES
(2, 3, 'tambah area', '2020-09-16'),
(2, 3, 'tambah area', '2020-09-16'),
(1, 2, 'ubah gudang', '2020-10-06'),
(13, 2, 'tambah gudang dan area', '2020-10-09'),
(14, 2, 'tambah gudang dan area', '2020-10-09'),
(15, 2, 'tambah gudang dan area', '2020-10-18'),
(15, 2, 'ubah gudang', '2020-10-18'),
(15, 2, 'ubah gudang', '2020-10-18'),
(15, 2, 'ubah gudang', '2020-10-18'),
(16, 2, 'tambah gudang dan area', '2020-10-20'),
(16, 2, 'edit gudang', '2020-10-20'),
(16, 2, 'edit area', '2020-10-20'),
(16, 2, 'edit area', '2020-10-20'),
(16, 2, 'edit area', '2020-10-20');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_histori_barang`
--

CREATE TABLE `jejak_histori_barang` (
  `users_id` int(11) NOT NULL,
  `histori_barang_idhistori_barang` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_histori_barang`
--

INSERT INTO `jejak_histori_barang` (`users_id`, `histori_barang_idhistori_barang`, `keterangan`, `tanggal`) VALUES
(2, 2, 'create', '2020-09-22'),
(2, 2, 'edit', '2020-09-22'),
(2, 3, 'edit', '2020-10-06'),
(2, 9, 'create', '2020-10-15'),
(2, 12, 'create', '2020-10-18'),
(2, 12, 'edit', '2020-10-18'),
(2, 12, 'edit', '2020-10-18'),
(2, 4, 'edit', '2020-10-18'),
(2, 13, 'create', '2020-10-20'),
(2, 13, 'edit', '2020-10-20'),
(2, 14, 'create', '2020-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_notapembelian`
--

CREATE TABLE `jejak_notapembelian` (
  `Nota_pembelian_idNota_pembelian` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_notapembelian`
--

INSERT INTO `jejak_notapembelian` (`Nota_pembelian_idNota_pembelian`, `users_id`, `keterangan`, `tanggal`) VALUES
(2, 2, 'edit', '2020-10-05'),
(2, 2, 'edit', '2020-10-05'),
(3, 2, 'edit', '2020-10-07'),
(3, 2, 'edit', '2020-10-07'),
(3, 2, 'edit', '2020-10-07'),
(3, 2, 'edit', '2020-10-07'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(3, 2, 'edit', '2020-10-08'),
(101, 2, 'create', '2020-10-09'),
(102, 2, 'create', '2020-10-19'),
(102, 2, 'edit', '2020-10-19'),
(103, 2, 'create', '2020-10-21'),
(1, 2, 'edit', '2020-10-21'),
(104, 2, 'create', '2020-10-23'),
(105, 2, 'create', '2020-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_notapengiriman`
--

CREATE TABLE `jejak_notapengiriman` (
  `nota_pengiriman_idnota_pengiriman` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_notapengiriman`
--

INSERT INTO `jejak_notapengiriman` (`nota_pengiriman_idnota_pengiriman`, `users_id`, `keterangan`, `tanggal`) VALUES
(25, 2, 'create', '2020-10-11'),
(26, 2, 'create', '2020-10-19'),
(26, 2, 'edit', '2020-10-19'),
(26, 2, 'edit', '2020-10-19'),
(26, 2, 'edit', '2020-10-19'),
(26, 2, 'edit', '2020-10-19'),
(26, 2, 'edit', '2020-10-19'),
(31, 2, 'create', '2020-10-22'),
(31, 2, 'edit', '2020-10-22'),
(31, 2, 'edit', '2020-10-22'),
(31, 2, 'edit', '2020-10-22'),
(32, 2, 'create', '2020-10-23'),
(33, 2, 'create', '2020-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_pemesanan`
--

CREATE TABLE `jejak_pemesanan` (
  `pemesanan_idpemesanan` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_pemesanan`
--

INSERT INTO `jejak_pemesanan` (`pemesanan_idpemesanan`, `users_id`, `keterangan`, `tanggal`) VALUES
(2, 2, 'edit', NULL),
(3, 2, 'edit', '2020-10-07'),
(12, 2, 'create', '2020-10-08'),
(13, 2, 'create', '2020-10-19'),
(14, 2, 'create', '2020-10-19'),
(14, 2, 'edit', '2020-10-19'),
(15, 2, 'create', '2020-10-23'),
(16, 2, 'create', '2020-10-23'),
(17, 2, 'create', '2020-10-23'),
(18, 2, 'create', '2020-10-27');

-- --------------------------------------------------------

--
-- Table structure for table `jejak_transport`
--

CREATE TABLE `jejak_transport` (
  `transport_idtransport` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `keterangan` enum('create','edit') DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jejak_transport`
--

INSERT INTO `jejak_transport` (`transport_idtransport`, `users_id`, `keterangan`, `tanggal`) VALUES
(5, 2, 'create', '2020-10-06');

-- --------------------------------------------------------

--
-- Table structure for table `nota_pembelian`
--

CREATE TABLE `nota_pembelian` (
  `idNota_pembelian` int(11) NOT NULL,
  `no_nota` varchar(45) DEFAULT NULL,
  `tanggal` timestamp(6) NULL DEFAULT NULL,
  `total_nilai_pembelian` int(10) DEFAULT NULL,
  `distributor_iddistributor` int(11) NOT NULL,
  `estimasi` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nota_pembelian`
--

INSERT INTO `nota_pembelian` (`idNota_pembelian`, `no_nota`, `tanggal`, `total_nilai_pembelian`, `distributor_iddistributor`, `estimasi`) VALUES
(1, '1', '2020-10-08 17:00:00.000000', 100, 13, '2020-10-20 17:00:00.000000'),
(2, '001/00001', '2020-05-14 17:00:00.000000', 150000, 1, NULL),
(3, '001/00002', '2020-05-21 17:00:00.000000', 650000, 3, '2020-06-30 17:00:00.000000'),
(4, '001/22/001', '2020-05-28 17:00:00.000000', 365000000, 15, NULL),
(5, '001/22/002', '2020-06-01 17:00:00.000000', 2150000, 1, NULL),
(6, '143', '2020-09-14 17:00:00.000000', 41000000, 2, NULL),
(7, '143', '2020-09-14 17:00:00.000000', 41000000, 2, NULL),
(8, '001/00001', '2020-05-14 17:00:00.000000', 1400000, 1, NULL),
(9, '001/00001', '2020-05-14 17:00:00.000000', 1300000, 1, NULL),
(10, '001/00001', '2020-05-14 17:00:00.000000', 1300000, 1, NULL),
(11, '001/00001', '2020-05-14 17:00:00.000000', 1300000, 1, NULL),
(101, '112', '2020-10-08 17:00:00.000000', 1, 13, '2020-10-08 17:00:00.000000'),
(102, 'UJ100-90-123', '2020-10-18 17:00:00.000000', 2165000, 30, '2020-10-18 17:00:00.000000'),
(103, '234423', '2020-10-20 17:00:00.000000', 75086, 30, '2020-10-28 17:00:00.000000'),
(104, '1299-Pembelian', '2020-10-22 17:00:00.000000', 800000, 1, '2020-10-30 17:00:00.000000'),
(105, '111/223', '2020-10-22 17:00:00.000000', 20000, 2, '2020-10-29 17:00:00.000000');

-- --------------------------------------------------------

--
-- Table structure for table `nota_pengiriman`
--

CREATE TABLE `nota_pengiriman` (
  `idnota_pengiriman` int(11) NOT NULL,
  `tanggal_dikirim` date DEFAULT NULL,
  `tanggal_diterima` date DEFAULT NULL,
  `no_nota` varchar(45) DEFAULT NULL,
  `total_biaya_transport` int(10) DEFAULT NULL,
  `transport_idtransport` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nota_pengiriman`
--

INSERT INTO `nota_pengiriman` (`idnota_pengiriman`, `tanggal_dikirim`, `tanggal_diterima`, `no_nota`, `total_biaya_transport`, `transport_idtransport`) VALUES
(25, '2020-08-12', '2020-10-12', '11/1/', 50000, 1),
(26, '2020-07-15', '2020-10-19', '123-Datang', 700000, 1),
(31, '2020-10-18', '2020-10-22', '123445/Pengiriman', 100002, 2),
(32, '2020-10-19', '2020-10-23', '123.Penerimaan', 0, 2),
(33, '2020-10-23', '2020-10-23', '11/pengiriman.23', 70000, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `idpemesanan` int(11) NOT NULL,
  `tanggal_pembuatan` timestamp(6) NULL DEFAULT NULL,
  `tanggal_dipesan` timestamp(6) NULL DEFAULT NULL,
  `no_nota` varchar(45) DEFAULT NULL,
  `distributor_iddistributor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`idpemesanan`, `tanggal_pembuatan`, `tanggal_dipesan`, `no_nota`, `distributor_iddistributor`) VALUES
(2, '2020-05-11 17:00:00.000000', '2020-05-15 17:00:00.000000', '001/00011', 0),
(3, '2020-05-11 17:00:00.000000', '2020-05-31 17:00:00.000000', '001/00002', 0),
(4, '2020-05-11 17:00:00.000000', '2020-07-19 17:00:00.000000', '001/00003', 0),
(5, '2020-05-11 17:00:00.000000', '2020-08-30 17:00:00.000000', '001/00004', 0),
(6, '2020-05-11 17:00:00.000000', '2020-08-30 17:00:00.000000', '001/00005', 0),
(8, '2020-05-12 17:00:00.000000', '2020-05-23 17:00:00.000000', '001/00006', 0),
(9, '2020-05-27 17:00:00.000000', '2020-05-30 17:00:00.000000', '001/22/001', 0),
(10, '2020-06-03 17:00:00.000000', '2020-06-09 17:00:00.000000', '001/001', 0),
(11, '2020-09-16 17:00:00.000000', '2020-09-17 17:00:00.000000', '14445', 0),
(12, '2020-10-07 17:00:00.000000', '2020-10-08 17:00:00.000000', '123567', 0),
(13, '2020-10-18 17:00:00.000000', '2020-10-18 17:00:00.000000', '112234Pesan', 0),
(14, '2020-10-18 17:00:00.000000', '2020-10-18 17:00:00.000000', 'pesan-111/2', 0),
(15, '2020-10-22 17:00:00.000000', '2020-10-22 17:00:00.000000', '123.6378', 0),
(16, '2020-10-22 17:00:00.000000', '2020-10-22 17:00:00.000000', '123.Pemsanan23', 0),
(17, '2020-10-22 17:00:00.000000', '2020-10-22 17:00:00.000000', '12345/Pemesanan', 0),
(18, '2020-10-26 17:00:00.000000', '2020-10-26 17:00:00.000000', '20/10/27/18', 3);

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `idsatuan` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`idsatuan`, `nama`) VALUES
(1, 'pcs'),
(2, 'kg'),
(3, 'lembar'),
(4, 'pail'),
(5, 'meter'),
(6, 'box'),
(18, 'pak'),
(19, 'ikat'),
(20, 'biji'),
(21, 'kubik'),
(22, 'batang'),
(23, 'set'),
(24, 'set'),
(25, 'rim'),
(26, 'kubikasi');

-- --------------------------------------------------------

--
-- Table structure for table `satuan_simpan`
--

CREATE TABLE `satuan_simpan` (
  `satuan_idsatuan` int(11) NOT NULL,
  `barang_idbarang` int(11) NOT NULL,
  `konversi` int(10) DEFAULT NULL,
  `keterangan` enum('satuan jual','satuan simpan') DEFAULT NULL,
  `harga_jual` int(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `satuan_simpan`
--

INSERT INTO `satuan_simpan` (`satuan_idsatuan`, `barang_idbarang`, `konversi`, `keterangan`, `harga_jual`) VALUES
(4, 17, 500, 'satuan jual', 20000000),
(6, 46, 50, 'satuan jual', 5000),
(6, 46, 100, 'satuan simpan', 0),
(4, 46, 1000, 'satuan simpan', 0),
(26, 47, 10, 'satuan jual', 6500000),
(3, 47, 500, 'satuan jual', 200),
(26, 47, 10, 'satuan simpan', 0),
(6, 49, 1, 'satuan simpan', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transport`
--

CREATE TABLE `transport` (
  `idtransport` int(11) NOT NULL,
  `perusahaan` varchar(45) DEFAULT NULL,
  `kontak` varchar(45) DEFAULT NULL,
  `notlfn` varchar(45) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transport`
--

INSERT INTO `transport` (`idtransport`, `perusahaan`, `kontak`, `notlfn`, `alamat`) VALUES
(1, 'PT Irian Jaya Express', 'Taufik', '098623477364', 'Jl. Pajajaran Semarang'),
(2, 'JNE Surabaya', 'Mas Jor', '09723582413', 'JL. Ahmad Yani'),
(3, 'JNT Surabaya', 'Jusman', '098623477364', 'dfadf'),
(4, 'TIKI', 'Jaka', '097223', 'Jl. Ayani Surabaya'),
(5, 'SI Gercep', 'Udin', '764525', 'Jalan Kembang Merah');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  `jabatan_idjabatan` int(11) NOT NULL,
  `pwd_hp` varchar(10000) NOT NULL,
  `tag` varchar(10000) NOT NULL,
  `iv` varchar(1000) NOT NULL,
  `remember_token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `password`, `user_id`, `jabatan_idjabatan`, `pwd_hp`, `tag`, `iv`, `remember_token`) VALUES
(2, 'Admin Ganteng', '$2y$10$1ci5K0wOAVxxJTCnKBUBjOqFaIrbS72XXYayaz/Qz9qNJ3EmrG03S', 'adm1', 3, 'udcfoYY2eNdQAQ==', '4l\n¸Ý‹Òúlˆ«Ÿ¢¢', 'Ò6ÝÃuŠ¸UÚan', 'PZW8GJjWqgDgVsBCIDwXHYhpzuJD70W8L5Q5rsQuKH7HkC3bOUfZvZhN2ff1'),
(3, 'din', NULL, 'usin', 0, 'eOvO', '9>!a/Wo%•G´\nJ', 'ö~ØKÉNSîÈDÄ', ''),
(4, 'nurdin', '$2y$10$eix2Xxelb3nRG3QM862G7OCN0nG3u0vHLZelmZKbHmlp/RF6v.W0C', 'nurdin', 0, '0', '0', '0', 'NkhwgCO9S8lVSGfSjGRkkdUqGaVFzW9lsUnFRoXtxSUfbPawVDv0dqYPveWa'),
(5, 'Roy Matulangi', '$2y$10$Au07burBu09cblmRs/Z8BeqihD3K4UHiWqG8WrBUl.SIKTjdil4Au', 'roy', 5, '0', '0', '0', 'qhQv1bUVVggOUbzgPr4n0NoaX4A58X2n8KRVk9sv2nz52iCsLz9C0yXOU7e1'),
(6, 'Andi Palaurang', '$2y$10$p0z/oXLhZLKeRo/OAR.EN.bcrtSLJKw00vc0zgavlzSUKLVer7cY2', 'andi', 4, '0', '0', '0', 'HQbcMJ0X93Is4M6oDIuFj2P4X1uYpNghPOmEb0P1x1FWef3sWIvPFGlzPXbT'),
(7, 'Raku Wibu', '$2y$10$Ci1HkmhV0ZV/bfoxXd8V7uktRBJS8Fy6i6Y9qYYfV9wzAqFK6xqbq', 'raku', 4, '0', '0', '0', 'F8haqWsixqWlVkWyKU2SKZZVxaudHeoiOF2flsqVgA98lzcr4guxE3UFfmWb');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`idarea`),
  ADD KEY `fk_area_gudang1_idx` (`gudang_idgudang`);

--
-- Indexes for table `area_has_barang`
--
ALTER TABLE `area_has_barang`
  ADD PRIMARY KEY (`area_idarea`,`barang_idbarang`),
  ADD KEY `fk_area_has_barang_barang1_idx` (`barang_idbarang`),
  ADD KEY `fk_area_has_barang_area1_idx` (`area_idarea`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`idbarang`),
  ADD KEY `fk_barang_satuan_idx` (`satuan_idsatuan`);

--
-- Indexes for table `detail_nota_pembelian`
--
ALTER TABLE `detail_nota_pembelian`
  ADD KEY `fk_barang_has_Nota_pembelian_Nota_pembelian1_idx` (`Nota_pembelian_idNota_pembelian`),
  ADD KEY `fk_barang_has_Nota_pembelian_barang1_idx` (`barang_idbarang`);

--
-- Indexes for table `detail_nota_pengiriman`
--
ALTER TABLE `detail_nota_pengiriman`
  ADD KEY `fk_nota_pengiriman_has_barang_barang1_idx` (`barang_idbarang`),
  ADD KEY `fk_nota_pengiriman_has_barang_nota_pengiriman1_idx` (`pengiriman_idnota_pengiriman`);

--
-- Indexes for table `detail_pemesanan`
--
ALTER TABLE `detail_pemesanan`
  ADD KEY `fk_barang_has_pemesanan_pemesanan1_idx` (`pemesanan_idpemesanan`),
  ADD KEY `fk_barang_has_pemesanan_barang1_idx` (`barang_idbarang`);

--
-- Indexes for table `distributor`
--
ALTER TABLE `distributor`
  ADD PRIMARY KEY (`iddistributor`);

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`idgudang`);

--
-- Indexes for table `gudang_has_histori_barang`
--
ALTER TABLE `gudang_has_histori_barang`
  ADD PRIMARY KEY (`gudang_idgudang`,`histori_barang_idhistori_barang`),
  ADD KEY `fk_gudang_has_histori_barang_histori_barang1_idx` (`histori_barang_idhistori_barang`),
  ADD KEY `fk_gudang_has_histori_barang_gudang1_idx` (`gudang_idgudang`);

--
-- Indexes for table `halaman`
--
ALTER TABLE `halaman`
  ADD PRIMARY KEY (`idhalaman`);

--
-- Indexes for table `histori_barang`
--
ALTER TABLE `histori_barang`
  ADD PRIMARY KEY (`idhistori_barang`);

--
-- Indexes for table `histori_barang_keluar`
--
ALTER TABLE `histori_barang_keluar`
  ADD KEY `fk_barang_has_histori_barang_histori_barang1_idx` (`histori_barang_idhistori_barang`),
  ADD KEY `fk_barang_has_histori_barang_barang1_idx` (`barang_idbarang`);

--
-- Indexes for table `hubungan_nota`
--
ALTER TABLE `hubungan_nota`
  ADD PRIMARY KEY (`nota_pengiriman_idnota_pengiriman`,`Nota_pembelian_idNota_pembelian`),
  ADD KEY `fk_nota_pengiriman_has_Nota_pembelian_Nota_pembelian1_idx` (`Nota_pembelian_idNota_pembelian`),
  ADD KEY `fk_nota_pengiriman_has_Nota_pembelian_nota_pengiriman1_idx` (`nota_pengiriman_idnota_pengiriman`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`idjabatan`);

--
-- Indexes for table `jabatan_has_halaman`
--
ALTER TABLE `jabatan_has_halaman`
  ADD PRIMARY KEY (`jabatan_idjabatan`,`halaman_idhalaman`),
  ADD KEY `fk_jabatan_has_halaman_halaman1_idx` (`halaman_idhalaman`),
  ADD KEY `fk_jabatan_has_halaman_jabatan1_idx` (`jabatan_idjabatan`);

--
-- Indexes for table `jejak_barang`
--
ALTER TABLE `jejak_barang`
  ADD KEY `fk_barang_has_users_users1_idx` (`users_id`),
  ADD KEY `fk_barang_has_users_barang1_idx` (`barang_idbarang`);

--
-- Indexes for table `jejak_distributor`
--
ALTER TABLE `jejak_distributor`
  ADD KEY `fk_distributor_has_users_users1_idx` (`users_id`),
  ADD KEY `fk_distributor_has_users_distributor1_idx` (`distributor_iddistributor`);

--
-- Indexes for table `jejak_gudang_area`
--
ALTER TABLE `jejak_gudang_area`
  ADD KEY `fk_gudang_has_users_users1_idx` (`users_id`),
  ADD KEY `fk_gudang_has_users_gudang1_idx` (`gudang_idgudang`);

--
-- Indexes for table `jejak_histori_barang`
--
ALTER TABLE `jejak_histori_barang`
  ADD KEY `fk_user_has_histori_barang_histori_barang1_idx` (`histori_barang_idhistori_barang`),
  ADD KEY `fk_user_has_histori_barang_user1_idx` (`users_id`);

--
-- Indexes for table `jejak_notapembelian`
--
ALTER TABLE `jejak_notapembelian`
  ADD KEY `fk_Nota_pembelian_has_user_user1_idx` (`users_id`),
  ADD KEY `fk_Nota_pembelian_has_user_Nota_pembelian1_idx` (`Nota_pembelian_idNota_pembelian`);

--
-- Indexes for table `jejak_notapengiriman`
--
ALTER TABLE `jejak_notapengiriman`
  ADD KEY `fk_nota_pengiriman_has_users_users1_idx` (`users_id`),
  ADD KEY `fk_nota_pengiriman_has_users_nota_pengiriman1_idx` (`nota_pengiriman_idnota_pengiriman`);

--
-- Indexes for table `jejak_pemesanan`
--
ALTER TABLE `jejak_pemesanan`
  ADD KEY `fk_pemesanan_has_user_user1_idx` (`users_id`),
  ADD KEY `fk_pemesanan_has_user_pemesanan1_idx` (`pemesanan_idpemesanan`);

--
-- Indexes for table `jejak_transport`
--
ALTER TABLE `jejak_transport`
  ADD KEY `fk_transport_has_users_users1_idx` (`users_id`),
  ADD KEY `fk_transport_has_users_transport1_idx` (`transport_idtransport`);

--
-- Indexes for table `nota_pembelian`
--
ALTER TABLE `nota_pembelian`
  ADD PRIMARY KEY (`idNota_pembelian`),
  ADD KEY `fk_Nota_pembelian_distributor1_idx` (`distributor_iddistributor`);

--
-- Indexes for table `nota_pengiriman`
--
ALTER TABLE `nota_pengiriman`
  ADD PRIMARY KEY (`idnota_pengiriman`),
  ADD KEY `fk_nota_pengiriman_transport1_idx` (`transport_idtransport`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`idpemesanan`),
  ADD KEY `fk_pemesanan_distributor1_idx` (`distributor_iddistributor`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`idsatuan`);

--
-- Indexes for table `satuan_simpan`
--
ALTER TABLE `satuan_simpan`
  ADD KEY `fk_satuan_has_barang_barang1_idx` (`barang_idbarang`),
  ADD KEY `fk_satuan_has_barang_satuan1_idx` (`satuan_idsatuan`);

--
-- Indexes for table `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`idtransport`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  ADD KEY `fk_users_jabatan1_idx` (`jabatan_idjabatan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `idarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `idbarang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `distributor`
--
ALTER TABLE `distributor`
  MODIFY `iddistributor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `idgudang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `halaman`
--
ALTER TABLE `halaman`
  MODIFY `idhalaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `histori_barang`
--
ALTER TABLE `histori_barang`
  MODIFY `idhistori_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `idjabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nota_pembelian`
--
ALTER TABLE `nota_pembelian`
  MODIFY `idNota_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `nota_pengiriman`
--
ALTER TABLE `nota_pengiriman`
  MODIFY `idnota_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `idpemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `idsatuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `transport`
--
ALTER TABLE `transport`
  MODIFY `idtransport` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `area`
--
ALTER TABLE `area`
  ADD CONSTRAINT `fk_area_gudang1` FOREIGN KEY (`gudang_idgudang`) REFERENCES `gudang` (`idgudang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `area_has_barang`
--
ALTER TABLE `area_has_barang`
  ADD CONSTRAINT `fk_area_has_barang_area1` FOREIGN KEY (`area_idarea`) REFERENCES `area` (`idarea`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_area_has_barang_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_satuan` FOREIGN KEY (`satuan_idsatuan`) REFERENCES `satuan` (`idsatuan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `detail_nota_pembelian`
--
ALTER TABLE `detail_nota_pembelian`
  ADD CONSTRAINT `fk_barang_has_Nota_pembelian_Nota_pembelian1` FOREIGN KEY (`Nota_pembelian_idNota_pembelian`) REFERENCES `nota_pembelian` (`idNota_pembelian`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_barang_has_Nota_pembelian_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `detail_nota_pengiriman`
--
ALTER TABLE `detail_nota_pengiriman`
  ADD CONSTRAINT `fk_nota_pengiriman_has_barang_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_nota_pengiriman_has_barang_nota_pengiriman1` FOREIGN KEY (`pengiriman_idnota_pengiriman`) REFERENCES `nota_pengiriman` (`idnota_pengiriman`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `detail_pemesanan`
--
ALTER TABLE `detail_pemesanan`
  ADD CONSTRAINT `fk_barang_has_pemesanan_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_barang_has_pemesanan_pemesanan1` FOREIGN KEY (`pemesanan_idpemesanan`) REFERENCES `pemesanan` (`idpemesanan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `gudang_has_histori_barang`
--
ALTER TABLE `gudang_has_histori_barang`
  ADD CONSTRAINT `fk_gudang_has_histori_barang_gudang1` FOREIGN KEY (`gudang_idgudang`) REFERENCES `gudang` (`idgudang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_gudang_has_histori_barang_histori_barang1` FOREIGN KEY (`histori_barang_idhistori_barang`) REFERENCES `histori_barang` (`idhistori_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `histori_barang_keluar`
--
ALTER TABLE `histori_barang_keluar`
  ADD CONSTRAINT `fk_barang_has_histori_barang_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_barang_has_histori_barang_histori_barang1` FOREIGN KEY (`histori_barang_idhistori_barang`) REFERENCES `histori_barang` (`idhistori_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `hubungan_nota`
--
ALTER TABLE `hubungan_nota`
  ADD CONSTRAINT `fk_nota_pengiriman_has_Nota_pembelian_Nota_pembelian1` FOREIGN KEY (`Nota_pembelian_idNota_pembelian`) REFERENCES `nota_pembelian` (`idNota_pembelian`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_nota_pengiriman_has_Nota_pembelian_nota_pengiriman1` FOREIGN KEY (`nota_pengiriman_idnota_pengiriman`) REFERENCES `nota_pengiriman` (`idnota_pengiriman`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jabatan_has_halaman`
--
ALTER TABLE `jabatan_has_halaman`
  ADD CONSTRAINT `fk_jabatan_has_halaman_halaman1` FOREIGN KEY (`halaman_idhalaman`) REFERENCES `halaman` (`idhalaman`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_jabatan_has_halaman_jabatan1` FOREIGN KEY (`jabatan_idjabatan`) REFERENCES `jabatan` (`idjabatan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_barang`
--
ALTER TABLE `jejak_barang`
  ADD CONSTRAINT `fk_barang_has_users_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_barang_has_users_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_distributor`
--
ALTER TABLE `jejak_distributor`
  ADD CONSTRAINT `fk_distributor_has_users_distributor1` FOREIGN KEY (`distributor_iddistributor`) REFERENCES `distributor` (`iddistributor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_distributor_has_users_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_gudang_area`
--
ALTER TABLE `jejak_gudang_area`
  ADD CONSTRAINT `fk_gudang_has_users_gudang1` FOREIGN KEY (`gudang_idgudang`) REFERENCES `gudang` (`idgudang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_gudang_has_users_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_histori_barang`
--
ALTER TABLE `jejak_histori_barang`
  ADD CONSTRAINT `fk_user_has_histori_barang_histori_barang1` FOREIGN KEY (`histori_barang_idhistori_barang`) REFERENCES `histori_barang` (`idhistori_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_has_histori_barang_user1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_notapembelian`
--
ALTER TABLE `jejak_notapembelian`
  ADD CONSTRAINT `fk_Nota_pembelian_has_user_Nota_pembelian1` FOREIGN KEY (`Nota_pembelian_idNota_pembelian`) REFERENCES `nota_pembelian` (`idNota_pembelian`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Nota_pembelian_has_user_user1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_notapengiriman`
--
ALTER TABLE `jejak_notapengiriman`
  ADD CONSTRAINT `fk_nota_pengiriman_has_users_nota_pengiriman1` FOREIGN KEY (`nota_pengiriman_idnota_pengiriman`) REFERENCES `nota_pengiriman` (`idnota_pengiriman`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_nota_pengiriman_has_users_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_pemesanan`
--
ALTER TABLE `jejak_pemesanan`
  ADD CONSTRAINT `fk_pemesanan_has_user_pemesanan1` FOREIGN KEY (`pemesanan_idpemesanan`) REFERENCES `pemesanan` (`idpemesanan`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pemesanan_has_user_user1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `jejak_transport`
--
ALTER TABLE `jejak_transport`
  ADD CONSTRAINT `fk_transport_has_users_transport1` FOREIGN KEY (`transport_idtransport`) REFERENCES `transport` (`idtransport`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_transport_has_users_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `nota_pembelian`
--
ALTER TABLE `nota_pembelian`
  ADD CONSTRAINT `fk_Nota_pembelian_distributor1` FOREIGN KEY (`distributor_iddistributor`) REFERENCES `distributor` (`iddistributor`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `nota_pengiriman`
--
ALTER TABLE `nota_pengiriman`
  ADD CONSTRAINT `fk_nota_pengiriman_transport1` FOREIGN KEY (`transport_idtransport`) REFERENCES `transport` (`idtransport`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `fk_pemesanan_distributor1` FOREIGN KEY (`distributor_iddistributor`) REFERENCES `distributor` (`iddistributor`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `satuan_simpan`
--
ALTER TABLE `satuan_simpan`
  ADD CONSTRAINT `fk_satuan_has_barang_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_satuan_has_barang_satuan1` FOREIGN KEY (`satuan_idsatuan`) REFERENCES `satuan` (`idsatuan`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
