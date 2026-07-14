# Mantri Tani — Laravel

Toko online pertanian **Mantri Tani Selorejo** (skripsi). Template dipecah per tema/role.

## Struktur View

```
resources/views/
├── layouts/
│   ├── partials/head.blade.php      # CSS, Tailwind, font
│   ├── mobile/
│   │   ├── app.blade.php            # Layout mobile + bottom nav
│   │   ├── home.blade.php           # Beranda (hero header)
│   │   └── profile.blade.php        # Profil (hero profil)
│   ├── admin/app.blade.php          # Sidebar admin hijau
│   └── owner/app.blade.php          # Sidebar owner gelap
├── components/mobile/
│   ├── bottom-nav.blade.php
│   ├── hero-header.blade.php
│   ├── product-card.blade.php
│   ├── cart-footer.blade.php
│   └── profile-hero.blade.php
├── mobile/          # Konsumen (mobile)
├── admin/           # Admin panel (desktop)
└── owner/           # Owner panel (desktop)
```

## Routes

| URL | Halaman |
|-----|---------|
| `/` | Hub navigasi |
| `/toko/produk` | Beranda mobile |
| `/toko/produk/{slug}` | Detail produk |
| `/toko/kategori` | Kategori |
| `/toko/keranjang` | Keranjang |
| `/toko/checkout` | Checkout |
| `/toko/pembayaran` | Midtrans Snap |
| `/admin/pengaturan/midtrans` | Pengaturan Midtrans |
| `/toko/profil` | Profil |
| `/admin/login` | Login admin/owner |
| `/admin/dashboard` | Dashboard admin |
| `/owner/dashboard` | Dashboard owner |

## Diagram (PlantUML)

| File | Isi |
|------|-----|
| [`planning/ERD.md`](ERD.md) | Dokumentasi database |
| [`planning/ERD.puml`](ERD.puml) | ERD + sequence + state |
| [`planning/ERD.plantuml.md`](ERD.plantuml.md) | Panduan ERD |
| [`planning/FLOWCHART.puml`](FLOWCHART.puml) | **Flowchart alur bisnis** |
| [`planning/FLOWCHART.plantuml.md`](FLOWCHART.plantuml.md) | Panduan flowchart |

## Menjalankan

```bash
cd mantri-tani
php artisan serve
```

Buka: http://127.0.0.1:8000

## Regenerate views

Setelah edit template HTML di folder `template/`:

```bash
python build_pro_template.py      # HTML statis
python build_laravel.py           # layouts & components
python build_laravel_part2.py     # views, controllers, routes
```

CSS: `public/css/custom.css` (disinkron dari `template/css/custom.css`)
