# QR Portal (PDF/Link to QR)

AdminLTE-powered web app to generate QR codes from uploaded PDFs or direct links. Users can upload, convert, download, and revisit previous QR entries through a simple dashboard workflow.

---

## Overview

This project is built as a lightweight PHP dashboard that lets users:
- Upload a PDF and instantly get a shareable QR.
- Paste a URL and convert it to QR without uploading files.
- View history of generated items (with stored links and filenames).
- Download the rendered QR code as an image.
- Stay authenticated via the existing login/register flow before using the tool.

---

## Tech Stack

- PHP 8+ (server-side)
- MySQL/MariaDB (persistent storage)
- AdminLTE + Bootstrap 5 (UI shell)
- Font Awesome (icons)
- qrcode.js (client-side QR rendering)

---

## Features

- Dual input modes: PDF upload or direct URL.
- Drag-and-drop upload with filename preview.
- AdminLTE-styled cards, hero, and stats for clarity.
- Instant QR generation + download button.
- History page to review past uploads/links.

---

## Project Structure (key files)

```
qr-portal/
├── upload.php          # Main upload + QR generation screen
├── history.php         # History list of generated QRs
├── includes/           # Auth helpers, layout header/footer, sidebar
├── config/db.php       # Database connection bootstrap
├── assets/js/qrcode.min.js  # Client-side QR generator
├── uploads/            # Stored PDF files (per upload flow)
└── vendor/             # Composer dependencies (if any)
```

---

## Run Locally (XAMPP/LAMP)

1. Clone or copy the project into your web root (e.g., `htdocs/qr-portal`).
2. Create a MySQL database and import your schema (match tables used in `uploads`, users, etc.).
3. Update database credentials in `config/db.php`.
4. Ensure PHP file uploads are enabled and `uploads/` is writable by the web server.
5. Start Apache/MySQL, then open `http://localhost/qr-portal/upload.php`.
6. Register/login, choose PDF or URL, generate QR, and download.

---

##  Contributing

Contributions, ideas, and forks are welcome! Please open an issue to discuss major changes or submit a PR for enhancements, bug fixes, or documentation improvements.

---

##  About the Developer

Hi, I'm **Harsh Pandey** – a self-driven web developer building practical side projects while working full-time. I share my learning journey and ship products like Bike Expense Tracker to help everyday users stay organised.

---

## Connect With Me

-  Instagram: [@sasta_developer0143](https://www.instagram.com/sasta_developer0143)
-  Personal Instagram: [@lucifer__1430](https://www.instagram.com/lucifer__1430)
-  Portfolio: [Harsh Pandey](https://lucifer01430.github.io/Portfolio/)
-  Email: harshpandeylucifer@gmail.com

---

> ⭐ **If this project helps you, please consider starring the repository!**

## Notes

- Designed for offline-friendly QR rendering (qrcode.js runs in-browser).
- Keep PDFs within allowed size limits configured in PHP (post_max_size, upload_max_filesize).
- For production, enforce TLS and validate user inputs/server file permissions. 
