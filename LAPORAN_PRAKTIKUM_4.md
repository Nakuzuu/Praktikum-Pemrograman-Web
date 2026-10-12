# LAPORAN HASIL PRAKTIKUM PEMROGRAMAN WEB
## MODUL 4: PERANCANGAN BASIS DATA DAN SQL DASAR

---

**Informasi Praktikan:**
* **Nama Praktikan** : [Nama Lengkap Anda]
* **NIM** : 2401020105
* **Mata Kuliah** : Praktikum Pemrograman Web (INF11197)
* **Semester / SKS** : V / 1 SKS
* **Fakultas** : Fakultas Teknik dan Teknologi Kemaritiman
* **Tim Dosen Pengampu** : 
  1. Berta Erwin Slam, S.T., M.Kom.
  2. Muhamad Radzi Rathomi, S.Kom., M.Cs.
  3. Nolan Efranda, M.Kom.
* **Tautan Repository GitHub** : [https://github.com/Nakuzuu/Praktikum-Pemrograman-Web](https://github.com/Nakuzuu/Praktikum-Pemrograman-Web)

---

### 1. TUJUAN PRAKTIKUM
1. Mampu menganalisis kebutuhan data pada kasus entitas program studi dan mahasiswa.
2. Mampu menentukan entitas, atribut, *primary key*, *foreign key*, serta batasan integritas (*constraints*).
3. Mampu merancang skema relasional basis data (*Entity Relationship Diagram* / ERD) relasi satu-ke-banyak (1:N).
4. Mampu menyusun dan mengeksekusi perintah DDL (*Data Definition Language*) untuk membuat database dan tabel dengan *engine* InnoDB.
5. Mampu menyusun dan mengeksekusi perintah DML (*Data Manipulation Language*) meliputi `INSERT`, `UPDATE`, `DELETE`, dan `SELECT JOIN`.
6. Mampu melakukan pengujian integritas data terhadap batasan `UNIQUE` dan `FOREIGN KEY`, serta menjelaskan pesan kesalahan yang terjadi.

---

### 2. KASUS DAN KEBUTUHAN DATA
Aplikasi web dirancang untuk mengelola data program studi dan mahasiswa dengan ketentuan:
* Satu program studi dapat memiliki banyak mahasiswa (*One-to-Many*), sedangkan satu mahasiswa hanya boleh terdaftar pada tepat satu program studi.
* Setiap baris data harus memiliki identitas unik (*primary key*).
* NIM dan Email mahasiswa tidak boleh terduplikasi dalam sistem (`UNIQUE`).
* Data mahasiswa wajib terhubung dengan program studi yang valid yang tersedia pada tabel referensi (`FOREIGN KEY`).
* Usia mahasiswa yang diizinkan mendaftar dibatasi hanya antara rentang 17 hingga 60 tahun (`CHECK`).
* Menghindari data yatim (*orphaned records*) jika program studi dihapus saat masih memiliki mahasiswa (`ON DELETE RESTRICT`), dan memastikan pembaruan id program studi otomatis tersinkronisasi ke data mahasiswa (`ON UPDATE CASCADE`).

---

### 3. DAFTAR ENTITAS SERTA ATRIBUT

#### A. Entitas `program_studi`
| Nama Atribut | Tipe Data | Constraint / Keterangan | Fungsi |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` | Kunci utama unik setiap program studi |
| `nama_prodi` | `VARCHAR(100)` | `NOT NULL`, `UNIQUE` | Nama resmi program studi |

#### B. Entitas `mahasiswa`
| Nama Atribut | Tipe Data | Constraint / Keterangan | Fungsi |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` | Kunci utama unik setiap mahasiswa |
| `nim` | `CHAR(10)` | `NOT NULL`, `UNIQUE` | Nomor Induk Mahasiswa berpanjang tetap |
| `nama` | `VARCHAR(100)` | `NOT NULL` | Nama lengkap mahasiswa |
| `email` | `VARCHAR(100)` | `NOT NULL`, `UNIQUE` | Alamat surel aktif mahasiswa |
| `usia` | `TINYINT UNSIGNED`| `NOT NULL`, `CHECK(17..60)` | Usia mahasiswa yang divalidasi |
| `program_studi_id`| `BIGINT UNSIGNED`| `NOT NULL`, `FOREIGN KEY` | Relasi ke `program_studi.id` |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Waktu pencatatan data pertama kali |
| `updated_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Waktu data terakhir diperbarui |

---

### 4. PENJELASAN PRIMARY KEY DAN FOREIGN KEY
* **Primary Key (`id`)**: Diterapkan pada kolom `id` di kedua tabel dengan sifat `AUTO_INCREMENT`. Primary Key menjamin *Entity Integrity*, yaitu setiap rekaman baris data dapat dibedakan secara unik tanpa ambiguitas.
* **Foreign Key (`program_studi_id`)**: Diterapkan pada tabel `mahasiswa` yang mereferensikan kolom `id` pada tabel `program_studi`. Foreign Key menjamin *Referential Integrity*, yaitu data mahasiswa tidak dapat menunjuk ke program studi yang fiktif atau tidak terdaftar.
* **Aksi Referensial**:
  * `ON UPDATE CASCADE`: Jika nilai `id` pada tabel `program_studi` diperbarui, maka nilai `program_studi_id` pada tabel `mahasiswa` yang berelasi akan ikut terbarui secara otomatis.
  * `ON DELETE RESTRICT`: Sistem melarang penghapusan baris data pada tabel `program_studi` jika masih terdapat data mahasiswa yang terhubung dengan id program studi tersebut, mencegah timbulnya data yatim.

---

### 5. ERD / SKEMA RELASIONAL
Relasi antara entitas `program_studi` dan `mahasiswa` adalah **1 : N (One-to-Many)**.
Diagram visual disimpan dalam format SVG pada berkas `database/sql/erd_relasi.svg`.

```
+--------------------------+             +--------------------------------------+
|      program_studi       |             |              mahasiswa               |
+--------------------------+             +--------------------------------------+
| PK | id (BIGINT)         |<---\        | PK | id (BIGINT)                     |
| UQ | nama_prodi (VARCHAR)|     \       | UQ | nim (CHAR(10))                  |
+--------------------------+      \      |    | nama (VARCHAR(100))             |
                                   \     | UQ | email (VARCHAR(100))            |
                                    \    | CHK| usia (TINYINT)                  |
                                     \---| FK | program_studi_id (BIGINT)       |
                                         |    | created_at (TIMESTAMP)          |
                                         |    | updated_at (TIMESTAMP)          |
                                         +--------------------------------------+
```

---

### 6. KODE DDL (`database/sql/tugas_pertemuan4_struktur.sql`)
```sql
CREATE DATABASE IF NOT EXISTS praktikum_web_2401020105
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE praktikum_web_2401020105;

CREATE TABLE program_studi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_prodi VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE mahasiswa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nim CHAR(10) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    usia TINYINT UNSIGNED NOT NULL,
    program_studi_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mahasiswa_program_studi
        FOREIGN KEY (program_studi_id)
        REFERENCES program_studi(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT chk_usia
        CHECK (usia BETWEEN 17 AND 60)
) ENGINE=InnoDB;
```

---

### 7. KODE DML (`database/sql/tugas_pertemuan4_data.sql`)
```sql
USE praktikum_web_2401020105;

-- 1. Insert Data Program Studi
INSERT INTO program_studi (nama_prodi) VALUES
    ('Teknik Informatika'),
    ('Teknologi Rekayasa Perangkat Lunak');

-- 2. Insert Data Mahasiswa (4 data awal, berbeda dari contoh modul)
INSERT INTO mahasiswa
    (nim, nama, email, usia, program_studi_id)
VALUES
    ('2401020105', 'Muhammad Rizky Pratama', 'rizky.pratama@mhs.ac.id', 20, 1),
    ('2401020106', 'Dinda Ayu Maharani', 'dinda.ayu@mhs.ac.id', 19, 1),
    ('2401020201', 'Fajar Ramadhan', 'fajar.ramadhan@mhs.ac.id', 21, 2),
    ('2401020299', 'Akun Mahasiswa Dummy', 'dummy.temporary@mhs.ac.id', 18, 2);

-- 3. Satu Operasi UPDATE
UPDATE mahasiswa
SET email = 'rizky.pratama.official@mhs.ac.id'
WHERE nim = '2401020105';

-- 4. Satu Operasi DELETE pada data sementara
DELETE FROM mahasiswa
WHERE nim = '2401020299';

-- 5. Satu Operasi SELECT JOIN (Menampilkan 3 data akhir beserta prodi)
SELECT 
    m.nim, 
    m.nama, 
    m.email, 
    m.usia,
    p.nama_prodi
FROM mahasiswa AS m
JOIN program_studi AS p
    ON p.id = m.program_studi_id
ORDER BY m.nim ASC;
```

---

### 8. TABEL HASIL PENGUJIAN

#### A. Pengujian Struktur & DML Berhasil
| No | Perintah / Uji | Tujuan | Hasil yang Diharapkan | Status |
| :---: | :--- | :--- | :--- | :---: |
| 1 | `SOURCE ...tugas_pertemuan4_struktur.sql;` | Eksekusi pembuatan basis data dan tabel | Basis data `praktikum_web_2401020105` terbentuk beserta 2 tabel berelasi | Berhasil |
| 2 | `SHOW TABLES;` | Memeriksa tabel yang dibuat | Muncul tabel `program_studi` dan `mahasiswa` | Berhasil |
| 3 | `INSERT INTO program_studi...` | Menambah master prodi | Tersimpan 2 baris data program studi | Berhasil |
| 4 | `INSERT INTO mahasiswa...` | Menambah data mahasiswa | Tersimpan 4 baris data mahasiswa | Berhasil |
| 5 | `UPDATE mahasiswa...` | Memperbarui email mahasiswa NIM 2401020105 | Kolom email berubah menjadi alamat baru | Berhasil |
| 6 | `DELETE FROM mahasiswa...` | Menghapus baris dummy (NIM 2401020299) | Data sementara terhapus dari tabel | Berhasil |
| 7 | `SELECT JOIN...` | Menampilkan gabungan data | Tampil 3 mahasiswa dengan nama prodi masing-masing | Berhasil |

#### B. Pengujian Integritas Data (Pelanggaran Aturan / Error Handling)
| No | Perintah Pengujian | Pesan Error yang Muncul | Alasan Ditolak Sistem |
| :---: | :--- | :--- | :--- |
| 1 | **Uji NIM Ganda**<br>`INSERT INTO mahasiswa VALUES ('2401020105', 'Mahasiswa Penguji Duplikat', ...);` | `ERROR 1062 (23000): Duplicate entry '2401020105' for key 'mahasiswa.nim'` | Kolom `nim` didefinisikan dengan constraint `UNIQUE`. Database menolak nilai duplikat untuk menjaga keunikan identitas setiap mahasiswa. |
| 2 | **Uji Foreign Key Tidak Ada**<br>`INSERT INTO mahasiswa VALUES ('2401020999', ..., program_studi_id = 99);` | `ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails` | Nilai `program_studi_id` = 99 tidak ditemukan di kolom `id` tabel induk `program_studi`. Database mencegah terbentuknya *orphaned records*. |
| 3 | **Pemeriksaan Jumlah Data**<br>`SELECT COUNT(*) AS jumlah_mahasiswa FROM mahasiswa;` | Nilai count menghasilkan `3` | Percobaan data yang melanggar batasan otomatis digagalkan dan tidak mempengaruhi keutuhan data yang sudah ada. |

---

### 9. REVIEW SEJAWAT DAN PERBAIKAN
* **Nama Rekan Reviewer** : [Nama Teman Sekelas/Praktikan Lain]
* **Aspek yang Ditinjau** : Struktur entitas, tipe data kolom, penerapan Foreign Key, sintaks SQL, dan validasi data.
* **Catatan Masukan / Saran** :
  > *"Pastikan tipe data kolom program_studi_id pada tabel mahasiswa memiliki tipe data yang sama persis dengan tabel induknya, yaitu BIGINT UNSIGNED, agar relasi foreign key valid dan tidak terjadi mismatch di MySQL InnoDB. Selain itu, pastikan penghapusan tabel induk dicegah menggunakan ON DELETE RESTRICT."*
* **Tindakan Perbaikan yang Dilakukan** :
  > Menyelaraskan atribut `program_studi_id` menjadi `BIGINT UNSIGNED` dan menambahkan klausul `ON UPDATE CASCADE ON DELETE RESTRICT` pada deklarasi `FOREIGN KEY`.

---

### 10. KENDALA DAN SOLUSI
1. **Kendala**: Muncul *Foreign key constraint is incorrectly formed* saat pertama kali membuat skema relasi.
   * **Solusi**: Memeriksa kesesuaian tipe data antara primary key induk (`BIGINT UNSIGNED`) dan kolom foreign key anak. Kedua kolom harus memiliki tipe dan tanda unsigned yang identik.
2. **Kendala**: Perintah `SOURCE` di MySQL CLI gagal menemukan berkas SQL karena perbedaan tanda pemisah direktori (*backslash* vs *forward slash* di Windows).
   * **Solusi**: Menggunakan *forward slash* (`/`) pada path lengkap berkas (misal: `SOURCE C:/Users/.../database/sql/tugas_pertemuan4_struktur.sql;`).

---

### 11. KESIMPULAN
Pada Pertemuan 4 ini, mahasiswa berhasil merancang dan mengimplementasikan skema basis data relasional langsung pada tingkat DBMS MySQL menggunakan DDL dan DML. Penerapan batasan integritas seperti `PRIMARY KEY`, `FOREIGN KEY`, `UNIQUE`, dan `CHECK` terbukti krusial dalam menjamin keabsahan dan konsistensi data sebelum aplikasi web dihubungkan ke framework seperti Laravel pada pertemuan-pertemuan berikutnya.

---

### 12. LAMPIRAN SOURCE CODE & STRUKTUR FOLDER
Struktur berkas pada proyek Laravel:
```text
praktikum_web_2401020105/
├── database/
│   └── sql/
│       ├── erd_relasi.svg
│       ├── pertemuan4_struktur.sql
│       ├── pertemuan4_data.sql
│       ├── tugas_pertemuan4_struktur.sql
│       ├── tugas_pertemuan4_data.sql
│       └── tugas_pertemuan4_uji.sql
├── LAPORAN_PRAKTIKUM_4.md
└── ...
```
