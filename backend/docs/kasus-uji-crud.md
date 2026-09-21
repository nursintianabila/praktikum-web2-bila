# Laporan Kasus Uji CRUD Tiket Helpdesk (`docs/kasus-uji-crud.md`)

**Tanggal Pengujian:** 21 September 2026  
**Lingkungan:** Lokal (Laravel Development Server)  
**Target Database:** MySQL / SQLite (`praktikum_web2`)  

---

### Matriks Pengujian 18 Kasus Uji (Test Cases)

| ID TC | Nama Kasus Uji | Skenario / Payload | Hasil yang Diharapkan (Expected Result) | Status | Keterangan & Bukti |
| :--- | :--- | :--- | :--- | :---: | :--- |
| **TC-01** | Create Valid | Isi seluruh field valid pada `/tickets/create` | Status `303 Redirect` ke detail, tiket berstatus `open`, `tickets` +1, `comments` +1 | **PASS** | Data tersimpan lengkap dengan 1 komentar awal |
| **TC-02** | Input Kosong | Lewati HTML `required`, kirim form kosong (`subject`, `description`, `note` kosong) | Menampilkan pesan error validasi server, DB tidak berubah | **PASS** | Validasi `required` FormRequest berhasil menolak |
| **TC-03** | Spasi Saja | Isi `subject` dengan 3 karakter spasi (`"   "`) | Ter-normalisasi *trim* menjadi string kosong, ditolak aturan `required` | **PASS** | Menampilkan error *"Subjek wajib diisi"* |
| **TC-04** | Batas Subject | Uji `subject` dengan panjang 149, 150, dan 151 karakter (ASCII) | 149 & 150 karakter **PASS**; 151 karakter **FAIL** (menampilkan error max 150) | **PASS** | Diuji pada Create & Update |
| **TC-05** | Batas Description & Note | Uji `description` (5000 vs 5001) dan `note` (1000 vs 1001) | Panjang 5000 & 1000 **PASS**; panjang 5001 & 1001 **FAIL** dengan error validasi | **PASS** | Mencegah *overflow* teks pada database |
| **TC-06** | Identifier Relasi Tidak Sah | Kirim `category_id=9999` (non-eksisten) dan `category_id=abc` (string) | Ditolak oleh validasi `exists:categories,id`, DB tidak berubah | **PASS** | Diuji juga pada `user_id` saat Create |
| **TC-07** | Identifier Route Tidak Sah | Akses `GET /tickets/abc`, `GET /tickets/9999/edit`, `PUT /tickets/9999` | Server mengembalikan HTTP `404 Not Found`, DB tidak berubah | **PASS** | Route Model Binding menangani ID invalid |
| **TC-08** | Update Valid | Ubah `subject`, `category_id`, `status`, dan kirim `note` baru | Tiket ter-update, `user_id` tetap, `comments` +1 | **PASS** | Status berubah dan catatan baru bertambah |
| **TC-09** | Update Invalid | Ubah `subject` 151 karakter atau `status=invalid_status` | Ditolak validasi server, data lama di DB tetap, `comments` tidak bertambah | **PASS** | Data tiket lama tidak korup |
| **TC-10** | Manipulasi Field Terlarang | Kirim `status=closed` saat Create, kirim `user_id` lain saat Update | Ditolak oleh aturan `prohibited`, data terlarang diabaikan | **PASS** | *Validated payload* mencegah *mass assignment* |
| **TC-11** | Urgensi (Checkbox) | Centang `is_urgent` saat Create, hapus centang saat Edit; kirim `is_urgent=abc` | Nilai berubah `true` lalu `false`. Kirim `abc` ditolak validasi `boolean` | **PASS** | `hidden input=0` menangani uncheck checkbox |
| **TC-12** | Delete Sukses | Hapus tiket berstatus `open` atau `pending` yang memiliki komentar | Status `303 Redirect` ke index, tiket & seluruh komentar terhapus (*cascade*) | **PASS** | Berhasil dites pada tiket |
| **TC-13** | Delete Ditolak | Hapus tiket berstatus `closed` | Ditolak oleh aturan bisnis `TicketService`, muncul error, jumlah tiket/komentar tetap | **PASS** | Tiket `closed` dilindungi dari penghapusan |
| **TC-14** | Rollback Multi-Tabel | Jalankan `TicketTransactionTest` (listener `Comment::creating` melempar exception) | Exception memicu `DB::transaction` rollback. Tiket & komentar kembali ke keadaan awal | **PASS** | Teruji via PHPUnit (`PASS 10 assertions`) |
| **TC-15** | PRG dan Refresh | Amati tab Network DevTools saat simpan data valid | Urutan HTTP: `POST 303` $\rightarrow$ `GET 200`. Tekan `F5` tidak memicu duplikasi data | **PASS** | Aman dari *double submit* saat refresh |
| **TC-16** | Escaping Output | Isi `subject` dengan `<b>Uji Cross Site Scripting</b>` | Teks tampil literal sebagai `<b>Uji...</b>`, bukan dicetak tebal oleh browser | **PASS** | Tanda `{ { $ticket->subject } }` Blade mencegah XSS |
| **TC-17** | CSRF Protection | Hapus elemen `<input type="hidden" name="_token">` lalu submit form | Server mengembalikan HTTP `419 Page Expired`, DB tidak berubah | **PASS** | Middleware CSRF aktif melindungi form |
| **TC-18** | Tipe Data Salah | Kirim `subject` sebagai array (`subject[]` via DevTools/HTTP Client) | Server menolak dengan pesan error validasi `string`, bukan error 500 | **PASS** | Mencegah `TypeError` pada fungsi `trim()` |

---

### Catatan Hasil Evaluasi Kasus Uji

* **Keamanan Server-Side:** Seluruh pengujian *bypass* HTML (TC-02, TC-03, TC-04, TC-17, TC-18) membuktikan bahwa `FormRequest` Laravel memvalidasi dan memfilter input secara ketat sebelum menyentuh lapisan *Service* dan *Database*.
* **Integritas Data:** Pengujian transaksi atomik (TC-14) serta penghapusan berantai (*cascade delete* pada TC-12) memastikan database selalu konsisten dan tidak meninggalkan *orphan records*.