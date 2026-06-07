-- =====================================================
-- DNA VACATION DATABASE SCHEMA
-- Import file ini ke phpMyAdmin (http://localhost/phpmyadmin)
-- Buat database "dna" dulu, lalu import file ini
-- =====================================================

CREATE DATABASE IF NOT EXISTS `dna` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dna`;

-- =====================================================
-- TABLE: admin
-- =====================================================
CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (username: admin, password: admin123)
INSERT INTO `admin` (`username`, `password`, `nama`) VALUES
('admin', '$2y$10$E6yYpJxXgVPHFmA8wF8mEewYbR2gE6.GnoH0lZl3yKxQyXz3K5kYS', 'Administrator');

-- =====================================================
-- TABLE: tour (paket tour)
-- =====================================================
CREATE TABLE IF NOT EXISTS `tour` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `judul` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) DEFAULT NULL,
    `durasi` VARCHAR(100) NOT NULL,
    `lokasi` VARCHAR(150) NOT NULL,
    `kategori` ENUM('private_trip','open_trip','family_trip','honeymoon','corporate_trip') DEFAULT 'open_trip',
    `harga` INT(11) NOT NULL DEFAULT 0,
    `deskripsi` TEXT NOT NULL,
    `itinerary` TEXT DEFAULT NULL,
    `fasilitas_termasuk` TEXT DEFAULT NULL,
    `fasilitas_tidak_termasuk` TEXT DEFAULT NULL,
    `syarat_ketentuan` TEXT DEFAULT NULL,
    `meeting_point` VARCHAR(255) DEFAULT NULL,
    `gambar` VARCHAR(255) DEFAULT NULL,
    `is_bestseller` TINYINT(1) DEFAULT 0,
    `is_promo` TINYINT(1) DEFAULT 0,
    `status` ENUM('aktif','nonaktif') DEFAULT 'aktif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample tour data
INSERT INTO `tour` (`judul`,`durasi`,`lokasi`,`kategori`,`harga`,`deskripsi`,`itinerary`,`fasilitas_termasuk`,`fasilitas_tidak_termasuk`,`meeting_point`,`gambar`,`is_bestseller`,`status`) VALUES
('Family Tour Malang 4H3M','4 Hari 3 Malam','Malang, Jawa Timur','family_trip',2500000,'Nikmati liburan keluarga ke Kota Malang dengan destinasi populer seperti Bromo, BNS, dan Museum Angkut.','Hari 1: Penjemputan & City Tour\nHari 2: Bromo Sunrise Tour\nHari 3: Museum Angkut & BNS\nHari 4: Wisata Kuliner & Pulang','Hotel bintang 3\nTransportasi AC\nTour leader\nTiket masuk wisata\nMakan sesuai program','Tiket pesawat\nKeperluan pribadi\nLaundry','Stasiun Kota Malang','yogya.jpg',1,'aktif'),
('Open Trip Bromo 2H1M','2 Hari 1 Malam','Bromo, Jawa Timur','open_trip',850000,'Nikmati keindahan sunrise Bromo dengan paket open trip ekonomis.','Hari 1: Penjemputan & ke Cemoro Lawang\nHari 2: Bromo Sunrise & Pulang','Jeep wisata\nGuide lokal\nTiket masuk\nMakan 1x','Tiket pesawat\nHotel\nKeperluan pribadi','Surabaya / Malang','Wonderful Bali.jpg',1,'aktif'),
('Honeymoon Bali 4H3M','4 Hari 3 Malam','Bali','honeymoon',4500000,'Bulan madu romantis ke Pulau Dewata dengan paket eksklusif untuk pasangan.','Hari 1: Welcome dinner\nHari 2: Tour Ubud\nHari 3: Tanah Lot Sunset\nHari 4: Free time & Pulang','Hotel romantis\nMobil pribadi\nMakan 3x sehari\nFlower bath\nCandlelight dinner','Tiket pesawat\nOleh-oleh','Bandara Ngurah Rai','SMA2.png',0,'aktif'),
('Private Trip Yogyakarta 3H2M','3 Hari 2 Malam','Yogyakarta','private_trip',1850000,'Eksplor keajaiban Yogyakarta dengan paket private trip nyaman untuk keluarga atau teman.','Hari 1: Borobudur\nHari 2: Prambanan + Malioboro\nHari 3: Pantai Parangtritis','Hotel bintang 3\nMobil pribadi\nGuide\nTiket masuk','Tiket transportasi pulang-pergi\nKeperluan pribadi','Stasiun Tugu Yogyakarta','yogya.jpg',0,'aktif'),
('Corporate Trip Surabaya','3 Hari 2 Malam','Surabaya','corporate_trip',1200000,'Paket trip korporat untuk team building atau gathering perusahaan.','Hari 1: City Tour\nHari 2: Outbond\nHari 3: Wisata Kuliner','Hotel\nBus AC\nGuide\nGames & doorprize','Konsumsi tambahan','Bandara Juanda','Surabaya.png',1,'aktif'),
('Open Trip Karimunjawa 3H2M','3 Hari 2 Malam','Karimunjawa','open_trip',1450000,'Snorkeling dan island hopping di surga tersembunyi Jepara.','Hari 1: Jepara - Karimunjawa\nHari 2: Island Hopping & Snorkeling\nHari 3: Pulang','Kapal\nHomestay\nMakan 6x\nGuide\nAlat snorkeling','Tiket KA ke Jepara\nKeperluan pribadi','Pelabuhan Kartini Jepara','Wonderful Bali.jpg',0,'aktif');

-- =====================================================
-- TABLE: tour_booking
-- =====================================================
CREATE TABLE IF NOT EXISTS `tour_booking` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tour_id` INT(11) NOT NULL,
    `nama` VARCHAR(150) NOT NULL,
    `whatsapp` VARCHAR(30) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `tanggal_keberangkatan` DATE DEFAULT NULL,
    `jumlah_peserta` INT(11) NOT NULL DEFAULT 1,
    `catatan` TEXT DEFAULT NULL,
    `status` ENUM('baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `tour_id` (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: rental (mobil)
-- =====================================================
CREATE TABLE IF NOT EXISTS `rental` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nama_mobil` VARCHAR(150) NOT NULL,
    `tipe` VARCHAR(100) DEFAULT NULL,
    `kapasitas` INT(11) NOT NULL DEFAULT 4,
    `transmisi` ENUM('manual','automatic') DEFAULT 'manual',
    `harga_per_hari` INT(11) NOT NULL DEFAULT 0,
    `harga_dengan_sopir` INT(11) DEFAULT 0,
    `area_layanan` VARCHAR(255) DEFAULT NULL,
    `fasilitas` TEXT DEFAULT NULL,
    `syarat_sewa` TEXT DEFAULT NULL,
    `gambar` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('tersedia','tidak_tersedia') DEFAULT 'tersedia',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `rental` (`nama_mobil`,`tipe`,`kapasitas`,`transmisi`,`harga_per_hari`,`harga_dengan_sopir`,`area_layanan`,`fasilitas`,`syarat_sewa`,`gambar`,`status`) VALUES
('Toyota Avanza','MPV',7,'manual',350000,500000,'Jakarta, Bogor, Depok, Tangerang, Bekasi','AC\nAudio system\nSafety belt\nBensin penuh saat pengambilan','KTP & SIM A\nKartu Keluarga\nDeposit Rp 500.000','SMA2.png','tersedia'),
('Honda Brio','City Car',5,'automatic',300000,450000,'Jakarta & sekitarnya','AC\nAudio Bluetooth\nIrit BBM','KTP & SIM A\nDeposit Rp 300.000','SMA6.png','tersedia'),
('Toyota Hiace','Mini Bus',14,'manual',1200000,1500000,'Jabodetabek & Bandung','AC double blower\nAudio premium\nSeat reclining','KTP & SIM A\nDeposit Rp 1.000.000','Surabaya.png','tersedia'),
('Toyota Innova Reborn','MPV Premium',7,'automatic',650000,850000,'Jakarta, Bandung, Yogyakarta','AC\nCaptain seat\nAudio premium\nSafety airbag','KTP & SIM A\nDeposit Rp 1.000.000','pemkot.png','tersedia'),
('Daihatsu Xenia','MPV',7,'manual',300000,450000,'Jakarta & sekitarnya','AC\nAudio system\nSafety belt','KTP & SIM A\nDeposit Rp 500.000','yogya.jpg','tersedia'),
('Toyota Alphard','Luxury MPV',6,'automatic',2500000,3000000,'Jakarta, Bandung, Surabaya','Captain seat\nMassage chair\nMini bar\nTV','KTP & SIM A\nDeposit Rp 3.000.000','Wonderful Bali.jpg','tersedia');

-- =====================================================
-- TABLE: rental_booking
-- =====================================================
CREATE TABLE IF NOT EXISTS `rental_booking` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `rental_id` INT(11) NOT NULL,
    `nama` VARCHAR(150) NOT NULL,
    `whatsapp` VARCHAR(30) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `tanggal_mulai` DATE NOT NULL,
    `tanggal_selesai` DATE NOT NULL,
    `lokasi_penjemputan` VARCHAR(255) DEFAULT NULL,
    `dengan_sopir` TINYINT(1) DEFAULT 0,
    `catatan` TEXT DEFAULT NULL,
    `status` ENUM('baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `rental_id` (`rental_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: hotel_request
-- =====================================================
CREATE TABLE IF NOT EXISTS `hotel_request` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(150) NOT NULL,
    `whatsapp` VARCHAR(30) NOT NULL,
    `destinasi` VARCHAR(255) NOT NULL,
    `check_in` DATE NOT NULL,
    `check_out` DATE NOT NULL,
    `jumlah_tamu` INT(11) DEFAULT 2,
    `budget` VARCHAR(100) DEFAULT NULL,
    `catatan` TEXT DEFAULT NULL,
    `status` ENUM('baru','diproses','selesai','dibatalkan') DEFAULT 'baru',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: blog (artikel)
-- =====================================================
CREATE TABLE IF NOT EXISTS `blog` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `judul` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) DEFAULT NULL,
    `kategori` ENUM('tips_travel','rekomendasi_destinasi','paket_tour','hotel','rental_mobil','promo') DEFAULT 'tips_travel',
    `konten` LONGTEXT NOT NULL,
    `gambar` VARCHAR(255) DEFAULT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `status` ENUM('publish','draft') DEFAULT 'publish',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blog` (`judul`,`slug`,`kategori`,`konten`,`gambar`,`meta_title`,`meta_description`,`status`) VALUES
('Tips Memilih Paket Tour yang Sesuai Budget','tips-memilih-paket-tour-budget','tips_travel','Memilih paket tour yang sesuai dengan budget bukan hal yang mudah. Berikut tips dari DNA Vacation:\n\n1. Tentukan budget maksimal Anda\n2. Bandingkan beberapa paket dari travel agent\n3. Cek apa saja fasilitas yang termasuk\n4. Perhatikan musim travel (low/high season)\n5. Pilih paket open trip untuk hemat\n\nDengan tips ini, Anda bisa liburan tanpa khawatir budget membengkak!','yogya.jpg','Tips Memilih Paket Tour Sesuai Budget - DNA Vacation','Pelajari tips ampuh memilih paket tour yang pas dengan budget Anda dari DNA Vacation.','publish'),
('Rekomendasi Destinasi Liburan Keluarga di Indonesia','rekomendasi-destinasi-liburan-keluarga','rekomendasi_destinasi','Indonesia punya banyak destinasi liburan keluarga yang seru dan ramah anak. Berikut top 5 versi DNA Vacation:\n\n1. Yogyakarta - Wisata edukasi dan budaya\n2. Bandung - Banyak taman hiburan\n3. Malang - Sejuk dan beragam wahana\n4. Bali - Pantai dan budaya\n5. Lombok - Surga alam tersembunyi\n\nMau liburan keluarga? Hubungi DNA Vacation untuk paket terbaik!','Wonderful Bali.jpg','Rekomendasi Destinasi Liburan Keluarga','Cek 5 destinasi terbaik untuk liburan keluarga di Indonesia rekomendasi DNA Vacation.','publish'),
('Kenapa Lebih Praktis Pakai Travel Agent?','kenapa-pakai-travel-agent','tips_travel','Liburan dengan travel agent seperti DNA Vacation punya banyak keuntungan:\n\n- Hemat waktu perencanaan\n- Hemat biaya (paket lebih murah)\n- Tidak ribet urus tiket dan hotel\n- Ada guide profesional\n- Itinerary sudah optimal\n\nYuk percayakan liburan Anda pada kami!','SMA2.png','Keuntungan Pakai Travel Agent - DNA Vacation','Liburan jadi praktis dan hemat dengan travel agent. Ini alasannya menurut DNA Vacation.','publish'),
('Tips Sewa Mobil Saat Liburan','tips-sewa-mobil-liburan','rental_mobil','Sewa mobil saat liburan bisa jadi solusi mobilitas. Tips dari DNA Vacation:\n\n1. Pilih jenis mobil sesuai jumlah penumpang\n2. Cek kondisi mobil sebelum sewa\n3. Pastikan asuransi lengkap\n4. Pilih dengan sopir kalau tidak terbiasa\n5. Konfirmasi area layanan\n\nDNA Vacation siap bantu rental mobil terbaik untuk liburan Anda!','SMA6.png','Tips Sewa Mobil Liburan','Tips lengkap sewa mobil untuk liburan dari DNA Vacation.','publish'),
('Checklist Sebelum Liburan agar Tidak Ada yang Tertinggal','checklist-sebelum-liburan','tips_travel','Checklist liburan dari DNA Vacation:\n\n1. Dokumen (KTP, tiket, voucher hotel)\n2. Obat-obatan pribadi\n3. Charger HP & power bank\n4. Pakaian sesuai cuaca destinasi\n5. Uang cash secukupnya\n6. Asuransi perjalanan\n\nLiburan jadi tenang dan menyenangkan!','Surabaya.png','Checklist Sebelum Liburan','Jangan lupakan checklist liburan ini. Tips dari DNA Vacation.','publish');

-- =====================================================
-- TABLE: testimoni
-- =====================================================
CREATE TABLE IF NOT EXISTS `testimoni` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(150) NOT NULL,
    `isi` TEXT NOT NULL,
    `rating` INT(1) DEFAULT 5,
    `foto` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `testimoni` (`nama`,`isi`,`rating`,`is_active`) VALUES
('Bapak Andi','Liburan keluarga ke Malang sangat menyenangkan! Pelayanan DNA Vacation profesional, mobil bersih, dan guide ramah. Terima kasih!',5,1),
('Ibu Sari','Paket honeymoon ke Bali bener-bener romantis. Hotel bagus, itinerary pas, dan harganya terjangkau. Recommended banget!',5,1),
('Pak Budi','Sewa Innova untuk dinas luar kota. Mobilnya prima dan sopirnya ramah. Sudah berkali-kali pakai DNA Vacation tidak pernah kecewa.',5,1),
('Mbak Linda','Open trip Bromo seru banget! Pesertanya friendly, guide berpengalaman, dan view sunrisenya tak terlupakan. Pasti pakai lagi!',5,1);

-- =====================================================
-- TABLE: settings (konfigurasi website)
-- =====================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`,`setting_value`) VALUES
('site_name','DNA Vacation'),
('whatsapp_number','6285280825858'),
('instagram_url','https://www.instagram.com/dnavacation/'),
('email','info@dnavacation.com'),
('alamat','Jl. Travel Dunia No. 123, Jakarta Pusat - Indonesia 10160'),
('telepon','(021) 1234 5678'),
('hero_title','Nikmati Liburanmu bersama DNA Vacation!'),
('hero_subtitle','Paket tour, sewa mobil, dan booking hotel terpercaya'),
('about_text','DNA Vacation adalah travel agent terpercaya yang menyediakan paket tour, rental mobil, dan layanan booking hotel di seluruh Indonesia. Kami berkomitmen memberikan pengalaman liburan terbaik untuk setiap pelanggan.');
