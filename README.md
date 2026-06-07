# DNA Vacation - Website Travel & Tour Booking

Website lengkap untuk DNA Vacation: paket tour, rental mobil, booking hotel via WhatsApp, blog, dan admin panel.

## Cara Install & Setup

### 1. Persiapan
- Pastikan **XAMPP** terinstall dan **Apache + MySQL** sudah dijalankan.
- Letakkan folder ini di `C:\xampp\htdocs\dnavacation\`.

### 2. Jalankan Setup Database
Buka browser:

```
http://localhost/dnavacation/setup.php
```

Setup akan otomatis membuat:
- Database `dna`
- Semua tabel (tour, rental, blog, booking, dst)
- Sample data
- Admin default

### 3. Login Admin

```
http://localhost/dnavacation/admin/login.php
```

Default credential:
- **Username**: `admin`
- **Password**: `admin123`

### 4. Hapus `setup.php`
Setelah setup berhasil, **hapus file `setup.php`** untuk keamanan.

## Struktur Halaman

### Frontend (Public)
| URL | Fungsi |
|-----|--------|
| `index.php` | Beranda dengan hero, paket populer, testimoni |
| `tour.php` | Daftar paket tour dengan filter |
| `tour-detail.php?id=` | Detail paket + form booking + WhatsApp |
| `rental.php` | Daftar mobil rental dengan filter |
| `rental-detail.php?id=` | Detail mobil + form sewa + WhatsApp |
| `hotel.php` | Landing page hotel + request form |
| `blog.php` | Daftar artikel blog |
| `blog-detail.php?id=` | Detail artikel |
| `about.php` | Tentang DNA Vacation |
| `kontak.php` | Form kontak ke WhatsApp |

### Admin Panel
| URL | Fungsi |
|-----|--------|
| `admin/dashboard.php` | Statistik & overview |
| `admin/tour-admin.php` | CRUD paket tour |
| `admin/rental-admin.php` | CRUD rental mobil |
| `admin/blog-admin.php` | CRUD artikel blog |
| `admin/testimoni-admin.php` | CRUD testimoni |
| `admin/booking-tour.php` | Kelola booking tour |
| `admin/booking-rental.php` | Kelola booking rental |
| `admin/hotel-request.php` | Kelola request hotel |
| `admin/settings.php` | Setting kontak, WhatsApp, hero |

## Fitur Utama

- Form booking dengan auto-redirect ke WhatsApp + template pesan
- Filter & search tour/rental berdasarkan harga, lokasi, kategori, durasi
- Setting nomor WhatsApp & teks website dapat diubah dari admin
- Floating WhatsApp button di setiap halaman
- Mobile responsive
- Upload gambar (tour, rental, blog)
- SEO meta title & description per artikel
- Status booking (baru, diproses, terkonfirmasi, dst)

## Cara Edit Database Manual
Jika ingin akses database via phpMyAdmin: `http://localhost/phpmyadmin` → pilih database `dna`.

## Update Nomor WhatsApp
Login ke admin → menu **Setting Website** → ubah field "Nomor WhatsApp" → Simpan.
