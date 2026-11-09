-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2026 at 10:06 AM
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
-- Database: `dna_vacation`
--

-- --------------------------------------------------------

--
-- Table structure for table `attractions`
--

CREATE TABLE `attractions` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Tips Travel', 'tips-travel', '2026-08-19 02:29:51'),
(2, 'Destinasi', 'destinasi', '2026-08-19 02:29:51'),
(3, 'Kuliner', 'kuliner', '2026-08-19 02:29:51'),
(4, 'Rental Mobil', 'rental-mobil', '2026-08-19 02:29:51'),
(5, 'Promo', 'promo', '2026-08-19 02:29:51'),
(6, 'Budaya', 'budaya', '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `category_id`, `excerpt`, `content`, `image`, `author_id`, `is_published`, `meta_title`, `meta_description`, `view_count`, `published_at`, `created_at`, `updated_at`) VALUES
(1, '7 Alasan Wajib Kunjungi Toraja Sekali Seumur Hidup', '7-alasan-wajib-kunjungi-toraja', 2, 'Toraja bukan sekedar destinasi wisata biasa. Simak 7 alasan kenapa Toraja wajib masuk bucket list Anda.', '<h2>1. Budaya Unik yang Tidak Ada di Tempat Lain</h2><p>Ritual Rambu Solo (upacara pemakaman) adalah tradisi yang unik di dunia. Anda akan menyaksikan bagaimana masyarakat Toraja menghormati orang yang telah tiada dengan cara yang sangat spesial dan penuh makna.</p><h2>2. Rumah Adat Tongkonan yang Megah</h2><p>Arsitektur rumah adat Tongkonan dengan atap melengkung seperti perahu adalah salah satu warisan budaya paling ikonik di Indonesia. Setiap ukiran memiliki makna filosofis yang mendalam.</p><h2>3. Pemandangan Alam yang Memukau</h2><p>Dari Batutumonga Anda bisa melihat hamparan sawah terasering dan pegunungan yang menakjubkan. Toraja dijuluki \"negeri di atas awan\" karena pemandangannya yang spektakuler.</p><h2>4. Kuliner Khas yang Menggugah Selera</h2><p>Papiong (daging masak dalam bambu), Pantollo Pamarrasan (ikan mas kuah hitam), dan kopi Toraja yang mendunia. Kuliner Toraja adalah pengalaman kuliner yang tidak boleh dilewatkan.</p><h2>5. Kete Kesu - Desa Adat Terpelihara</h2><p>Kompleks rumah tongkonan tertua yang masih terjaga keasliannya. Di sini Anda bisa melihat proses kehidupan tradisional Toraja yang belum banyak berubah selama berabad-abad.</p><h2>6. Kopi Toraja Kelas Dunia</h2><p>Kopi Toraja termasuk salah satu kopi terbaik di dunia. Nikmati langsung di kebunnya dan pelajari proses dari petik hingga siap seduh.</p><h2>7. Aksesibilitas yang Semakin Mudah</h2><p>Dengan pembukaan Bandara Toraja (Buntu Kunik), kunjungan ke Toraja jadi lebih mudah dan cepat.</p><h3>Siap Menjelajahi Toraja?</h3><p>DNA Vacation menyediakan paket Toraja 3D2N dengan itinerary lengkap. Hubungi kami untuk info & booking!</p>', NULL, 1, 1, NULL, NULL, 0, '2026-08-19 02:29:51', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(2, 'Wakatobi vs Bunaken: Mana yang Lebih Cocok untuk Anda?', 'wakatobi-vs-bunaken', 2, 'Bingung memilih antara Wakatobi dan Bunaken untuk liburan snorkeling? Simak perbandingan lengkapnya di sini.', '<h2>Wakatobi: Surga Tersembunyi</h2><p>Wakatobi merupakan singkatan dari 4 pulau utama: Wangi-Wangi, Kaledupa, Tomia, dan Binongko. Terkenal sebagai salah satu spot diving terbaik di dunia dengan biodiversitas luar biasa.</p><p><strong>Kelebihan Wakatobi:</strong></p><ul><li>Kondisi karang masih sangat perawan</li><li>Kepadatan wisatawan lebih sedikit</li><li>Pengalaman lebih otentik & eksklusif</li><li>Cocok untuk diver berpengalaman</li></ul><p><strong>Kekurangan:</strong></p><ul><li>Akses lebih sulit (transit di Baubau)</li><li>Fasilitas terbatas dibanding Bunaken</li><li>Biaya cenderung lebih tinggi</li></ul><h2>Bunaken: Klasik yang Selalu Menawan</h2><p>Bunaken adalah taman laut nasional pertama di Indonesia. Terkenal dengan dinding karang tegak lurus (vertical wall) hingga 1500m dan populasi ikan yang melimpah.</p><p><strong>Kelebihan Bunaken:</strong></p><ul><li>Akses mudah dari Manado</li><li>Cocok untuk keluarga & pemula</li><li>Banyak pilihan akomodasi</li><li>Kuliner Manado kelas dunia</li></ul><p><strong>Kekurangan:</strong></p><ul><li>Beberapa spot sudah cukup ramai</li><li>Kerusakan karang di area populer</li></ul><h2>Kesimpulan</h2><p>Pilih <strong>Wakatobi</strong> jika Anda: diver berpengalaman, cari pengalaman eksklusif, punya budget cukup, tidak keberatan dengan akses yang sedikit challenging.</p><p>Pilih <strong>Bunaken</strong> jika Anda: keluarga dengan anak-anak, pemula snorkeling, cari kombinasi wisata laut + kuliner + kota, ingin akses mudah.</p><p>Kedua destinasi punya keunikan masing-masing. Bahkan banyak wisatawan yang mengkombinasikan keduanya dalam satu trip!</p>', NULL, 1, 1, NULL, NULL, 0, '2026-08-19 02:29:51', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(3, 'Tips Hemat Sewa Mobil untuk Liburan Keluarga', 'tips-hemat-sewa-mobil', 4, 'Panduan lengkap sewa mobil hemat dan nyaman untuk liburan keluarga. Simak tips-tipsnya di sini!', '<h2>1. Booking Jauh-Jauh Hari</h2><p>Booking H-14 atau lebih biasanya mendapatkan harga terbaik. Terutama untuk high season seperti liburan sekolah dan lebaran.</p><h2>2. Pilih Mobil Sesuai Kebutuhan</h2><p>Tidak semua liburan membutuhkan Alphard. Untuk keluarga 4-5 orang, Avanza atau Ertiga sudah sangat cukup dan lebih hemat BBM.</p><h2>3. Perhitungkan Total Kilometer</h2><p>Jika perjalanan jauh, mobil dengan konsumsi BBM irit seperti Innova diesel akan lebih hemat dibanding SUV bensin.</p><h2>4. Cek Include & Exclude</h2><p>Pastikan Anda tahu apa saja yang termasuk: BBM, tol, parkir, sopir, uang makan sopir? Pertanyaan detail ini menghindari surprise di akhir sewa.</p><h2>5. Foto Kondisi Mobil di Awal</h2><p>Selalu dokumentasikan kondisi mobil sebelum berangkat. Ini melindungi Anda dari klaim kerusakan yang tidak Anda lakukan.</p><h2>6. Pahami Kebijakan Kelebihan Jam</h2><p>1 hari sewa umumnya 12 jam. Setelah itu ada charge tambahan. Untuk trip 2-3 hari, sewa dengan hitungan hari lebih menguntungkan.</p><h2>7. Sopir vs Self Drive</h2><p>Sopir bertambah biaya, tapi Anda bisa istirahat dan menikmati pemandangan. Untuk perjalanan panjang (>4 jam), dengan sopir lebih direkomendasikan.</p><h3>Butuh Rental Mobil?</h3><p>DNA Vacation menyediakan berbagai jenis mobil dengan harga transparan. Hubungi kami untuk konsultasi gratis!</p>', NULL, 1, 1, NULL, NULL, 0, '2026-08-19 02:29:51', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(4, 'Panduan Sunrise di Bromo: Tips dari yang Sudah Coba', 'panduan-sunrise-bromo', 1, 'Ingin melihat sunrise legendaris di Bromo? Simak panduan lengkapnya agar tidak zonk saat berangkat!', '<h2>Kapan Waktu Terbaik?</h2><p>Musim kemarau (April-Oktober) adalah waktu terbaik karena minim awan dan hujan. Sunrise time berkisar 05:15-05:45 WIB.</p><h2>Berangkat Jam Berapa dari Cemoro Lawang?</h2><p>Untuk sunrise 05:30, minimal berangkat 03:30 dini hari agar tiba di Penanjakan 04:15-04:30. Ini kasih waktu untuk cari spot terbaik.</p><h2>Perlengkapan Wajib</h2><ul><li>Jaket tebal (suhu bisa 5°C)</li><li>Sarung tangan & kupluk</li><li>Sepatu tertutup</li><li>Masker (debu vulkanik)</li><li>Kamera + tripod</li><li>Powerbank</li></ul><h2>Pilihan Spot Sunrise</h2><p><strong>Penanjakan 1:</strong> View klasik, paling ramai, akses jeep.<br><strong>Penanjakan 2:</strong> Lebih tinggi, view lebih dramatis, less crowded.<br><strong>Bukit Kingkong:</strong> Alternatif ketika Penanjakan penuh.<br><strong>Bukit Cinta:</strong> Baru dan sunyi, view berbeda.</p><h2>Setelah Sunrise</h2><p>Setelah sunrise, langsung ke kawah Bromo. Trekking dari parkir jeep sekitar 30 menit, atau naik kuda (Rp 100-150rb sekali jalan). Setelah kawah, mampir ke Pasir Berbisik dan Bukit Teletubbies (Savana).</p><h2>Kesalahan yang Sering Terjadi</h2><ul><li>Kepagian → nunggu lama & kedinginan</li><li>Kesiangan → sunrise sudah lewat</li><li>Baju kurang tebal → kedinginan</li><li>Tidak sarapan → lemas saat trekking</li></ul><h3>Ingin All-In-Package?</h3><p>DNA Vacation punya paket Bromo Sunrise 2D1N dengan semua handled by us. Cek katalog kami!</p>', NULL, 1, 1, NULL, NULL, 0, '2026-08-19 02:29:51', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(5, 'Kuliner Wajib Coba saat Liburan ke Sulawesi', 'kuliner-wajib-sulawesi', 3, 'Liburan ke Sulawesi belum lengkap tanpa mencicipi kuliner khasnya. Ini list wajib coba dari lokal!', '<h2>Sulawesi Selatan - Makassar</h2><p><strong>1. Coto Makassar</strong> - Sup daging dengan kuah kacang khas. Best di Coto Nusantara atau Coto Paraikatte.</p><p><strong>2. Sop Konro</strong> - Iga sapi dengan kuah rempah. Coba Konro Karebosi.</p><p><strong>3. Pisang Epe</strong> - Dessert manis dengan gula merah cair. Pinggir Pantai Losari.</p><p><strong>4. Pallubasa</strong> - Sup daging serupa coto tapi ada tambahan telur mentah.</p><h2>Sulawesi Selatan - Toraja</h2><p><strong>1. Papiong</strong> - Daging babi/ayam dimasak dalam bambu.</p><p><strong>2. Pantollo Pamarrasan</strong> - Ikan mas kuah hitam khas Toraja.</p><p><strong>3. Kopi Toraja</strong> - Wajib coba langsung di kebunnya!</p><h2>Sulawesi Tenggara - Kendari</h2><p><strong>1. Sinonggi</strong> - Sagu kenyal dengan kuah ikan.</p><p><strong>2. Kabuto</strong> - Singkong fermentasi.</p><h2>Sulawesi Utara - Manado</h2><p><strong>1. Tinutuan (Bubur Manado)</strong> - Bubur sayur khas.</p><p><strong>2. Cakalang Fufu</strong> - Ikan cakalang asap.</p><p><strong>3. Nasi Kuning Manado</strong> - Dengan cakalang & sambal roa.</p><h2>Sulawesi Tengah - Palu</h2><p><strong>1. Kaledo</strong> - Sup kaki sapi khas Palu.</p><p><strong>2. Uta Dada</strong> - Ayam santan kelapa.</p><p>Selamat berpetualang kuliner!</p>', NULL, 1, 1, NULL, NULL, 0, '2026-08-19 02:29:51', '2026-08-19 02:29:51', '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `type` enum('mpv','suv','sedan','bus','minibus','pickup','city') DEFAULT 'mpv',
  `capacity` int(11) DEFAULT 4,
  `luggage` int(11) DEFAULT 2,
  `transmission` enum('manual','automatic') DEFAULT 'automatic',
  `fuel_type` enum('bensin','solar','listrik','hybrid') DEFAULT 'bensin',
  `year` int(11) DEFAULT 2022,
  `price_with_driver` decimal(12,0) DEFAULT 0,
  `price_without_driver` decimal(12,0) DEFAULT 0,
  `has_driver_option` tinyint(1) DEFAULT 1,
  `has_self_drive` tinyint(1) DEFAULT 0,
  `facilities` text DEFAULT NULL COMMENT 'JSON array',
  `terms` text DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text DEFAULT NULL COMMENT 'JSON array',
  `is_available` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `slug`, `brand`, `type`, `capacity`, `luggage`, `transmission`, `fuel_type`, `year`, `price_with_driver`, `price_without_driver`, `has_driver_option`, `has_self_drive`, `facilities`, `terms`, `service_area`, `image`, `gallery`, `is_available`, `is_featured`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Toyota Avanza New', 'toyota-avanza-new', 'Toyota', 'mpv', 7, 2, 'automatic', 'bensin', 2023, 550000, 350000, 1, 1, '[\"AC\",\"Audio\",\"Charger USB\",\"Bagasi luas\",\"Kursi nyaman\"]', NULL, 'Makassar & Sekitarnya', NULL, NULL, 1, 1, 'MPV andalan untuk perjalanan keluarga. Nyaman, irit BBM, dan bagasi luas.', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(2, 'Toyota Innova Reborn', 'toyota-innova-reborn', 'Toyota', 'mpv', 7, 3, 'automatic', 'solar', 2023, 800000, 600000, 1, 1, '[\"AC double blower\",\"Audio touchscreen\",\"Charger USB\",\"Jok kulit\",\"Cruise control\",\"Bagasi luas\"]', NULL, 'Sulawesi Selatan', NULL, NULL, 1, 1, 'MPV premium dengan kenyamanan ekstra. Cocok untuk perjalanan jarak jauh Toraja/Bulukumba.', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(3, 'Mitsubishi Pajero Sport', 'mitsubishi-pajero-sport', 'Mitsubishi', 'suv', 7, 3, 'automatic', 'solar', 2022, 1100000, 0, 1, 0, '[\"AC\",\"4WD\",\"Audio premium\",\"Cruise control\",\"Sunroof\",\"Rear entertainment\"]', NULL, 'All Sulawesi + Adventure', NULL, NULL, 1, 1, 'SUV tangguh untuk segala medan. Ideal untuk adventure trip ke Rammang-Rammang, Bantimurung.', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(4, 'Toyota HiAce Commuter', 'toyota-hiace-commuter', 'Toyota', 'minibus', 15, 5, 'manual', 'solar', 2022, 1400000, 0, 1, 0, '[\"AC\",\"Reclining seat\",\"Audio\",\"Bagasi besar\",\"Mikrofon\",\"TV monitor\"]', NULL, 'Sulawesi Selatan & Barat', NULL, NULL, 1, 0, 'Minibus nyaman untuk rombongan besar. Cocok untuk group trip, corporate outing, family gathering.', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(5, 'Honda Brio', 'honda-brio', 'Honda', 'city', 5, 1, 'automatic', 'bensin', 2023, 400000, 300000, 1, 1, '[\"AC\",\"Audio\",\"Charger USB\",\"Irit BBM\",\"Lincah di kota\"]', NULL, 'Dalam Kota Makassar', NULL, NULL, 1, 0, 'City car ekonomis dan lincah. Cocok untuk solo traveler atau couple wisata dalam kota.', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(6, 'Toyota Alphard', 'toyota-alphard', 'Toyota', 'mpv', 7, 3, 'automatic', 'bensin', 2022, 2500000, 0, 1, 0, '[\"AC dual zone\",\"Kursi captain\",\"Audio JBL\",\"Sunroof\",\"Kulkas mini\",\"Sopir formal\"]', NULL, 'Sulawesi Selatan', NULL, NULL, 1, 1, 'Executive MPV untuk VVIP, wedding car, atau corporate high-level meeting.', '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(7, 'Suzuki Ertiga', 'suzuki-ertiga', 'Suzuki', 'mpv', 7, 2, 'automatic', 'bensin', 2023, 500000, 350000, 1, 1, '[\"AC\",\"Audio\",\"USB port\",\"Bagasi cukup\"]', NULL, 'Makassar & Sekitarnya', NULL, NULL, 1, 0, 'MPV ekonomis dengan kabin luas. Alternatif Avanza dengan feel yang lebih modern.', '2026-08-19 02:29:51', '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `car_bookings`
--

CREATE TABLE `car_bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `car_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_whatsapp` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pickup_time` time DEFAULT '08:00:00',
  `pickup_location` varchar(255) DEFAULT NULL,
  `return_location` varchar(255) DEFAULT NULL,
  `with_driver` tinyint(1) DEFAULT 1,
  `total_price` decimal(12,0) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `status` enum('baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_trip_requests`
--

CREATE TABLE `custom_trip_requests` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_whatsapp` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `destination` varchar(200) DEFAULT NULL,
  `trip_type` varchar(50) DEFAULT NULL,
  `num_persons` int(11) DEFAULT 1,
  `budget_range` varchar(100) DEFAULT NULL,
  `preferred_dates` text DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('baru','diproses','penawaran_terkirim','terkonfirmasi','selesai') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `name`, `slug`, `description`, `image`, `is_popular`, `created_at`) VALUES
(1, 'Toraja', 'toraja', 'Negeri di atas awan dengan budaya unik dan pemandangan menakjubkan.', NULL, 1, '2026-08-19 02:29:51'),
(2, 'Wakatobi', 'wakatobi', 'Surga bawah laut kelas dunia di Sulawesi Tenggara.', NULL, 1, '2026-08-19 02:29:51'),
(3, 'Bunaken', 'bunaken', 'Taman laut ikonik Sulawesi Utara dengan biodiversitas luar biasa.', NULL, 1, '2026-08-19 02:29:51'),
(4, 'Labuan Bajo', 'labuan-bajo', 'Gerbang menuju Komodo dan pulau-pulau eksotis Flores.', NULL, 1, '2026-08-19 02:29:51'),
(5, 'Raja Ampat', 'raja-ampat', 'Surga terakhir di Bumi dengan keanekaragaman hayati laut tertinggi.', NULL, 1, '2026-08-19 02:29:51'),
(6, 'Bali', 'bali', 'Pulau Dewata dengan budaya, pantai, dan spa kelas dunia.', NULL, 1, '2026-08-19 02:29:51'),
(7, 'Lombok', 'lombok', 'Pesona Gili Trawangan, Gunung Rinjani, dan pantai pink.', NULL, 1, '2026-08-19 02:29:51'),
(8, 'Bromo', 'bromo', 'Sunrise legendaris di Gunung Bromo, Jawa Timur.', NULL, 1, '2026-08-19 02:29:51'),
(9, 'Yogyakarta', 'yogyakarta', 'Kota budaya, Borobudur, Prambanan, dan Malioboro.', NULL, 0, '2026-08-19 02:29:51'),
(10, 'Derawan', 'derawan', 'Pulau-pulau tropis dengan penyu hijau dan ubur-ubur tanpa sengat.', NULL, 0, '2026-08-19 02:29:51'),
(11, 'Belitung', 'belitung', 'Pantai pasir putih dengan formasi batu granit ikonik.', NULL, 0, '2026-08-19 02:29:51'),
(12, 'Banda Neira', 'banda-neira', 'Kepulauan rempah bersejarah dengan snorkeling luar biasa.', NULL, 0, '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `star_rating` tinyint(4) DEFAULT 3,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_requests`
--

CREATE TABLE `hotel_requests` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_whatsapp` varchar(20) NOT NULL,
  `destination` varchar(200) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `num_guests` int(11) DEFAULT 1,
  `num_rooms` int(11) DEFAULT 1,
  `budget` varchar(100) DEFAULT NULL,
  `hotel_type` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('baru','diproses','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'DNA Vacation', '2026-08-19 02:29:51'),
(2, 'site_tagline', 'Discover Indonesia', '2026-08-20 06:34:19'),
(3, 'whatsapp_number', '6285757253329', '2026-08-19 02:29:51'),
(4, 'instagram_url', 'https://www.instagram.com/dnavacation/', '2026-08-19 02:29:51'),
(5, 'email', 'info@dnavacation.com', '2026-08-19 02:29:51'),
(6, 'address', 'Surabaya, Jawa Timur, Indonesia', '2026-08-19 02:29:51'),
(7, 'phone', '+628575725332', '2026-08-19 02:29:51'),
(8, 'about_text', 'DNA Vacation adalah travel agent kepercayaan Anda untuk menjelajahi keindahan Indonesia. Kami menyediakan paket tour, rental mobil, dan booking hotel dengan pelayanan profesional dan harga terbaik. Berpusat di Sulawesi, kami mengkhususkan diri dalam destinasi Timur Indonesia yang eksotis: Toraja, Wakatobi, Bunaken, Labuan Bajo, hingga Raja Ampat.', '2026-08-19 02:29:51'),
(9, 'vision', 'Menjadi travel agent terdepan di Indonesia Timur yang menghadirkan pengalaman liburan tak terlupakan dengan pelayanan berkualitas tinggi.', '2026-08-19 02:29:51'),
(10, 'mission', 'Menyediakan paket wisata berkualitas dengan harga kompetitif, memberikan pelayanan personal 24/7 via WhatsApp, membangun kepercayaan melalui transparansi, dan mempromosikan pariwisata Indonesia Timur ke dunia.', '2026-08-19 02:29:51'),
(11, 'maps_embed', '', '2026-08-19 02:29:51'),
(12, 'hero_headline', 'Jelajahi Keindahan Indonesia Timur bersama DNA Vacation', '2026-08-19 02:29:51'),
(13, 'hero_subheadline', 'Jelajahi destinasi terbaik Indonesia dengan layanan perjalanan yang lengkap dan terpercaya.', '2026-08-20 06:39:05'),
(14, 'hero_image', '', '2026-08-19 02:29:51'),
(15, 'footer_text', 'Wujudkan liburan impian Anda bersama DNA Vacation.', '2026-08-20 06:41:17'),
(16, 'meta_title', 'DNA Vacation - Tour & Travel Terbaik Indonesia Timur', '2026-08-19 02:29:51'),
(17, 'meta_description', 'DNA Vacation menyediakan paket tour Toraja, Wakatobi, Bunaken, Labuan Bajo, Raja Ampat + rental mobil & bantuan booking hotel. Konsultasi gratis!', '2026-08-19 02:29:51'),
(18, 'brand_tagline', 'It\'s More Than Fun', '2026-08-20 06:30:45'),
(19, 'footer_tagline', 'It\'s More Than Fun', '2026-08-20 06:30:45'),
(20, 'facebook_url', '', '2026-08-20 06:30:45'),
(21, 'tiktok_url', '', '2026-08-20 06:30:45'),
(22, 'operational_hours', 'Setiap hari, 08:00 - 22:00 WITA', '2026-08-20 06:30:45'),
(23, 'hero_badge', '', '2026-08-20 06:30:45'),
(24, 'hero_headline_before', 'Discover', '2026-08-20 06:36:51'),
(25, 'hero_headline_script', 'Indonesia', '2026-08-20 06:36:37'),
(26, 'hero_headline_after', '', '2026-08-20 06:30:45'),
(27, 'hero_cta_primary', 'Explore Now', '2026-08-20 06:30:45'),
(28, 'hero_cta_secondary', 'Konsultasi Tour', '2026-08-20 06:39:05'),
(29, 'section_destinations_title', 'Popular Destinations', '2026-08-20 06:30:45'),
(30, 'section_destinations_subtitle', 'Destinasi favorit yang paling dicari traveler', '2026-08-20 06:30:45'),
(31, 'section_tours_title', 'Best Tour Packages', '2026-08-20 06:30:45'),
(32, 'section_tours_subtitle', 'Paket tour paling populer pilihan traveler', '2026-08-20 06:30:45'),
(33, 'section_cars_title', 'Featured Rental Cars', '2026-08-20 06:30:45'),
(34, 'section_cars_subtitle', 'Mobil nyaman dengan/tanpa sopir', '2026-08-20 06:30:45'),
(35, 'section_testimonials_title', 'What Our Travelers Say', '2026-08-20 06:30:45'),
(36, 'section_testimonials_subtitle', 'Cerita nyata dari pelanggan', '2026-08-20 06:30:45'),
(37, 'section_blog_title', 'Travel Stories & Tips', '2026-08-20 06:30:45'),
(38, 'section_blog_subtitle', 'Inspirasi & tips untuk perjalanan terbaik', '2026-08-20 06:30:45'),
(39, 'cta_headline', 'Your Next Adventure Awaits!', '2026-08-20 06:30:45'),
(40, 'cta_subheadline', 'Hubungi kami sekarang dan biarkan tim DNA Vacation mewujudkan liburan impian Anda!', '2026-08-20 06:30:45'),
(41, 'copyright_text', 'Made with ♥ for Indonesian travelers', '2026-08-20 06:30:45'),
(42, 'show_trust_strip', '1', '2026-08-20 06:30:45'),
(43, 'show_destinations', '1', '2026-08-20 06:30:45'),
(44, 'show_featured_cars', '1', '2026-08-20 06:30:45'),
(45, 'show_testimonials_home', '1', '2026-08-20 06:30:45'),
(46, 'show_blog_home', '1', '2026-08-20 06:30:45'),
(47, 'show_cta', '1', '2026-08-20 06:30:45'),
(48, 'footer_about_text', 'DNA Vacation adalah travel agent kepercayaan Anda untuk menjelajahi keindahan Indonesia. Kami menyediakan paket tour, rental mobil, dan booking hotel dengan pelayanan profesional dan harga terbaik.', '2026-08-20 06:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_location` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `tour_id` int(11) DEFAULT NULL,
  `tour_name` varchar(200) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0 COMMENT '0=pending review, 1=approved & shown',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `customer_name`, `customer_location`, `content`, `rating`, `tour_id`, `tour_name`, `image`, `is_active`, `created_at`) VALUES
(1, 'Rina Susanti', 'Jakarta', 'Trip ke Toraja bareng DNA Vacation luar biasa! Guide-nya sangat expert soal budaya lokal, hotelnya nyaman, dan makanan enak semua. Papiong-nya endulita! Pasti trip lagi bareng DNA.', 5, 1, 'Toraja Cultural Heritage 3D2N', NULL, 1, '2026-08-19 02:29:51'),
(2, 'Budi Hartono', 'Surabaya', 'Wakatobi memang surga bawah laut. Terumbu karangnya masih perawan dan ikan warna-warni banyak sekali. Crew DNA Vacation sangat helpful, alat snorkeling bagus. Recommended banget!', 5, 2, 'Wakatobi Underwater Paradise', NULL, 1, '2026-08-19 02:29:51'),
(3, 'Dewi Anggraini', 'Bandung', 'Sewa Innova di DNA Vacation prosesnya mudah dan cepat. Mobil bersih, sopirnya (Pak Ridwan) ramah dan tahu banyak tempat wisata hidden gem. Highly recommended!', 5, NULL, 'Rental Innova', NULL, 1, '2026-08-19 02:29:51'),
(4, 'Ahmad Rizki', 'Makassar', 'Family trip ke Bali via DNA Vacation sangat menyenangkan. Anak-anak happy, orang tua puas. Nusa Penida island hopping-nya jadi highlight liburan. Terima kasih tim DNA!', 5, 6, 'Bali Cultural & Beach', NULL, 1, '2026-08-19 02:29:51'),
(5, 'Siti Nurhaliza', 'Yogyakarta', 'Labuan Bajo trip is a bucket list checked! Sailing di phinisi sunset, ketemu komodo, snorkeling Pink Beach. Semua terkoordinasi dengan rapi. Fotografer drone-nya juga cakep hasilnya!', 5, 4, 'Labuan Bajo Sailing Komodo', NULL, 1, '2026-08-19 02:29:51'),
(6, 'Michael Tanudibrata', 'Jakarta', 'Trip Raja Ampat 5D4N adalah pengalaman diving terbaik saya. Cape Kri memang legend! DNA Vacation handle semua logistik dengan sangat baik. Worth every rupiah!', 5, 5, 'Raja Ampat Ultimate Diving', NULL, 1, '2026-08-19 02:29:51'),
(7, 'Fitri Handayani', 'Semarang', 'Bunaken trip bersama keluarga besar (12 orang) super smooth. Speedboat aman, snorkeling di 3 spot terbaik, dan makanan Manado juara. Recommended untuk family trip!', 4, 3, 'Bunaken Snorkeling Explorer', NULL, 1, '2026-08-19 02:29:51'),
(8, 'Arif Wibowo', 'Surabaya', 'Bromo sunrise trip 2D1N pas budget dan waktunya. Jeep tepat waktu, guide informatif, sunrise memang worth waking up 03:00 AM. Terima kasih DNA Vacation!', 5, 8, 'Bromo Sunrise Adventure', NULL, 1, '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `itinerary` text DEFAULT NULL,
  `includes` text DEFAULT NULL,
  `excludes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `meeting_point` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `duration_days` int(11) DEFAULT 1,
  `price` decimal(12,0) NOT NULL DEFAULT 0,
  `price_label` varchar(50) DEFAULT 'per orang',
  `min_persons` int(11) DEFAULT 1,
  `max_persons` int(11) DEFAULT 20,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text DEFAULT NULL COMMENT 'JSON array of image paths',
  `is_active` tinyint(1) DEFAULT 1,
  `is_best_seller` tinyint(1) DEFAULT 0,
  `is_promo` tinyint(1) DEFAULT 0,
  `promo_price` decimal(12,0) DEFAULT NULL,
  `departure_dates` text DEFAULT NULL COMMENT 'JSON array of dates',
  `sort_order` int(11) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `slug`, `destination_id`, `category_id`, `description`, `itinerary`, `includes`, `excludes`, `terms`, `meeting_point`, `duration`, `duration_days`, `price`, `price_label`, `min_persons`, `max_persons`, `image`, `gallery`, `is_active`, `is_best_seller`, `is_promo`, `promo_price`, `departure_dates`, `sort_order`, `view_count`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Toraja Cultural Heritage 3D2N', 'toraja-cultural-heritage-3d2n', 1, 1, 'Rasakan pengalaman spiritual dan budaya di Tana Toraja. Kunjungi Kete Kesu, Londa, Lemo, dan nikmati panorama pegunungan yang menakjubkan.', '[{\"day\":1,\"title\":\"Makassar - Toraja\",\"activities\":[\"Penjemputan pagi di Makassar\",\"Perjalanan ke Toraja via Pare-Pare\",\"Makan siang di Restoran Padi (Pare-Pare)\",\"Tiba di Toraja sore hari\",\"Check-in hotel & welcome dinner\"]},{\"day\":2,\"title\":\"Explore Toraja\",\"activities\":[\"Kunjungi Kete Kesu (rumah adat & tebing kubur)\",\"Londa (goa pemakaman & tau-tau)\",\"Lemo (kubur tebing)\",\"Makan siang khas Toraja (papiong)\",\"Batutumonga - view pegunungan\",\"Kembali ke hotel\"]},{\"day\":3,\"title\":\"Toraja - Makassar\",\"activities\":[\"Pasar Bolu (kerbau)\",\"Ollon village (opsional)\",\"Makan siang\",\"Perjalanan kembali ke Makassar\",\"Tiba malam hari\"]}]', '[\"Transportasi AC PP\",\"Hotel 2 malam (standar 3*)\",\"Makan sesuai program (2x sarapan, 3x makan siang, 2x makan malam)\",\"Guide lokal berpengalaman\",\"Tiket masuk semua wisata\",\"Air mineral\",\"Dokumentasi drone\"]', '[\"Tiket pesawat\",\"Pengeluaran pribadi\",\"Tips guide/driver\",\"Asuransi perjalanan\"]', 'Minimal 2 orang. DP 50% untuk konfirmasi booking, pelunasan H-7 sebelum keberangkatan. Cancellation policy: refund 70% jika batal H-14, tidak ada refund H-7.', 'Bandara Sultan Hasanuddin Makassar', '3 Hari 2 Malam', 3, 2850000, 'per orang', 1, 20, '', NULL, 1, 1, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(2, 'Wakatobi Underwater Paradise 4D3N', 'wakatobi-underwater-paradise-4d3n', 2, 6, 'Jelajahi surga bawah laut Wakatobi - salah satu spot diving terbaik dunia. Snorkeling di terumbu karang yang masih perawan, jumpa ikan-ikan warna-warni dan mungkin lumba-lumba!', '[{\"day\":1,\"title\":\"Arrival Wakatobi\",\"activities\":[\"Penjemputan Bandara Matahora\",\"Transfer ke penginapan di Wangi-Wangi\",\"Welcome briefing\",\"Snorkeling perkenalan di Waha\",\"Sunset dinner\"]},{\"day\":2,\"title\":\"Island Hopping Kaledupa\",\"activities\":[\"Speedboat ke Pulau Hoga\",\"Snorkeling di Coral Garden\",\"Makan siang di pulau\",\"Snorkeling di Sombu Dive Point\",\"Kembali ke Wangi-Wangi\"]},{\"day\":3,\"title\":\"Tomia Adventure\",\"activities\":[\"Perjalanan ke Tomia\",\"Snorkeling Roma Point (spot manta)\",\"Puncak Kahyangan sunset\",\"Menginap di Tomia\"]},{\"day\":4,\"title\":\"Return\",\"activities\":[\"Breakfast\",\"Snorkeling terakhir\",\"Transfer ke bandara\",\"Departure\"]}]', '[\"Penginapan 3 malam\",\"Speedboat & transportasi\",\"Makan 3x sehari\",\"Guide profesional\",\"Alat snorkeling lengkap\",\"Tiket masuk & fee wisata\",\"Air mineral\",\"Dokumentasi underwater\"]', '[\"Tiket pesawat ke Wakatobi\",\"Alat diving (opsional sewa)\",\"Tips guide\",\"Pengeluaran pribadi\",\"Asuransi\"]', 'Minimal 4 orang untuk private trip. Open trip jadwal fixed. Booking H-14 sebelum keberangkatan.', 'Bandara Matahora, Wangi-Wangi', '4 Hari 3 Malam', 4, 4800000, 'per orang', 1, 20, '', NULL, 1, 1, 1, 4200000, NULL, 0, 0, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(3, 'Bunaken Snorkeling Explorer 3D2N', 'bunaken-snorkeling-explorer-3d2n', 3, 3, 'Nikmati pesona Taman Nasional Bunaken. Snorkeling di dinding karang tegak lurus, jumpa penyu, dan lumba-lumba di Selat Lembeh.', '[{\"day\":1,\"title\":\"Manado Welcome\",\"activities\":[\"Penjemputan Bandara Sam Ratulangi\",\"City tour Manado\",\"Kuliner khas: tinutuan/bubur manado\",\"Transfer ke hotel\",\"Malam bebas\"]},{\"day\":2,\"title\":\"Bunaken Marine Park\",\"activities\":[\"Speedboat ke Bunaken\",\"Snorkeling 3 spot terbaik\",\"Makan siang seafood BBQ\",\"Pulau Siladen\",\"Kembali ke Manado sore\"]},{\"day\":3,\"title\":\"Tomohon & Departure\",\"activities\":[\"Pasar ekstrem Tomohon\",\"Bukit Kasih\",\"Danau Linow (danau warna-warni)\",\"Transfer ke bandara\"]}]', '[\"Hotel 2 malam\",\"Transportasi AC\",\"Speedboat ke Bunaken\",\"Makan sesuai program\",\"Guide lokal\",\"Alat snorkeling\",\"Tiket wisata\"]', '[\"Tiket pesawat\",\"Diving (opsional)\",\"Pengeluaran pribadi\",\"Tips\"]', 'DP 40% untuk booking. Cocok untuk keluarga & pemula snorkeling.', 'Bandara Sam Ratulangi Manado', '3 Hari 2 Malam', 3, 3200000, 'per orang', 1, 20, '', NULL, 1, 1, 0, NULL, NULL, 0, 1, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 07:32:49'),
(4, 'Labuan Bajo Sailing Komodo 3D2N', 'labuan-bajo-sailing-komodo-3d2n', 4, 1, 'Petualangan sailing dengan phinisi ke Taman Nasional Komodo. Temui komodo, snorkeling di Pink Beach, dan sunset di Pulau Padar.', '[{\"day\":1,\"title\":\"Welcome to Flores\",\"activities\":[\"Penjemputan Bandara Komodo\",\"Transfer ke pelabuhan\",\"Sailing ke Pulau Kelor\",\"Snorkeling & sunset di kapal\",\"Menginap di kapal phinisi\"]},{\"day\":2,\"title\":\"Komodo National Park\",\"activities\":[\"Trekking Pulau Padar sunrise\",\"Pink Beach snorkeling\",\"Trekking Pulau Komodo (jumpa komodo)\",\"Manta Point snorkeling\",\"Pulau Kanawa sunset\"]},{\"day\":3,\"title\":\"Final Day\",\"activities\":[\"Sunrise di kapal\",\"Gili Laba trekking\",\"Return ke Labuan Bajo\",\"Transfer ke bandara\"]}]', '[\"Kapal phinisi AC (2 malam)\",\"Makan 3x sehari\",\"Guide berlisensi\",\"Alat snorkeling\",\"Tiket TN Komodo\",\"Ranger fee\",\"Dokumentasi drone & underwater\"]', '[\"Tiket pesawat\",\"Tips crew\",\"Pengeluaran pribadi\",\"Minuman alkohol\"]', 'DP 50%. Weather-dependent - jadwal bisa berubah karena cuaca laut.', 'Bandara Komodo, Labuan Bajo', '3 Hari 2 Malam', 3, 4500000, 'per orang', 1, 20, '', NULL, 1, 1, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(5, 'Raja Ampat Ultimate Diving 5D4N', 'raja-ampat-ultimate-diving-5d4n', 5, 2, 'Ekspedisi lengkap ke Raja Ampat - biodiversitas laut tertinggi di planet ini. Piaynemo viewpoint, Wayag, dan spot diving legendaris.', '[{\"day\":1,\"title\":\"Sorong - Waisai\",\"activities\":[\"Penjemputan Bandara DEO\",\"Kapal ferry ke Waisai\",\"Check-in resort\",\"Welcome dinner\"]},{\"day\":2,\"title\":\"Piaynemo & Star Lagoon\",\"activities\":[\"Speedboat ke Piaynemo\",\"Trekking viewpoint ikonik\",\"Snorkeling Star Lagoon\",\"Snorkeling Manta Point\",\"Kembali ke Waisai\"]},{\"day\":3,\"title\":\"Wayag Expedition\",\"activities\":[\"Full day trip ke Wayag\",\"Trekking puncak Wayag\",\"Snorkeling di gugusan karst\",\"Sunset di kapal\"]},{\"day\":4,\"title\":\"Local Village & Diving\",\"activities\":[\"Kunjungi Kampung Sawinggrai\",\"Bird of Paradise watching (opsional pagi)\",\"Diving/snorkeling spot terbaik\",\"Cape Kri underwater paradise\"]},{\"day\":5,\"title\":\"Departure\",\"activities\":[\"Breakfast\",\"Ferry ke Sorong\",\"Transfer ke bandara\"]}]', '[\"Resort 4 malam\",\"Ferry Sorong-Waisai PP\",\"Speedboat harian\",\"Makan 3x sehari\",\"Guide lokal\",\"Alat snorkeling\",\"Tiket masuk semua wisata\",\"Pin masuk kawasan\"]', '[\"Tiket pesawat ke Sorong\",\"Alat diving (sewa Rp 500rb/hari)\",\"Tips guide\",\"Pengeluaran pribadi\",\"Minuman ekstra\"]', 'Booking minimum H-30. DP 40%. Best season: Oktober-April.', 'Bandara Domine Eduard Osok (DEO) Sorong', '5 Hari 4 Malam', 5, 8500000, 'per orang', 1, 20, '', NULL, 1, 1, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(6, 'Bali Cultural & Beach 4D3N', 'bali-cultural-beach-4d3n', 6, 4, 'Kombinasi sempurna budaya & pantai Bali. Ubud sawah, Tanah Lot sunset, Nusa Penida island hopping, dan Uluwatu Kecak dance.', '[{\"day\":1,\"title\":\"South Bali\",\"activities\":[\"Penjemputan bandara\",\"Pura Uluwatu sunset\",\"Kecak dance\",\"Dinner seafood Jimbaran\"]},{\"day\":2,\"title\":\"Central Bali & Ubud\",\"activities\":[\"Tegallalang rice terrace\",\"Tirta Empul\",\"Ubud monkey forest\",\"Ubud art market\",\"Menginap Ubud\"]},{\"day\":3,\"title\":\"Nusa Penida\",\"activities\":[\"Speedboat ke Nusa Penida\",\"Kelingking Beach\",\"Angel Billabong\",\"Broken Beach\",\"Return ke Bali\"]},{\"day\":4,\"title\":\"Beach & Departure\",\"activities\":[\"Tanah Lot\",\"Beachside brunch\",\"Transfer bandara\"]}]', '[\"Hotel 3 malam (kombinasi Ubud-Kuta)\",\"Mobil AC + driver\",\"Guide berlisensi\",\"Makan sesuai program\",\"Tiket wisata\",\"Speedboat Nusa Penida\"]', '[\"Tiket pesawat\",\"Makan di luar program\",\"Pengeluaran pribadi\"]', 'Cocok untuk couple/honeymoon. Booking H-14.', 'Bandara Ngurah Rai Denpasar', '4 Hari 3 Malam', 4, 5200000, 'per orang', 1, 20, '', NULL, 1, 0, 1, 4800000, NULL, 0, 1, NULL, NULL, '2026-08-19 02:29:51', '2026-08-20 06:49:01'),
(7, 'Lombok Gili Trip 3D2N', 'lombok-gili-trip-3d2n', 7, 3, 'Pesona Lombok: Gili Trawangan, pantai pink, dan Sasak village. Cocok untuk keluarga & couple.', '[{\"day\":1,\"title\":\"Gili Trawangan\",\"activities\":[\"Penjemputan bandara Lombok\",\"Fast boat ke Gili Trawangan\",\"Snorkeling turtle spot\",\"Sunset di beach club\",\"Menginap Gili\"]},{\"day\":2,\"title\":\"Central Lombok\",\"activities\":[\"Kembali ke Lombok\",\"Sasak village Sade\",\"Pantai Kuta Mandalika\",\"Tanjung Aan\",\"Menginap Kuta\"]},{\"day\":3,\"title\":\"Pink Beach\",\"activities\":[\"Pink Beach\",\"Tangsi Beach snorkeling\",\"Return & transfer bandara\"]}]', '[\"Hotel 2 malam\",\"Fast boat PP\",\"Mobil AC\",\"Makan program\",\"Guide\",\"Tiket wisata\",\"Alat snorkeling\"]', '[\"Tiket pesawat\",\"Pengeluaran pribadi\",\"Tips\"]', 'DP 40%. All seasons trip.', 'Bandara Zainuddin Abdul Madjid Lombok', '3 Hari 2 Malam', 3, 2650000, 'per orang', 1, 20, '', NULL, 1, 0, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51'),
(8, 'Bromo Sunrise Adventure 2D1N', 'bromo-sunrise-adventure-2d1n', 8, 1, 'Saksikan sunrise terbaik di Indonesia dari Penanjakan. Jeep 4x4 ke lautan pasir, kawah Bromo, dan Bukit Teletubbies.', '[{\"day\":1,\"title\":\"Journey to Bromo\",\"activities\":[\"Penjemputan Malang/Surabaya\",\"Perjalanan ke Cemoro Lawang\",\"Check-in penginapan\",\"Free time & early rest\"]},{\"day\":2,\"title\":\"Sunrise & Return\",\"activities\":[\"Bangun 03:00 - jeep ke Penanjakan\",\"Sunrise view point\",\"Kawah Bromo (trekking/kuda opsional)\",\"Pasir Berbisik & Bukit Teletubbies\",\"Breakfast\",\"Return ke kota\"]}]', '[\"Penginapan 1 malam\",\"Jeep 4x4 sharing\",\"Tiket TN Bromo\",\"Guide\",\"Breakfast\"]', '[\"Transportasi ke meeting point\",\"Sewa kuda\",\"Pengeluaran pribadi\",\"Makan siang & malam\"]', 'Sharing jeep 6 orang. Private jeep +Rp 300.000/kelompok.', 'Meeting point Malang/Surabaya', '2 Hari 1 Malam', 2, 1450000, 'per orang', 1, 20, '', NULL, 1, 0, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `tour_bookings`
--

CREATE TABLE `tour_bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_whatsapp` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `num_persons` int(11) DEFAULT 1,
  `total_price` decimal(12,0) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `status` enum('baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_categories`
--

CREATE TABLE `tour_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_categories`
--

INSERT INTO `tour_categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Open Trip', 'open-trip', NULL, '2026-08-19 02:29:51'),
(2, 'Private Trip', 'private-trip', NULL, '2026-08-19 02:29:51'),
(3, 'Family Trip', 'family-trip', NULL, '2026-08-19 02:29:51'),
(4, 'Honeymoon', 'honeymoon', NULL, '2026-08-19 02:29:51'),
(5, 'Corporate Trip', 'corporate-trip', NULL, '2026-08-19 02:29:51'),
(6, 'Adventure', 'adventure', NULL, '2026-08-19 02:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `full_name`, `role`, `is_verified`, `verification_token`, `verification_expires`, `reset_token`, `reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@dnavacation.com', '082349627779', '$2y$10$WlVjPJxVPSfzAnd5tOFRvuzS//4vxb./S0xgP4F.R2b8UL3fX9iwy', 'Administrator DNA', 'admin', 1, NULL, NULL, NULL, NULL, '2026-08-19 02:29:51', '2026-08-19 02:29:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attractions`
--
ALTER TABLE `attractions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_destination` (`destination_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_published` (`is_published`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_available` (`is_available`);

--
-- Indexes for table `car_bookings`
--
ALTER TABLE `car_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_trip_requests`
--
ALTER TABLE `custom_trip_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dest_star` (`destination_id`,`star_rating`);

--
-- Indexes for table `hotel_requests`
--
ALTER TABLE `hotel_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_destination` (`destination_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `tour_bookings`
--
ALTER TABLE `tour_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_code` (`booking_code`);

--
-- Indexes for table `tour_categories`
--
ALTER TABLE `tour_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_verification_token` (`verification_token`),
  ADD KEY `idx_reset_token` (`reset_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attractions`
--
ALTER TABLE `attractions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `car_bookings`
--
ALTER TABLE `car_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_trip_requests`
--
ALTER TABLE `custom_trip_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotel_requests`
--
ALTER TABLE `hotel_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tour_bookings`
--
ALTER TABLE `tour_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_categories`
--
ALTER TABLE `tour_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attractions`
--
ALTER TABLE `attractions`
  ADD CONSTRAINT `attractions_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `car_bookings`
--
ALTER TABLE `car_bookings`
  ADD CONSTRAINT `car_bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tours_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `tour_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tour_bookings`
--
ALTER TABLE `tour_bookings`
  ADD CONSTRAINT `tour_bookings_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
