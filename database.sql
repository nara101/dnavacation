-- ============================================
-- DNA Vacation - Database Schema
-- MySQL 5.7+ / MariaDB 10.3+
-- ============================================

CREATE DATABASE IF NOT EXISTS dna_vacation
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE dna_vacation;

-- ---------- ADMIN USERS ----------
DROP TABLE IF EXISTS admin_users;
CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  role ENUM('superadmin','admin','editor') DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: admin / admin123 (CHANGE THIS!)
INSERT INTO admin_users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$WlVjPJxVPSfzAnd5tOFRvuzS//4vxb./S0xgP4F.R2b8UL3fX9iwy', 'Administrator DNA', 'admin@dnavacation.com', 'superadmin');

-- ---------- SITE SETTINGS ----------
DROP TABLE IF EXISTS site_settings;
CREATE TABLE site_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'DNA Vacation'),
('site_tagline', 'Your Journey, Our Passion'),
('whatsapp_number', '6285242000900'),
('instagram_url', 'https://www.instagram.com/dnavacation/'),
('email', 'info@dnavacation.com'),
('address', 'Surabaya, Jawa Timur, Indonesia'),
('phone', '+62 852-4200-0900'),
('about_text', 'DNA Vacation adalah travel agent kepercayaan Anda untuk menjelajahi keindahan Indonesia. Kami menyediakan paket tour, rental mobil, dan booking hotel dengan pelayanan profesional dan harga terbaik. Berpusat di Sulawesi, kami mengkhususkan diri dalam destinasi Timur Indonesia yang eksotis: Toraja, Wakatobi, Bunaken, Labuan Bajo, hingga Raja Ampat.'),
('vision', 'Menjadi travel agent terdepan di Indonesia Timur yang menghadirkan pengalaman liburan tak terlupakan dengan pelayanan berkualitas tinggi.'),
('mission', 'Menyediakan paket wisata berkualitas dengan harga kompetitif, memberikan pelayanan personal 24/7 via WhatsApp, membangun kepercayaan melalui transparansi, dan mempromosikan pariwisata Indonesia Timur ke dunia.'),
('maps_embed', ''),
('hero_headline', 'Jelajahi Keindahan Indonesia Timur bersama DNA Vacation'),
('hero_subheadline', 'Paket tour eksklusif ke Toraja, Wakatobi, Bunaken, Labuan Bajo & Raja Ampat. Rental mobil terpercaya + bantuan booking hotel — semua dalam satu tempat.'),
('hero_image', ''),
('footer_text', 'Wujudkan liburan impian Anda bersama DNA Vacation. Konsultasi gratis via WhatsApp!'),
('meta_title', 'DNA Vacation - Tour & Travel Terbaik Indonesia Timur'),
('meta_description', 'DNA Vacation menyediakan paket tour Toraja, Wakatobi, Bunaken, Labuan Bajo, Raja Ampat + rental mobil & bantuan booking hotel. Konsultasi gratis!');

-- ---------- TOUR CATEGORIES ----------
DROP TABLE IF EXISTS tour_categories;
CREATE TABLE tour_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO tour_categories (name, slug) VALUES
('Open Trip', 'open-trip'),
('Private Trip', 'private-trip'),
('Family Trip', 'family-trip'),
('Honeymoon', 'honeymoon'),
('Corporate Trip', 'corporate-trip'),
('Adventure', 'adventure');

-- ---------- DESTINATIONS ----------
DROP TABLE IF EXISTS destinations;
CREATE TABLE destinations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description TEXT,
  image VARCHAR(255),
  is_popular TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO destinations (name, slug, description, is_popular) VALUES
('Toraja', 'toraja', 'Negeri di atas awan dengan budaya unik dan pemandangan menakjubkan.', 1),
('Wakatobi', 'wakatobi', 'Surga bawah laut kelas dunia di Sulawesi Tenggara.', 1),
('Bunaken', 'bunaken', 'Taman laut ikonik Sulawesi Utara dengan biodiversitas luar biasa.', 1),
('Labuan Bajo', 'labuan-bajo', 'Gerbang menuju Komodo dan pulau-pulau eksotis Flores.', 1),
('Raja Ampat', 'raja-ampat', 'Surga terakhir di Bumi dengan keanekaragaman hayati laut tertinggi.', 1),
('Bali', 'bali', 'Pulau Dewata dengan budaya, pantai, dan spa kelas dunia.', 1),
('Lombok', 'lombok', 'Pesona Gili Trawangan, Gunung Rinjani, dan pantai pink.', 1),
('Bromo', 'bromo', 'Sunrise legendaris di Gunung Bromo, Jawa Timur.', 1),
('Yogyakarta', 'yogyakarta', 'Kota budaya, Borobudur, Prambanan, dan Malioboro.', 0),
('Derawan', 'derawan', 'Pulau-pulau tropis dengan penyu hijau dan ubur-ubur tanpa sengat.', 0),
('Belitung', 'belitung', 'Pantai pasir putih dengan formasi batu granit ikonik.', 0),
('Banda Neira', 'banda-neira', 'Kepulauan rempah bersejarah dengan snorkeling luar biasa.', 0);

-- ---------- TOUR PACKAGES ----------
DROP TABLE IF EXISTS tours;
CREATE TABLE tours (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  destination_id INT,
  category_id INT,
  description TEXT,
  itinerary TEXT,
  includes TEXT,
  excludes TEXT,
  terms TEXT,
  meeting_point VARCHAR(255),
  duration VARCHAR(50),
  duration_days INT DEFAULT 1,
  price DECIMAL(12,0) NOT NULL DEFAULT 0,
  price_label VARCHAR(50) DEFAULT 'per orang',
  min_persons INT DEFAULT 1,
  max_persons INT DEFAULT 20,
  image VARCHAR(255),
  gallery TEXT COMMENT 'JSON array of image paths',
  is_active TINYINT(1) DEFAULT 1,
  is_best_seller TINYINT(1) DEFAULT 0,
  is_promo TINYINT(1) DEFAULT 0,
  promo_price DECIMAL(12,0) DEFAULT NULL,
  departure_dates TEXT COMMENT 'JSON array of dates',
  sort_order INT DEFAULT 0,
  view_count INT DEFAULT 0,
  meta_title VARCHAR(200),
  meta_description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL,
  FOREIGN KEY (category_id) REFERENCES tour_categories(id) ON DELETE SET NULL,
  INDEX idx_active (is_active),
  INDEX idx_destination (destination_id),
  INDEX idx_category (category_id)
) ENGINE=InnoDB;

-- Sample tour packages (Indonesian Eastern destinations)
INSERT INTO tours (title, slug, destination_id, category_id, description, itinerary, includes, excludes, terms, meeting_point, duration, duration_days, price, image, is_active, is_best_seller, is_promo, promo_price) VALUES
('Toraja Cultural Heritage 3D2N', 'toraja-cultural-heritage-3d2n',
 (SELECT id FROM destinations WHERE slug='toraja'),
 (SELECT id FROM tour_categories WHERE slug='open-trip'),
 'Rasakan pengalaman spiritual dan budaya di Tana Toraja. Kunjungi Kete Kesu, Londa, Lemo, dan nikmati panorama pegunungan yang menakjubkan.',
 '[{"day":1,"title":"Makassar - Toraja","activities":["Penjemputan pagi di Makassar","Perjalanan ke Toraja via Pare-Pare","Makan siang di Restoran Padi (Pare-Pare)","Tiba di Toraja sore hari","Check-in hotel & welcome dinner"]},{"day":2,"title":"Explore Toraja","activities":["Kunjungi Kete Kesu (rumah adat & tebing kubur)","Londa (goa pemakaman & tau-tau)","Lemo (kubur tebing)","Makan siang khas Toraja (papiong)","Batutumonga - view pegunungan","Kembali ke hotel"]},{"day":3,"title":"Toraja - Makassar","activities":["Pasar Bolu (kerbau)","Ollon village (opsional)","Makan siang","Perjalanan kembali ke Makassar","Tiba malam hari"]}]',
 '["Transportasi AC PP","Hotel 2 malam (standar 3*)","Makan sesuai program (2x sarapan, 3x makan siang, 2x makan malam)","Guide lokal berpengalaman","Tiket masuk semua wisata","Air mineral","Dokumentasi drone"]',
 '["Tiket pesawat","Pengeluaran pribadi","Tips guide/driver","Asuransi perjalanan"]',
 'Minimal 2 orang. DP 50% untuk konfirmasi booking, pelunasan H-7 sebelum keberangkatan. Cancellation policy: refund 70% jika batal H-14, tidak ada refund H-7.',
 'Bandara Sultan Hasanuddin Makassar', '3 Hari 2 Malam', 3, 2850000, '', 1, 1, 0, NULL),

('Wakatobi Underwater Paradise 4D3N', 'wakatobi-underwater-paradise-4d3n',
 (SELECT id FROM destinations WHERE slug='wakatobi'),
 (SELECT id FROM tour_categories WHERE slug='adventure'),
 'Jelajahi surga bawah laut Wakatobi - salah satu spot diving terbaik dunia. Snorkeling di terumbu karang yang masih perawan, jumpa ikan-ikan warna-warni dan mungkin lumba-lumba!',
 '[{"day":1,"title":"Arrival Wakatobi","activities":["Penjemputan Bandara Matahora","Transfer ke penginapan di Wangi-Wangi","Welcome briefing","Snorkeling perkenalan di Waha","Sunset dinner"]},{"day":2,"title":"Island Hopping Kaledupa","activities":["Speedboat ke Pulau Hoga","Snorkeling di Coral Garden","Makan siang di pulau","Snorkeling di Sombu Dive Point","Kembali ke Wangi-Wangi"]},{"day":3,"title":"Tomia Adventure","activities":["Perjalanan ke Tomia","Snorkeling Roma Point (spot manta)","Puncak Kahyangan sunset","Menginap di Tomia"]},{"day":4,"title":"Return","activities":["Breakfast","Snorkeling terakhir","Transfer ke bandara","Departure"]}]',
 '["Penginapan 3 malam","Speedboat & transportasi","Makan 3x sehari","Guide profesional","Alat snorkeling lengkap","Tiket masuk & fee wisata","Air mineral","Dokumentasi underwater"]',
 '["Tiket pesawat ke Wakatobi","Alat diving (opsional sewa)","Tips guide","Pengeluaran pribadi","Asuransi"]',
 'Minimal 4 orang untuk private trip. Open trip jadwal fixed. Booking H-14 sebelum keberangkatan.',
 'Bandara Matahora, Wangi-Wangi', '4 Hari 3 Malam', 4, 4800000, '', 1, 1, 1, 4200000),

('Bunaken Snorkeling Explorer 3D2N', 'bunaken-snorkeling-explorer-3d2n',
 (SELECT id FROM destinations WHERE slug='bunaken'),
 (SELECT id FROM tour_categories WHERE slug='family-trip'),
 'Nikmati pesona Taman Nasional Bunaken. Snorkeling di dinding karang tegak lurus, jumpa penyu, dan lumba-lumba di Selat Lembeh.',
 '[{"day":1,"title":"Manado Welcome","activities":["Penjemputan Bandara Sam Ratulangi","City tour Manado","Kuliner khas: tinutuan/bubur manado","Transfer ke hotel","Malam bebas"]},{"day":2,"title":"Bunaken Marine Park","activities":["Speedboat ke Bunaken","Snorkeling 3 spot terbaik","Makan siang seafood BBQ","Pulau Siladen","Kembali ke Manado sore"]},{"day":3,"title":"Tomohon & Departure","activities":["Pasar ekstrem Tomohon","Bukit Kasih","Danau Linow (danau warna-warni)","Transfer ke bandara"]}]',
 '["Hotel 2 malam","Transportasi AC","Speedboat ke Bunaken","Makan sesuai program","Guide lokal","Alat snorkeling","Tiket wisata"]',
 '["Tiket pesawat","Diving (opsional)","Pengeluaran pribadi","Tips"]',
 'DP 40% untuk booking. Cocok untuk keluarga & pemula snorkeling.',
 'Bandara Sam Ratulangi Manado', '3 Hari 2 Malam', 3, 3200000, '', 1, 1, 0, NULL),

('Labuan Bajo Sailing Komodo 3D2N', 'labuan-bajo-sailing-komodo-3d2n',
 (SELECT id FROM destinations WHERE slug='labuan-bajo'),
 (SELECT id FROM tour_categories WHERE slug='open-trip'),
 'Petualangan sailing dengan phinisi ke Taman Nasional Komodo. Temui komodo, snorkeling di Pink Beach, dan sunset di Pulau Padar.',
 '[{"day":1,"title":"Welcome to Flores","activities":["Penjemputan Bandara Komodo","Transfer ke pelabuhan","Sailing ke Pulau Kelor","Snorkeling & sunset di kapal","Menginap di kapal phinisi"]},{"day":2,"title":"Komodo National Park","activities":["Trekking Pulau Padar sunrise","Pink Beach snorkeling","Trekking Pulau Komodo (jumpa komodo)","Manta Point snorkeling","Pulau Kanawa sunset"]},{"day":3,"title":"Final Day","activities":["Sunrise di kapal","Gili Laba trekking","Return ke Labuan Bajo","Transfer ke bandara"]}]',
 '["Kapal phinisi AC (2 malam)","Makan 3x sehari","Guide berlisensi","Alat snorkeling","Tiket TN Komodo","Ranger fee","Dokumentasi drone & underwater"]',
 '["Tiket pesawat","Tips crew","Pengeluaran pribadi","Minuman alkohol"]',
 'DP 50%. Weather-dependent - jadwal bisa berubah karena cuaca laut.',
 'Bandara Komodo, Labuan Bajo', '3 Hari 2 Malam', 3, 4500000, '', 1, 1, 0, NULL),

('Raja Ampat Ultimate Diving 5D4N', 'raja-ampat-ultimate-diving-5d4n',
 (SELECT id FROM destinations WHERE slug='raja-ampat'),
 (SELECT id FROM tour_categories WHERE slug='private-trip'),
 'Ekspedisi lengkap ke Raja Ampat - biodiversitas laut tertinggi di planet ini. Piaynemo viewpoint, Wayag, dan spot diving legendaris.',
 '[{"day":1,"title":"Sorong - Waisai","activities":["Penjemputan Bandara DEO","Kapal ferry ke Waisai","Check-in resort","Welcome dinner"]},{"day":2,"title":"Piaynemo & Star Lagoon","activities":["Speedboat ke Piaynemo","Trekking viewpoint ikonik","Snorkeling Star Lagoon","Snorkeling Manta Point","Kembali ke Waisai"]},{"day":3,"title":"Wayag Expedition","activities":["Full day trip ke Wayag","Trekking puncak Wayag","Snorkeling di gugusan karst","Sunset di kapal"]},{"day":4,"title":"Local Village & Diving","activities":["Kunjungi Kampung Sawinggrai","Bird of Paradise watching (opsional pagi)","Diving/snorkeling spot terbaik","Cape Kri underwater paradise"]},{"day":5,"title":"Departure","activities":["Breakfast","Ferry ke Sorong","Transfer ke bandara"]}]',
 '["Resort 4 malam","Ferry Sorong-Waisai PP","Speedboat harian","Makan 3x sehari","Guide lokal","Alat snorkeling","Tiket masuk semua wisata","Pin masuk kawasan"]',
 '["Tiket pesawat ke Sorong","Alat diving (sewa Rp 500rb/hari)","Tips guide","Pengeluaran pribadi","Minuman ekstra"]',
 'Booking minimum H-30. DP 40%. Best season: Oktober-April.',
 'Bandara Domine Eduard Osok (DEO) Sorong', '5 Hari 4 Malam', 5, 8500000, '', 1, 1, 0, NULL),

('Bali Cultural & Beach 4D3N', 'bali-cultural-beach-4d3n',
 (SELECT id FROM destinations WHERE slug='bali'),
 (SELECT id FROM tour_categories WHERE slug='honeymoon'),
 'Kombinasi sempurna budaya & pantai Bali. Ubud sawah, Tanah Lot sunset, Nusa Penida island hopping, dan Uluwatu Kecak dance.',
 '[{"day":1,"title":"South Bali","activities":["Penjemputan bandara","Pura Uluwatu sunset","Kecak dance","Dinner seafood Jimbaran"]},{"day":2,"title":"Central Bali & Ubud","activities":["Tegallalang rice terrace","Tirta Empul","Ubud monkey forest","Ubud art market","Menginap Ubud"]},{"day":3,"title":"Nusa Penida","activities":["Speedboat ke Nusa Penida","Kelingking Beach","Angel Billabong","Broken Beach","Return ke Bali"]},{"day":4,"title":"Beach & Departure","activities":["Tanah Lot","Beachside brunch","Transfer bandara"]}]',
 '["Hotel 3 malam (kombinasi Ubud-Kuta)","Mobil AC + driver","Guide berlisensi","Makan sesuai program","Tiket wisata","Speedboat Nusa Penida"]',
 '["Tiket pesawat","Makan di luar program","Pengeluaran pribadi"]',
 'Cocok untuk couple/honeymoon. Booking H-14.',
 'Bandara Ngurah Rai Denpasar', '4 Hari 3 Malam', 4, 5200000, '', 1, 0, 1, 4800000),

('Lombok Gili Trip 3D2N', 'lombok-gili-trip-3d2n',
 (SELECT id FROM destinations WHERE slug='lombok'),
 (SELECT id FROM tour_categories WHERE slug='family-trip'),
 'Pesona Lombok: Gili Trawangan, pantai pink, dan Sasak village. Cocok untuk keluarga & couple.',
 '[{"day":1,"title":"Gili Trawangan","activities":["Penjemputan bandara Lombok","Fast boat ke Gili Trawangan","Snorkeling turtle spot","Sunset di beach club","Menginap Gili"]},{"day":2,"title":"Central Lombok","activities":["Kembali ke Lombok","Sasak village Sade","Pantai Kuta Mandalika","Tanjung Aan","Menginap Kuta"]},{"day":3,"title":"Pink Beach","activities":["Pink Beach","Tangsi Beach snorkeling","Return & transfer bandara"]}]',
 '["Hotel 2 malam","Fast boat PP","Mobil AC","Makan program","Guide","Tiket wisata","Alat snorkeling"]',
 '["Tiket pesawat","Pengeluaran pribadi","Tips"]',
 'DP 40%. All seasons trip.',
 'Bandara Zainuddin Abdul Madjid Lombok', '3 Hari 2 Malam', 3, 2650000, '', 1, 0, 0, NULL),

('Bromo Sunrise Adventure 2D1N', 'bromo-sunrise-adventure-2d1n',
 (SELECT id FROM destinations WHERE slug='bromo'),
 (SELECT id FROM tour_categories WHERE slug='open-trip'),
 'Saksikan sunrise terbaik di Indonesia dari Penanjakan. Jeep 4x4 ke lautan pasir, kawah Bromo, dan Bukit Teletubbies.',
 '[{"day":1,"title":"Journey to Bromo","activities":["Penjemputan Malang/Surabaya","Perjalanan ke Cemoro Lawang","Check-in penginapan","Free time & early rest"]},{"day":2,"title":"Sunrise & Return","activities":["Bangun 03:00 - jeep ke Penanjakan","Sunrise view point","Kawah Bromo (trekking/kuda opsional)","Pasir Berbisik & Bukit Teletubbies","Breakfast","Return ke kota"]}]',
 '["Penginapan 1 malam","Jeep 4x4 sharing","Tiket TN Bromo","Guide","Breakfast"]',
 '["Transportasi ke meeting point","Sewa kuda","Pengeluaran pribadi","Makan siang & malam"]',
 'Sharing jeep 6 orang. Private jeep +Rp 300.000/kelompok.',
 'Meeting point Malang/Surabaya', '2 Hari 1 Malam', 2, 1450000, '', 1, 0, 0, NULL);

-- ---------- TOUR BOOKINGS ----------
DROP TABLE IF EXISTS tour_bookings;
CREATE TABLE tour_bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_code VARCHAR(20) NOT NULL UNIQUE,
  tour_id INT,
  customer_name VARCHAR(100) NOT NULL,
  customer_whatsapp VARCHAR(20) NOT NULL,
  customer_email VARCHAR(100),
  departure_date DATE,
  num_persons INT DEFAULT 1,
  total_price DECIMAL(12,0) DEFAULT 0,
  notes TEXT,
  status ENUM('baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL,
  INDEX idx_status (status),
  INDEX idx_code (booking_code)
) ENGINE=InnoDB;

-- ---------- CARS ----------
DROP TABLE IF EXISTS cars;
CREATE TABLE cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  brand VARCHAR(50),
  type ENUM('mpv','suv','sedan','bus','minibus','pickup','city') DEFAULT 'mpv',
  capacity INT DEFAULT 4,
  luggage INT DEFAULT 2,
  transmission ENUM('manual','automatic') DEFAULT 'automatic',
  fuel_type ENUM('bensin','solar','listrik','hybrid') DEFAULT 'bensin',
  year INT DEFAULT 2022,
  price_with_driver DECIMAL(12,0) DEFAULT 0,
  price_without_driver DECIMAL(12,0) DEFAULT 0,
  has_driver_option TINYINT(1) DEFAULT 1,
  has_self_drive TINYINT(1) DEFAULT 0,
  facilities TEXT COMMENT 'JSON array',
  terms TEXT,
  service_area VARCHAR(255),
  image VARCHAR(255),
  gallery TEXT COMMENT 'JSON array',
  is_available TINYINT(1) DEFAULT 1,
  is_featured TINYINT(1) DEFAULT 0,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_available (is_available)
) ENGINE=InnoDB;

-- Sample cars
INSERT INTO cars (name, slug, brand, type, capacity, luggage, transmission, fuel_type, year, price_with_driver, price_without_driver, has_driver_option, has_self_drive, facilities, service_area, is_available, is_featured, description) VALUES
('Toyota Avanza New', 'toyota-avanza-new', 'Toyota', 'mpv', 7, 2, 'automatic', 'bensin', 2023, 550000, 350000, 1, 1, '["AC","Audio","Charger USB","Bagasi luas","Kursi nyaman"]', 'Makassar & Sekitarnya', 1, 1, 'MPV andalan untuk perjalanan keluarga. Nyaman, irit BBM, dan bagasi luas.'),
('Toyota Innova Reborn', 'toyota-innova-reborn', 'Toyota', 'mpv', 7, 3, 'automatic', 'solar', 2023, 800000, 600000, 1, 1, '["AC double blower","Audio touchscreen","Charger USB","Jok kulit","Cruise control","Bagasi luas"]', 'Sulawesi Selatan', 1, 1, 'MPV premium dengan kenyamanan ekstra. Cocok untuk perjalanan jarak jauh Toraja/Bulukumba.'),
('Mitsubishi Pajero Sport', 'mitsubishi-pajero-sport', 'Mitsubishi', 'suv', 7, 3, 'automatic', 'solar', 2022, 1100000, 0, 1, 0, '["AC","4WD","Audio premium","Cruise control","Sunroof","Rear entertainment"]', 'All Sulawesi + Adventure', 1, 1, 'SUV tangguh untuk segala medan. Ideal untuk adventure trip ke Rammang-Rammang, Bantimurung.'),
('Toyota HiAce Commuter', 'toyota-hiace-commuter', 'Toyota', 'minibus', 15, 5, 'manual', 'solar', 2022, 1400000, 0, 1, 0, '["AC","Reclining seat","Audio","Bagasi besar","Mikrofon","TV monitor"]', 'Sulawesi Selatan & Barat', 1, 0, 'Minibus nyaman untuk rombongan besar. Cocok untuk group trip, corporate outing, family gathering.'),
('Honda Brio', 'honda-brio', 'Honda', 'city', 5, 1, 'automatic', 'bensin', 2023, 400000, 300000, 1, 1, '["AC","Audio","Charger USB","Irit BBM","Lincah di kota"]', 'Dalam Kota Makassar', 1, 0, 'City car ekonomis dan lincah. Cocok untuk solo traveler atau couple wisata dalam kota.'),
('Toyota Alphard', 'toyota-alphard', 'Toyota', 'mpv', 7, 3, 'automatic', 'bensin', 2022, 2500000, 0, 1, 0, '["AC dual zone","Kursi captain","Audio JBL","Sunroof","Kulkas mini","Sopir formal"]', 'Sulawesi Selatan', 1, 1, 'Executive MPV untuk VVIP, wedding car, atau corporate high-level meeting.'),
('Suzuki Ertiga', 'suzuki-ertiga', 'Suzuki', 'mpv', 7, 2, 'automatic', 'bensin', 2023, 500000, 350000, 1, 1, '["AC","Audio","USB port","Bagasi cukup"]', 'Makassar & Sekitarnya', 1, 0, 'MPV ekonomis dengan kabin luas. Alternatif Avanza dengan feel yang lebih modern.');

-- ---------- CAR BOOKINGS ----------
DROP TABLE IF EXISTS car_bookings;
CREATE TABLE car_bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_code VARCHAR(20) NOT NULL UNIQUE,
  car_id INT,
  customer_name VARCHAR(100) NOT NULL,
  customer_whatsapp VARCHAR(20) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  pickup_time TIME DEFAULT '08:00:00',
  pickup_location VARCHAR(255),
  return_location VARCHAR(255),
  with_driver TINYINT(1) DEFAULT 1,
  total_price DECIMAL(12,0) DEFAULT 0,
  notes TEXT,
  status ENUM('baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL,
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ---------- HOTEL REQUESTS ----------
DROP TABLE IF EXISTS hotel_requests;
CREATE TABLE hotel_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100) NOT NULL,
  customer_whatsapp VARCHAR(20) NOT NULL,
  destination VARCHAR(200),
  check_in DATE,
  check_out DATE,
  num_guests INT DEFAULT 1,
  num_rooms INT DEFAULT 1,
  budget VARCHAR(100),
  hotel_type VARCHAR(100),
  notes TEXT,
  status ENUM('baru','diproses','terkonfirmasi','selesai','dibatalkan') DEFAULT 'baru',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- BLOG ----------
DROP TABLE IF EXISTS blog_categories;
CREATE TABLE blog_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO blog_categories (name, slug) VALUES
('Tips Travel', 'tips-travel'),
('Destinasi', 'destinasi'),
('Kuliner', 'kuliner'),
('Rental Mobil', 'rental-mobil'),
('Promo', 'promo'),
('Budaya', 'budaya');

DROP TABLE IF EXISTS blog_posts;
CREATE TABLE blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  category_id INT,
  excerpt TEXT,
  content LONGTEXT,
  image VARCHAR(255),
  author_id INT,
  is_published TINYINT(1) DEFAULT 0,
  meta_title VARCHAR(200),
  meta_description TEXT,
  view_count INT DEFAULT 0,
  published_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
  FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL,
  INDEX idx_published (is_published),
  INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Sample blog posts
INSERT INTO blog_posts (title, slug, category_id, excerpt, content, is_published, published_at, author_id) VALUES
('7 Alasan Wajib Kunjungi Toraja Sekali Seumur Hidup', '7-alasan-wajib-kunjungi-toraja',
 (SELECT id FROM blog_categories WHERE slug='destinasi'),
 'Toraja bukan sekedar destinasi wisata biasa. Simak 7 alasan kenapa Toraja wajib masuk bucket list Anda.',
 '<h2>1. Budaya Unik yang Tidak Ada di Tempat Lain</h2><p>Ritual Rambu Solo (upacara pemakaman) adalah tradisi yang unik di dunia. Anda akan menyaksikan bagaimana masyarakat Toraja menghormati orang yang telah tiada dengan cara yang sangat spesial dan penuh makna.</p><h2>2. Rumah Adat Tongkonan yang Megah</h2><p>Arsitektur rumah adat Tongkonan dengan atap melengkung seperti perahu adalah salah satu warisan budaya paling ikonik di Indonesia. Setiap ukiran memiliki makna filosofis yang mendalam.</p><h2>3. Pemandangan Alam yang Memukau</h2><p>Dari Batutumonga Anda bisa melihat hamparan sawah terasering dan pegunungan yang menakjubkan. Toraja dijuluki "negeri di atas awan" karena pemandangannya yang spektakuler.</p><h2>4. Kuliner Khas yang Menggugah Selera</h2><p>Papiong (daging masak dalam bambu), Pantollo Pamarrasan (ikan mas kuah hitam), dan kopi Toraja yang mendunia. Kuliner Toraja adalah pengalaman kuliner yang tidak boleh dilewatkan.</p><h2>5. Kete Kesu - Desa Adat Terpelihara</h2><p>Kompleks rumah tongkonan tertua yang masih terjaga keasliannya. Di sini Anda bisa melihat proses kehidupan tradisional Toraja yang belum banyak berubah selama berabad-abad.</p><h2>6. Kopi Toraja Kelas Dunia</h2><p>Kopi Toraja termasuk salah satu kopi terbaik di dunia. Nikmati langsung di kebunnya dan pelajari proses dari petik hingga siap seduh.</p><h2>7. Aksesibilitas yang Semakin Mudah</h2><p>Dengan pembukaan Bandara Toraja (Buntu Kunik), kunjungan ke Toraja jadi lebih mudah dan cepat.</p><h3>Siap Menjelajahi Toraja?</h3><p>DNA Vacation menyediakan paket Toraja 3D2N dengan itinerary lengkap. Hubungi kami untuk info & booking!</p>',
 1, NOW(), 1),

('Wakatobi vs Bunaken: Mana yang Lebih Cocok untuk Anda?', 'wakatobi-vs-bunaken',
 (SELECT id FROM blog_categories WHERE slug='destinasi'),
 'Bingung memilih antara Wakatobi dan Bunaken untuk liburan snorkeling? Simak perbandingan lengkapnya di sini.',
 '<h2>Wakatobi: Surga Tersembunyi</h2><p>Wakatobi merupakan singkatan dari 4 pulau utama: Wangi-Wangi, Kaledupa, Tomia, dan Binongko. Terkenal sebagai salah satu spot diving terbaik di dunia dengan biodiversitas luar biasa.</p><p><strong>Kelebihan Wakatobi:</strong></p><ul><li>Kondisi karang masih sangat perawan</li><li>Kepadatan wisatawan lebih sedikit</li><li>Pengalaman lebih otentik & eksklusif</li><li>Cocok untuk diver berpengalaman</li></ul><p><strong>Kekurangan:</strong></p><ul><li>Akses lebih sulit (transit di Baubau)</li><li>Fasilitas terbatas dibanding Bunaken</li><li>Biaya cenderung lebih tinggi</li></ul><h2>Bunaken: Klasik yang Selalu Menawan</h2><p>Bunaken adalah taman laut nasional pertama di Indonesia. Terkenal dengan dinding karang tegak lurus (vertical wall) hingga 1500m dan populasi ikan yang melimpah.</p><p><strong>Kelebihan Bunaken:</strong></p><ul><li>Akses mudah dari Manado</li><li>Cocok untuk keluarga & pemula</li><li>Banyak pilihan akomodasi</li><li>Kuliner Manado kelas dunia</li></ul><p><strong>Kekurangan:</strong></p><ul><li>Beberapa spot sudah cukup ramai</li><li>Kerusakan karang di area populer</li></ul><h2>Kesimpulan</h2><p>Pilih <strong>Wakatobi</strong> jika Anda: diver berpengalaman, cari pengalaman eksklusif, punya budget cukup, tidak keberatan dengan akses yang sedikit challenging.</p><p>Pilih <strong>Bunaken</strong> jika Anda: keluarga dengan anak-anak, pemula snorkeling, cari kombinasi wisata laut + kuliner + kota, ingin akses mudah.</p><p>Kedua destinasi punya keunikan masing-masing. Bahkan banyak wisatawan yang mengkombinasikan keduanya dalam satu trip!</p>',
 1, NOW(), 1),

('Tips Hemat Sewa Mobil untuk Liburan Keluarga', 'tips-hemat-sewa-mobil',
 (SELECT id FROM blog_categories WHERE slug='rental-mobil'),
 'Panduan lengkap sewa mobil hemat dan nyaman untuk liburan keluarga. Simak tips-tipsnya di sini!',
 '<h2>1. Booking Jauh-Jauh Hari</h2><p>Booking H-14 atau lebih biasanya mendapatkan harga terbaik. Terutama untuk high season seperti liburan sekolah dan lebaran.</p><h2>2. Pilih Mobil Sesuai Kebutuhan</h2><p>Tidak semua liburan membutuhkan Alphard. Untuk keluarga 4-5 orang, Avanza atau Ertiga sudah sangat cukup dan lebih hemat BBM.</p><h2>3. Perhitungkan Total Kilometer</h2><p>Jika perjalanan jauh, mobil dengan konsumsi BBM irit seperti Innova diesel akan lebih hemat dibanding SUV bensin.</p><h2>4. Cek Include & Exclude</h2><p>Pastikan Anda tahu apa saja yang termasuk: BBM, tol, parkir, sopir, uang makan sopir? Pertanyaan detail ini menghindari surprise di akhir sewa.</p><h2>5. Foto Kondisi Mobil di Awal</h2><p>Selalu dokumentasikan kondisi mobil sebelum berangkat. Ini melindungi Anda dari klaim kerusakan yang tidak Anda lakukan.</p><h2>6. Pahami Kebijakan Kelebihan Jam</h2><p>1 hari sewa umumnya 12 jam. Setelah itu ada charge tambahan. Untuk trip 2-3 hari, sewa dengan hitungan hari lebih menguntungkan.</p><h2>7. Sopir vs Self Drive</h2><p>Sopir bertambah biaya, tapi Anda bisa istirahat dan menikmati pemandangan. Untuk perjalanan panjang (>4 jam), dengan sopir lebih direkomendasikan.</p><h3>Butuh Rental Mobil?</h3><p>DNA Vacation menyediakan berbagai jenis mobil dengan harga transparan. Hubungi kami untuk konsultasi gratis!</p>',
 1, NOW(), 1),

('Panduan Sunrise di Bromo: Tips dari yang Sudah Coba', 'panduan-sunrise-bromo',
 (SELECT id FROM blog_categories WHERE slug='tips-travel'),
 'Ingin melihat sunrise legendaris di Bromo? Simak panduan lengkapnya agar tidak zonk saat berangkat!',
 '<h2>Kapan Waktu Terbaik?</h2><p>Musim kemarau (April-Oktober) adalah waktu terbaik karena minim awan dan hujan. Sunrise time berkisar 05:15-05:45 WIB.</p><h2>Berangkat Jam Berapa dari Cemoro Lawang?</h2><p>Untuk sunrise 05:30, minimal berangkat 03:30 dini hari agar tiba di Penanjakan 04:15-04:30. Ini kasih waktu untuk cari spot terbaik.</p><h2>Perlengkapan Wajib</h2><ul><li>Jaket tebal (suhu bisa 5°C)</li><li>Sarung tangan & kupluk</li><li>Sepatu tertutup</li><li>Masker (debu vulkanik)</li><li>Kamera + tripod</li><li>Powerbank</li></ul><h2>Pilihan Spot Sunrise</h2><p><strong>Penanjakan 1:</strong> View klasik, paling ramai, akses jeep.<br><strong>Penanjakan 2:</strong> Lebih tinggi, view lebih dramatis, less crowded.<br><strong>Bukit Kingkong:</strong> Alternatif ketika Penanjakan penuh.<br><strong>Bukit Cinta:</strong> Baru dan sunyi, view berbeda.</p><h2>Setelah Sunrise</h2><p>Setelah sunrise, langsung ke kawah Bromo. Trekking dari parkir jeep sekitar 30 menit, atau naik kuda (Rp 100-150rb sekali jalan). Setelah kawah, mampir ke Pasir Berbisik dan Bukit Teletubbies (Savana).</p><h2>Kesalahan yang Sering Terjadi</h2><ul><li>Kepagian → nunggu lama & kedinginan</li><li>Kesiangan → sunrise sudah lewat</li><li>Baju kurang tebal → kedinginan</li><li>Tidak sarapan → lemas saat trekking</li></ul><h3>Ingin All-In-Package?</h3><p>DNA Vacation punya paket Bromo Sunrise 2D1N dengan semua handled by us. Cek katalog kami!</p>',
 1, NOW(), 1),

('Kuliner Wajib Coba saat Liburan ke Sulawesi', 'kuliner-wajib-sulawesi',
 (SELECT id FROM blog_categories WHERE slug='kuliner'),
 'Liburan ke Sulawesi belum lengkap tanpa mencicipi kuliner khasnya. Ini list wajib coba dari lokal!',
 '<h2>Sulawesi Selatan - Makassar</h2><p><strong>1. Coto Makassar</strong> - Sup daging dengan kuah kacang khas. Best di Coto Nusantara atau Coto Paraikatte.</p><p><strong>2. Sop Konro</strong> - Iga sapi dengan kuah rempah. Coba Konro Karebosi.</p><p><strong>3. Pisang Epe</strong> - Dessert manis dengan gula merah cair. Pinggir Pantai Losari.</p><p><strong>4. Pallubasa</strong> - Sup daging serupa coto tapi ada tambahan telur mentah.</p><h2>Sulawesi Selatan - Toraja</h2><p><strong>1. Papiong</strong> - Daging babi/ayam dimasak dalam bambu.</p><p><strong>2. Pantollo Pamarrasan</strong> - Ikan mas kuah hitam khas Toraja.</p><p><strong>3. Kopi Toraja</strong> - Wajib coba langsung di kebunnya!</p><h2>Sulawesi Tenggara - Kendari</h2><p><strong>1. Sinonggi</strong> - Sagu kenyal dengan kuah ikan.</p><p><strong>2. Kabuto</strong> - Singkong fermentasi.</p><h2>Sulawesi Utara - Manado</h2><p><strong>1. Tinutuan (Bubur Manado)</strong> - Bubur sayur khas.</p><p><strong>2. Cakalang Fufu</strong> - Ikan cakalang asap.</p><p><strong>3. Nasi Kuning Manado</strong> - Dengan cakalang & sambal roa.</p><h2>Sulawesi Tengah - Palu</h2><p><strong>1. Kaledo</strong> - Sup kaki sapi khas Palu.</p><p><strong>2. Uta Dada</strong> - Ayam santan kelapa.</p><p>Selamat berpetualang kuliner!</p>',
 1, NOW(), 1);

-- ---------- TESTIMONIALS ----------
DROP TABLE IF EXISTS testimonials;
CREATE TABLE testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100) NOT NULL,
  customer_location VARCHAR(100),
  content TEXT NOT NULL,
  rating TINYINT DEFAULT 5,
  tour_id INT,
  tour_name VARCHAR(200),
  image VARCHAR(255),
  is_active TINYINT(1) DEFAULT 0 COMMENT '0=pending review, 1=approved & shown',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL,
  INDEX idx_active (is_active)
) ENGINE=InnoDB;

INSERT INTO testimonials (customer_name, customer_location, content, rating, tour_id, tour_name, is_active) VALUES
('Rina Susanti', 'Jakarta',
 'Trip ke Toraja bareng DNA Vacation luar biasa! Guide-nya sangat expert soal budaya lokal, hotelnya nyaman, dan makanan enak semua. Papiong-nya endulita! Pasti trip lagi bareng DNA.',
 5, (SELECT id FROM tours WHERE slug='toraja-cultural-heritage-3d2n'), 'Toraja Cultural Heritage 3D2N', 1),

('Budi Hartono', 'Surabaya',
 'Wakatobi memang surga bawah laut. Terumbu karangnya masih perawan dan ikan warna-warni banyak sekali. Crew DNA Vacation sangat helpful, alat snorkeling bagus. Recommended banget!',
 5, (SELECT id FROM tours WHERE slug='wakatobi-underwater-paradise-4d3n'), 'Wakatobi Underwater Paradise', 1),

('Dewi Anggraini', 'Bandung',
 'Sewa Innova di DNA Vacation prosesnya mudah dan cepat. Mobil bersih, sopirnya (Pak Ridwan) ramah dan tahu banyak tempat wisata hidden gem. Highly recommended!',
 5, NULL, 'Rental Innova', 1),

('Ahmad Rizki', 'Makassar',
 'Family trip ke Bali via DNA Vacation sangat menyenangkan. Anak-anak happy, orang tua puas. Nusa Penida island hopping-nya jadi highlight liburan. Terima kasih tim DNA!',
 5, (SELECT id FROM tours WHERE slug='bali-cultural-beach-4d3n'), 'Bali Cultural & Beach', 1),

('Siti Nurhaliza', 'Yogyakarta',
 'Labuan Bajo trip is a bucket list checked! Sailing di phinisi sunset, ketemu komodo, snorkeling Pink Beach. Semua terkoordinasi dengan rapi. Fotografer drone-nya juga cakep hasilnya!',
 5, (SELECT id FROM tours WHERE slug='labuan-bajo-sailing-komodo-3d2n'), 'Labuan Bajo Sailing Komodo', 1),

('Michael Tanudibrata', 'Jakarta',
 'Trip Raja Ampat 5D4N adalah pengalaman diving terbaik saya. Cape Kri memang legend! DNA Vacation handle semua logistik dengan sangat baik. Worth every rupiah!',
 5, (SELECT id FROM tours WHERE slug='raja-ampat-ultimate-diving-5d4n'), 'Raja Ampat Ultimate Diving', 1),

('Fitri Handayani', 'Semarang',
 'Bunaken trip bersama keluarga besar (12 orang) super smooth. Speedboat aman, snorkeling di 3 spot terbaik, dan makanan Manado juara. Recommended untuk family trip!',
 4, (SELECT id FROM tours WHERE slug='bunaken-snorkeling-explorer-3d2n'), 'Bunaken Snorkeling Explorer', 1),

('Arif Wibowo', 'Surabaya',
 'Bromo sunrise trip 2D1N pas budget dan waktunya. Jeep tepat waktu, guide informatif, sunrise memang worth waking up 03:00 AM. Terima kasih DNA Vacation!',
 5, (SELECT id FROM tours WHERE slug='bromo-sunrise-adventure-2d1n'), 'Bromo Sunrise Adventure', 1);

-- ---------- CUSTOM TRIP REQUESTS ----------
DROP TABLE IF EXISTS custom_trip_requests;
CREATE TABLE custom_trip_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100) NOT NULL,
  customer_whatsapp VARCHAR(20) NOT NULL,
  customer_email VARCHAR(100),
  destination VARCHAR(200),
  trip_type VARCHAR(50),
  num_persons INT DEFAULT 1,
  budget_range VARCHAR(100),
  preferred_dates TEXT,
  special_requests TEXT,
  status ENUM('baru','diproses','penawaran_terkirim','terkonfirmasi','selesai') DEFAULT 'baru',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- BANNERS ----------
DROP TABLE IF EXISTS banners;
CREATE TABLE banners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200),
  subtitle TEXT,
  image VARCHAR(255),
  link VARCHAR(255),
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- CONTACT MESSAGES ----------
DROP TABLE IF EXISTS contact_messages;
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  whatsapp VARCHAR(20),
  subject VARCHAR(200),
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
