# UI Implementation Rules

1. Jangan pakai gambar PNG sebagai full-page background untuk menggantikan UI.
2. Pakai PNG mockup hanya sebagai referensi visual.
3. Pakai SVG assets untuk logo, icon, dan ilustrasi kecil.
4. Buat reusable Blade components.
5. Semua halaman app memakai sidebar dan header yang sama.
6. Gunakan card radius 22px atau 24px.
7. Teks utama `#0F172A`, teks sekunder `#64748B`.
8. Primary button memakai gradient/green `#10B981` ke `#059669`.
9. Badge income hijau, expense merah, pending amber, success green, cancel rose.
10. Untuk interaksi ringan pakai Alpine.js: dropdown, drawer, modal, tab, preview upload.
11. Chart pakai Chart.js; jangan SVG statis untuk chart final.
12. Data di dashboard harus berasal dari DB setelah Phase 4, bukan hardcoded.
