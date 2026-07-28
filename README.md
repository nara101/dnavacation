# DNA Vacation - Website Travel & Tour Booking

Travel agent website untuk paket tour, rental mobil, dan booking hotel — dengan spesialisasi destinasi Indonesia Timur (Toraja, Wakatobi, Bunaken, Labuan Bajo, Raja Ampat).

## Persyaratan Sistem
- PHP >= 7.4 (disarankan 8.0+)
- MySQL >= 5.7
- Apache dengan mod_rewrite
- Ekstensi PHP: PDO, pdo_mysql, mbstring, gd

## Cara Install

### 1. Upload File
Upload seluruh folder `dna-vacation` ke `htdocs` (XAMPP) atau `www` (Laragon).

### 2. Buat Database
- Buka phpMyAdmin
- Klik tab **Import** — pilih file `database.sql`
- Klik **Go/Execute** (database `dna_vacation` akan dibuat otomatis dengan sample data)

### 3. Konfigurasi
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dna_vacation');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/dna-vacation');
define('DEFAULT_WA', '6285242000900'); // fallback WA jika settings kosong
```
Semua kontak lain (WhatsApp, Instagram, alamat, email) diatur di admin panel → Pengaturan.

### 4. Permission Upload
Pastikan folder `uploads/` bisa ditulis:
```bash
chmod -R 755 uploads/
```

### 5. Akses Website
- **Website**: http://localhost/dna-vacation/
- **Admin Panel**: http://localhost/dna-vacation/admin/
- **Login Admin**: username `admin`, password `admin123`

> **PENTING**: Ganti password admin setelah login pertama!

## Fitur Utama

### Public
- **Homepage** — hero + search box (tour/car/hotel), best sellers, destinasi, testimoni, blog
- **Tours** — filter (destinasi, kategori, durasi, harga), sorting, pagination
- **Tour Detail** — itinerary, includes/excludes, form booking → generate WA message
- **Cars** — filter (tipe, kapasitas, transmisi, harga), Traveloka-style
- **Car Detail** — pilihan dengan/tanpa sopir, calc harga real-time by tanggal
- **Hotel** — CTA button + form request → dilanjutkan chat admin via WhatsApp
- **Blog** — kategori, search, artikel populer
- **Testimoni** — display + form submission (user dapat submit, admin approve)
- **Custom Trip** — form request paket khusus
- **About** — visi, misi, nilai-nilai, statistik
- **Contact** — info kontak + form pesan
- Floating WhatsApp button di semua halaman

### Admin Panel
- **Dashboard** — statistik + booking terbaru + testimoni pending
- **Tours CRUD** — full featured (image + gallery, itinerary editor, promo, best seller)
- **Tour Bookings** — status management, filter, WhatsApp reply
- **Cars CRUD** — brand, tahun, tipe, kapasitas, harga sopir/self-drive
- **Car Bookings** — same as tour bookings
- **Blog** — WYSIWYG-ready textarea, publish/draft toggle
- **Testimonials** — approve pending submissions, edit, delete
- **Destinations CRUD**
- **Hotel Requests** — kelola booking hotel dari user
- **Custom Trip Requests**
- **Messages** — pesan dari contact form (badge notif)
- **Settings** — identitas, kontak, hero, tentang, SEO

## Fitur Teknis
- **Alur booking**: form → simpan ke DB → generate kode + pesan WhatsApp otomatis
- **Testimoni user-submitted** dengan moderasi (is_active=0 default)
- **Placeholder SVG** untuk foto yang belum diupload — tampilan tetap profesional
- **Responsive mobile-first**
- **Notifikasi badge** di sidebar admin untuk item pending

## Struktur Folder
```
dna-vacation/
├── admin/                    # Admin panel
│   ├── includes/            # Header & footer admin
│   ├── index.php            # Dashboard
│   ├── tours.php, tour-form.php
│   ├── bookings.php
│   ├── cars.php, car-form.php
│   ├── car-bookings.php
│   ├── blog.php, blog-form.php
│   ├── testimonials.php
│   ├── destinations.php
│   ├── hotel-requests.php
│   ├── custom-trips.php
│   ├── messages.php
│   ├── settings.php
│   ├── login.php, logout.php
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   └── img/
├── includes/
│   ├── config.php           # Konfigurasi + helpers
│   ├── header.php, footer.php
├── uploads/                  # File upload user (tours/cars/blog/testimonials/destinations)
├── database.sql              # Schema + sample data
├── index.php                 # Homepage
├── tours.php, tour-detail.php
├── cars.php, car-detail.php
├── hotel.php
├── testimonials.php          # Public + form submission
├── blog.php, blog-detail.php
├── about.php, contact.php
├── custom-trip.php
```

## Deploy ke Shared Hosting
1. Upload via FTP/File Manager ke `public_html/`
2. Buat database MySQL di cPanel
3. Import `database.sql`
4. Update `includes/config.php` (kredensial DB + `BASE_URL`)
5. Pastikan folder `uploads/` writable (chmod 755)
