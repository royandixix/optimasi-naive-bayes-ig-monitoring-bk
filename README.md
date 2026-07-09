# Optimasi Algoritma Naive Bayes Menggunakan Information Gain untuk Klasifikasi Perilaku Siswa dalam Sistem Monitoring Bimbingan Konseling di SMP Frater Makassar

## Deskripsi Project

Project ini merupakan sistem monitoring Bimbingan Konseling berbasis web yang digunakan untuk membantu proses pencatatan, pemantauan, penanganan, dan klasifikasi perilaku siswa di lingkungan sekolah.

Sistem ini dikembangkan menggunakan Laravel dan Filament sebagai panel administrasi. Fokus utama sistem adalah penerapan algoritma Naive Bayes untuk klasifikasi perilaku siswa, kemudian dioptimasi menggunakan metode Information Gain sebagai seleksi fitur.

Hasil klasifikasi siswa dibagi menjadi tiga kategori utama:

- Baik
- Perlu Pembinaan
- Bermasalah

Sistem ini dirancang agar Guru BK dapat memantau data siswa, data pelanggaran, proses penanganan, hasil klasifikasi, serta evaluasi performa model secara lebih terstruktur dan mudah dipahami.

---

## Judul Penelitian

**Optimasi Algoritma Naive Bayes Menggunakan Information Gain untuk Klasifikasi Perilaku Siswa dalam Sistem Monitoring Bimbingan Konseling di SMP Frater Makassar**

---

## Tujuan Sistem

Tujuan dari sistem ini adalah:

1. Membantu Guru BK dalam melakukan monitoring perilaku siswa secara digital.
2. Mengelola data siswa, kelas, jenis pelanggaran, pelanggaran, dan penanganan.
3. Mengklasifikasikan perilaku siswa menggunakan algoritma Naive Bayes.
4. Mengoptimasi proses klasifikasi menggunakan Information Gain sebagai metode seleksi fitur.
5. Menampilkan hasil klasifikasi perilaku siswa ke dalam kategori Baik, Perlu Pembinaan, dan Bermasalah.
6. Menampilkan evaluasi model seperti akurasi, precision, recall, dan F1-score.
7. Memberikan dashboard monitoring yang dapat digunakan Guru BK dan Kepala Sekolah.

---

## Teknologi yang Digunakan

Project ini menggunakan teknologi berikut:

| Teknologi | Fungsi |
|---|---|
| Laravel | Framework utama backend |
| Filament | Admin panel dan manajemen data |
| MySQL | Database sistem |
| PHP | Bahasa pemrograman backend |
| Python | Engine proses algoritma Naive Bayes dan Information Gain |
| Livewire | Interaksi komponen Filament |
| Chart Widget Filament | Visualisasi data klasifikasi dan evaluasi model |

---

## Algoritma yang Digunakan

### 1. Naive Bayes

Naive Bayes digunakan untuk melakukan klasifikasi perilaku siswa berdasarkan data pelanggaran dan atribut pendukung lainnya.

Algoritma ini menghitung probabilitas setiap kategori berdasarkan data training, kemudian menentukan kategori dengan probabilitas tertinggi sebagai hasil klasifikasi.

Kategori hasil klasifikasi:

- Baik
- Perlu Pembinaan
- Bermasalah

### 2. Information Gain

Information Gain digunakan untuk mengukur tingkat kepentingan setiap fitur terhadap hasil klasifikasi.

Fitur dengan nilai gain yang lebih tinggi dianggap lebih berpengaruh dalam proses klasifikasi. Dengan Information Gain, sistem dapat memilih fitur yang paling relevan sehingga proses klasifikasi menjadi lebih optimal.

### 3. Naive Bayes + Information Gain

Metode ini merupakan hasil optimasi dari Naive Bayes. Information Gain digunakan terlebih dahulu untuk memilih fitur terbaik, kemudian fitur tersebut digunakan dalam proses klasifikasi Naive Bayes.

Tujuannya adalah meningkatkan kualitas hasil klasifikasi dan evaluasi model.

---

## Fitur Sistem

### 1. Dashboard Monitoring

Dashboard digunakan untuk menampilkan ringkasan informasi penting seperti:

- Total data klasifikasi
- Jumlah siswa kategori Baik
- Jumlah siswa kategori Perlu Pembinaan
- Jumlah siswa kategori Bermasalah
- Akurasi model Naive Bayes + Information Gain
- Jumlah fitur terpilih berdasarkan Information Gain
- Grafik distribusi hasil klasifikasi
- Grafik perbandingan Naive Bayes dan Naive Bayes + Information Gain
- Grafik ranking Information Gain
- Grafik evaluasi performa model
- Tabel hasil klasifikasi terbaru

---

### 2. Manajemen User

Fitur ini digunakan untuk mengelola akun pengguna sistem.

Role yang digunakan:

| Role Sistem | Nama Pengguna |
|---|---|
| super_admin | Guru BK |
| admin | OSIS |
| kepala_sekolah | Kepala Sekolah |
| wali_murid | Wali Murid |

---

### 3. Data Kelas

Fitur ini digunakan untuk mengelola data kelas siswa.

Data yang dikelola meliputi:

- Kode kelas
- Nama kelas
- Wali kelas
- Tahun ajaran

---

### 4. Data Siswa

Fitur ini digunakan untuk mengelola data siswa.

Data yang dikelola meliputi:

- NIS
- Nama siswa
- Jenis kelamin
- Kelas
- Tempat lahir
- Tanggal lahir
- Alamat
- Nama ayah
- Nama ibu
- Nomor HP orang tua
- Status siswa

---

### 5. Jenis Pelanggaran

Fitur ini digunakan untuk mengelola jenis pelanggaran yang dapat dilakukan siswa.

Data yang dikelola meliputi:

- Kode jenis pelanggaran
- Nama jenis pelanggaran
- Aspek pelanggaran
- Tingkat pelanggaran
- Poin pelanggaran
- Keterangan

Aspek pelanggaran terdiri dari:

- Kelakuan
- Kerajinan
- Kerapian
- Kehadiran
- Lainnya

Tingkat pelanggaran terdiri dari:

- Ringan
- Sedang
- Berat

---

### 6. Data Pelanggaran

Fitur ini digunakan untuk mencatat pelanggaran yang dilakukan oleh siswa.

Data yang dikelola meliputi:

- Nama siswa
- Jenis pelanggaran
- Tanggal pelanggaran
- Semester
- Tahun ajaran
- Keterangan

---

### 7. Data Penanganan

Fitur ini digunakan untuk mencatat tindak lanjut atau penanganan dari pelanggaran siswa.

Data yang dikelola meliputi:

- Data pelanggaran
- Tanggal penanganan
- Tindakan
- Catatan penanganan
- User yang menangani

---

### 8. Klasifikasi Naive Bayes

Fitur ini digunakan untuk memproses klasifikasi perilaku siswa menggunakan algoritma Naive Bayes dan Naive Bayes + Information Gain.

Data yang diproses berasal dari data siswa dan data pelanggaran.

Hasil yang ditampilkan meliputi:

- Jumlah pelanggaran
- Total poin pelanggaran
- Label aktual
- Hasil Naive Bayes
- Probabilitas Naive Bayes
- Hasil Naive Bayes + Information Gain
- Probabilitas Naive Bayes + Information Gain
- Fitur klasifikasi
- Detail Information Gain

---

### 9. Evaluasi Model

Fitur ini digunakan untuk melihat hasil evaluasi performa algoritma.

Metrik evaluasi yang digunakan:

- Akurasi
- Precision
- Recall
- F1-score
- Confusion matrix

Evaluasi dilakukan untuk membandingkan performa:

- Naive Bayes
- Naive Bayes + Information Gain

---

### 10. Ranking Information Gain

Fitur ini digunakan untuk melihat daftar fitur yang paling berpengaruh terhadap proses klasifikasi.

Data yang ditampilkan:

- Nama fitur
- Nilai gain
- Entropy before
- Entropy after
- Status fitur terpilih
- Ranking fitur
- Jumlah data

---

## Hak Akses Role

### 1. Guru BK

Role sistem:

```text
super_admin
```

Akses:

- Mengakses seluruh menu sistem
- Mengelola user
- Mengelola data kelas
- Mengelola data siswa
- Mengelola jenis pelanggaran
- Mengelola data pelanggaran
- Mengelola data penanganan
- Memproses klasifikasi Naive Bayes
- Memproses klasifikasi Naive Bayes + Information Gain
- Melihat evaluasi model
- Melihat dashboard monitoring
- Melihat ranking Information Gain

---

### 2. OSIS

Role sistem:

```text
admin
```

Akses:

- Melihat data siswa
- Menambahkan data pelanggaran
- Mengedit data pelanggaran
- Tidak dapat menghapus data pelanggaran
- Tidak dapat mengakses manajemen user
- Tidak dapat mengakses klasifikasi
- Tidak dapat mengakses evaluasi model

---

### 3. Kepala Sekolah

Role sistem:

```text
kepala_sekolah
```

Akses:

- Melihat dashboard monitoring
- Melihat data siswa
- Melihat data pelanggaran
- Melihat data penanganan
- Melihat hasil klasifikasi
- Melihat evaluasi model
- Melihat ranking Information Gain
- Tidak dapat menambah data
- Tidak dapat mengedit data
- Tidak dapat menghapus data

---

### 4. Wali Murid

Role sistem:

```text
wali_murid
```

Akses:

- Login ke sistem
- Akses data akan dibatasi hanya untuk data anak sendiri
- Tidak dapat melihat seluruh data siswa
- Tidak dapat mengelola data sistem

Catatan:

Role Wali Murid perlu menggunakan relasi khusus ke data siswa agar hanya dapat melihat data anaknya sendiri. Relasi ini dapat dikembangkan melalui kolom `siswa_id` pada tabel `users`.

---

## Alur Sistem

Alur penggunaan sistem secara umum:

1. Guru BK mengelola data master seperti kelas, siswa, dan jenis pelanggaran.
2. OSIS atau Guru BK mencatat data pelanggaran siswa.
3. Guru BK mencatat proses penanganan terhadap siswa.
4. Guru BK menjalankan proses klasifikasi Naive Bayes + Information Gain.
5. Sistem menghitung ranking fitur menggunakan Information Gain.
6. Sistem melakukan klasifikasi perilaku siswa menggunakan Naive Bayes.
7. Sistem menyimpan hasil klasifikasi ke database.
8. Sistem menampilkan evaluasi model berupa akurasi, precision, recall, dan F1-score.
9. Kepala Sekolah dapat melihat hasil monitoring melalui dashboard.
10. Wali Murid dapat dikembangkan untuk melihat data anaknya sendiri.

---

## Struktur Folder Penting

```text
app/
├── Filament/
│   ├── Resources/
│   │   ├── Users/
│   │   ├── Siswas/
│   │   ├── Kelas/
│   │   ├── JenisPelanggarans/
│   │   ├── Pelanggarans/
│   │   ├── Penanganans/
│   │   └── Klasifikasis/
│   └── Widgets/
│       ├── KlasifikasiStatsOverview.php
│       ├── KlasifikasiHasilChart.php
│       ├── KlasifikasiComparisonChart.php
│       ├── InformationGainRankingChart.php
│       ├── EvaluasiModelChart.php
│       └── LatestKlasifikasiTable.php
│
├── Models/
│   ├── User.php
│   ├── Siswa.php
│   ├── Kelas.php
│   ├── JenisPelanggaran.php
│   ├── Pelanggaran.php
│   ├── Penanganan.php
│   ├── Klasifikasi.php
│   ├── EvaluasiModel.php
│   └── InformationGainResult.php
│
├── Services/
│   └── PythonNaiveBayesInformationGainService.php
│
database/
├── migrations/
│
python/
└── naive_bayes_ig.py
```

---

## Instalasi Project

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/optimasi-naive-bayes-ig-monitoring-bk.git
cd optimasi-naive-bayes-ig-monitoring-bk
```

Ganti `USERNAME` dengan username GitHub pemilik repository.

---

### 2. Install Dependency PHP

```bash
composer install
```

---

### 3. Install Dependency Frontend

```bash
npm install
```

---

### 4. Buat File Environment

```bash
cp .env.example .env
```

---

### 5. Generate Application Key

```bash
php artisan key:generate
```

---

### 6. Konfigurasi Database

Buka file:

```text
.env
```

Sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_monitoring_bk
DB_USERNAME=root
DB_PASSWORD=
```

Nama database dapat disesuaikan dengan database lokal masing-masing.

---

### 7. Konfigurasi Python

Pastikan Python 3 sudah terinstall.

Cek versi Python:

```bash
python3 --version
```

Tambahkan konfigurasi berikut pada file `.env`:

```env
PYTHON_BIN=python3
```

Konfigurasi ini digunakan agar Laravel dapat menjalankan file Python untuk proses algoritma Naive Bayes dan Information Gain.

---

### 8. Jalankan Migration

```bash
php artisan migrate
```

---

### 9. Jalankan Storage Link

```bash
php artisan storage:link
```

---

### 10. Jalankan Server Laravel

```bash
php artisan serve
```

Atau gunakan port khusus:

```bash
php artisan serve --port=8001
```

Buka aplikasi melalui browser:

```text
http://127.0.0.1:8001/admin
```

---

### 11. Jalankan Vite

Pada terminal lain, jalankan:

```bash
npm run dev
```

---

## Membuat Akun Pertama

Jika belum ada akun untuk login, buat akun melalui Tinker:

```bash
php artisan tinker
```

Lalu jalankan:

```php
App\Models\User::create([
    'name' => 'Administrator',
    'email' => 'admin@gmail.com',
    'password' => bcrypt('password'),
    'role' => 'super_admin',
]);
```

Login menggunakan:

```text
Email: admin@gmail.com
Password: password
```

Setelah berhasil login, password dapat diganti melalui fitur manajemen user.

---

## Contoh Akun Berdasarkan Role

### Guru BK

```text
Nama: Administrator
Email: admin@gmail.com
Password: password
Role: super_admin
```

### OSIS

```text
Nama: Andi Pratama
Email: osis@smpfrater.sch.id
Password: Password123!
Role: admin
```

### Kepala Sekolah

```text
Nama: Drs. Budi Santoso
Email: kepala@smpfrater.sch.id
Password: Password123!
Role: kepala_sekolah
```

### Wali Murid

```text
Nama: Siti Rahmawati
Email: wali.siti@example.com
Password: Password123!
Role: wali_murid
```

---

## Perintah Penting Laravel

Membersihkan cache:

```bash
php artisan optimize:clear
```

Menjalankan migration:

```bash
php artisan migrate
```

Melihat status migration:

```bash
php artisan migrate:status
```

Menjalankan server:

```bash
php artisan serve --port=8001
```

Membuka Tinker:

```bash
php artisan tinker
```

Menjalankan composer autoload:

```bash
composer dump-autoload
```

---

## Alur Proses Algoritma

Alur proses algoritma pada sistem:

1. Laravel mengambil data siswa dan data pelanggaran dari database.
2. Data diubah menjadi fitur klasifikasi.
3. Data dikirim ke file Python.
4. Python menghitung nilai Information Gain setiap fitur.
5. Python memilih fitur yang relevan berdasarkan nilai gain.
6. Python menjalankan proses klasifikasi Naive Bayes.
7. Python menghitung hasil evaluasi model.
8. Laravel menerima hasil dari Python dalam format JSON.
9. Laravel menyimpan hasil klasifikasi ke database.
10. Filament menampilkan hasil klasifikasi, evaluasi model, dan ranking Information Gain di dashboard.

---

## Fitur Klasifikasi yang Digunakan

Contoh fitur yang digunakan dalam proses klasifikasi:

- Jumlah pelanggaran kategori
- Total poin kategori
- Kelakuan kategori
- Kerajinan kategori
- Kerapian kategori
- Kehadiran kategori
- Lainnya kategori
- Tingkat pelanggaran dominan
- Semester terakhir

Fitur tersebut digunakan untuk menentukan kategori perilaku siswa.

---

## Output Sistem

Output utama dari sistem:

1. Data siswa yang sudah terklasifikasi.
2. Hasil klasifikasi Naive Bayes.
3. Hasil klasifikasi Naive Bayes + Information Gain.
4. Nilai probabilitas klasifikasi.
5. Ranking fitur berdasarkan Information Gain.
6. Nilai akurasi model.
7. Nilai precision.
8. Nilai recall.
9. Nilai F1-score.
10. Dashboard monitoring perilaku siswa.

---

## Keamanan Data

Beberapa hal penting terkait keamanan data:

1. File `.env` tidak boleh diunggah ke GitHub.
2. Password user harus disimpan dalam bentuk hash.
3. Akses menu dibatasi berdasarkan role.
4. Wali Murid tidak boleh melihat seluruh data siswa.
5. Kepala Sekolah hanya diberi akses monitoring dan laporan.
6. OSIS hanya diberi akses terbatas untuk membantu input pelanggaran.
7. Guru BK memiliki akses penuh sebagai pengelola utama sistem.

---

## File yang Tidak Boleh Diupload ke GitHub

Pastikan file berikut masuk ke `.gitignore`:

```text
.env
/vendor
/node_modules
/storage/*.key
```

File `.env` tidak boleh dipublikasikan karena berisi konfigurasi penting seperti database, APP_KEY, dan konfigurasi lokal.

---

## Git Command

Jika repository belum pernah dipush ke GitHub:

```bash
git init
git add .
git commit -m "Initial commit sistem monitoring BK"
git branch -M main
git remote add origin https://github.com/USERNAME/optimasi-naive-bayes-ig-monitoring-bk.git
git push -u origin main
```

Jika remote sudah ada:

```bash
git remote -v
git remote set-url origin https://github.com/USERNAME/optimasi-naive-bayes-ig-monitoring-bk.git
git push -u origin main
```

Ganti `USERNAME` dengan username GitHub masing-masing.

---

## Catatan Pengembangan

Beberapa fitur yang dapat dikembangkan selanjutnya:

1. Relasi khusus antara Wali Murid dan Siswa menggunakan `siswa_id`.
2. Halaman khusus Wali Murid untuk melihat data anak sendiri.
3. Export laporan ke PDF atau Excel.
4. Filter laporan berdasarkan kelas, semester, dan tahun ajaran.
5. Grafik tren pelanggaran siswa per bulan.
6. Validasi data training dan testing.
7. Perbandingan algoritma lain dengan Naive Bayes.
8. Fitur notifikasi untuk Guru BK dan Wali Murid.

---

## Kesimpulan

Sistem ini dibuat untuk membantu proses monitoring Bimbingan Konseling secara digital dengan menerapkan algoritma Naive Bayes dan Information Gain.

Laravel dan Filament digunakan sebagai platform pengembangan web, sedangkan Python digunakan sebagai engine untuk proses klasifikasi dan evaluasi algoritma.

Dengan adanya sistem ini, Guru BK dapat lebih mudah melakukan pemantauan perilaku siswa, mencatat pelanggaran, melakukan penanganan, serta melihat hasil klasifikasi siswa secara lebih cepat, terstruktur, dan informatif.

---

## Lisensi

Project ini dibuat untuk kebutuhan penelitian, pembelajaran, dan pengembangan sistem monitoring Bimbingan Konseling.
