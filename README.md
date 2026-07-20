# Smart Calorie Tracker

**Smart Calorie Tracker** adalah aplikasi web modern yang membantu pengguna mencatat konsumsi makanan harian, menghitung kalori dan protein, serta memantau berat badan (Fat Loss Tracker). Aplikasi ini menggunakan fitur **AI Food Scanner** untuk mendeteksi makanan secara otomatis dari foto kamera atau unggahan berkas.

Aplikasi ini dibangun menggunakan arsitektur **decoupled** yang memisahkan antara Web Application (Laravel 12) dan AI Server (FastAPI). Laravel murni bertindak sebagai klien REST API dan tidak menjalankan model AI secara langsung.

---

## Prasyarat (Prerequisites)

Pastikan sistem Anda sudah terpasang:

-   **PHP 8.4+**
-   **Composer**
-   **Node.js & npm** (untuk aset Tailwind CSS v4)
-   **Python 3.13+** (untuk AI Server)
-   **MySQL Database Server**

---

## Prosedur Instalasi & Menjalankan Aplikasi

Setelah melakukan `git clone` dari repositori GitHub, ikuti langkah-langkah setup di bawah ini:

### Bagian 1: Konfigurasi Web Application (Laravel 12)

1.  **Masuk ke direktori utama proyek:**

    ```bash
    cd smart-calorie-tracker
    ```

2.  **Instal dependensi PHP:**

    ```bash
    composer install
    ```

3.  **Salin berkas konfigurasi lingkungan (.env):**

    ```bash
    copy .env.example .env
    ```

4.  **Buat Application Key:**

    ```bash
    php artisan key:generate
    ```

5.  **Konfigurasi Database di berkas `.env`:**
    Buka berkas `.env` Anda dan sesuaikan konfigurasi koneksi database MySQL:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=smart_calorie_tracker
    DB_USERNAME=root
    DB_PASSWORD=
    ```

    _Pastikan database dengan nama `smart_calorie_tracker` sudah dibuat di MySQL Anda._

6.  **Jalankan migrasi database dan pengisian data awal (seeding):**

    ```bash
    php artisan migrate:fresh --seed
    ```

7.  **Instal dependensi JavaScript & bangun aset CSS:**

    ```bash
    npm install
    npm run build
    ```

8.  **Hubungkan storage link untuk penyimpanan berkas avatar/gambar:**
    ```bash
    php artisan storage:link
    ```

---

### Bagian 2: Konfigurasi AI Server (FastAPI)

1.  **Masuk ke direktori `FastAPI`:**

    ```bash
    cd FastAPI
    ```

2.  **Buat Virtual Environment Python:**

    ```bash
    python -m venv venv
    ```

3.  **Aktifkan Virtual Environment:**

    -   **Windows (PowerShell):**
        ```powershell
        .\venv\Scripts\Activate.ps1
        ```
    -   **Windows (CMD):**
        ```cmd
        .\venv\Scripts\activate.bat
        ```
    -   **Linux / macOS:**
        ```bash
        source venv/bin/activate
        ```

4.  **Instal dependensi Python:**
    ```bash
    pip install -r requirements.txt
    ```

---

## Menjalankan Aplikasi

Aplikasi harus dijalankan secara bersamaan menggunakan dua terminal terpisah:

### 1. Jalankan AI Server (FastAPI)

Buka terminal baru, masuk ke direktori `FastAPI`, aktifkan `venv`, lalu jalankan Uvicorn:

```bash
cd FastAPI
# Aktifkan venv Anda terlebih dahulu (sesuai OS di atas)
uvicorn main:app --reload --port 8080
```

AI Server akan berjalan di `http://127.0.0.1:8080`. Anda bisa mengakses Swagger API Docs di `http://127.0.0.1:8080/docs`.

### 2. Jalankan Web Server Laravel

Buka terminal lainnya di direktori utama `smart-calorie-tracker`:

```bash
php artisan serve
```

Aplikasi web dapat diakses di browser melalui tautan: **`http://127.0.0.1:8000`**

---

## Akun Demo (Default Credentials)

Database Anda telah otomatis diisi dengan akun percobaan berikut setelah menjalankan perintah seed:

-   **Akun Demo User (Akses Jurnal & Tracker):**
    -   **Email:** `demo@smartcalorietracker.com`
    -   **Password:** `demo123`
-   **Akun Administrator (Akses Admin Panel & Master Makanan):**
    -   **Email:** `admin@smartcalorietracker.com`
    -   **Password:** `admin123`

---

## Menjalankan Pengujian (Testing)

Untuk memastikan seluruh sistem berjalan dengan benar (termasuk verifikasi koneksi HTTP ke AI Server), Anda dapat menjalankan pengujian otomatis di direktori utama Laravel:

```bash
php artisan test
```

_(Pastikan AI Server sudah berjalan pada port 8001 sebelum menjalankan pengujian)._
