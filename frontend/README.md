# Praktikum Pemrograman Web 2

## Deskripsi

Aplikasi web sederhana yang dikembangkan untuk memenuhi tugas mata kuliah Pemrograman Web 2. Project menggunakan konsep *decoupled architecture* dengan Laravel sebagai RESTful API di sisi backend dan Vue.js yang dibundel menggunakan Vite di sisi frontend.

Pada tahap ini, frontend digunakan untuk latihan komponen Vue, props, emits, reactive state, computed, watcher, lifecycle, validasi form, filtering, dan Vue Devtools.

## Teknologi

| Teknologi          | Versi                 |
| ------------------ | --------------------- |
| Backend            | PHP 8.x, Laravel 10.x |
| Frontend           | Vue.js 3.5.42         |
| Vite               | 5.4.21                |
| @vitejs/plugin-vue | 5.2.4                 |
| Node.js            | v18.8.0               |
| npm                | 8.18.0                |
| Database           | MySQL (Laragon)       |
| Version Control    | Git & GitHub          |

## Prasyarat

Sebelum menjalankan project, pastikan perangkat sudah memiliki:

* PHP >= 8.1
* Composer
* Node.js v18.x
* NPM
* Laragon atau XAMPP untuk MySQL Server
* Git

## Instalasi dan Menjalankan Frontend

Masuk ke folder frontend:

```bash
cd frontend
```

Install dependency:

```bash
npm install
```

Menjalankan development server:

```bash
npm run dev
```

Project dapat dibuka melalui alamat yang ditampilkan oleh Vite, misalnya:

```text
http://localhost:5173
```

Menjalankan production build:

```bash
npm run build
```

Perintah `npm run build` digunakan untuk memastikan project dapat dikompilasi tanpa error.

## Struktur Frontend

```text
frontend/
├── src/
│   ├── App.vue
│   ├── data.js
│   ├── main.js
│   └── components/
│       ├── BasePanel.vue
│       ├── TicketStatus.vue
│       ├── TicketCard.vue
│       ├── TicketList.vue
│       ├── TicketFilter.vue
│       └── TicketForm.vue
├── package.json
├── package-lock.json
└── README.md
```

### Tanggung Jawab Komponen

* `App.vue` menjadi pemilik state utama dan mengatur perubahan data tiket.
* `BasePanel.vue` menyediakan panel yang dapat digunakan kembali dengan default slot dan named slot footer.
* `TicketStatus.vue` menampilkan label berdasarkan status tiket.
* `TicketCard.vue` menampilkan satu tiket dan mengirim event perubahan status.
* `TicketList.vue` menampilkan daftar tiket dan meneruskan event dari `TicketCard`.
* `TicketFilter.vue` mengatur filter status menggunakan `v-model`.
* `TicketForm.vue` mengelola draft input form secara lokal dan mengirim payload melalui event `submit`.
* `data.js` menyimpan kategori dan data tiket awal.

## Props dan Emits

| Komponen       | Props                    | Emits               |
| -------------- | ------------------------ | ------------------- |
| `BasePanel`    | `title`                  | -                   |
| `TicketStatus` | `status`                 | -                   |
| `TicketCard`   | `ticket`, `categoryName` | `advance`           |
| `TicketList`   | `tickets`, `categories`  | `advance`           |
| `TicketFilter` | `modelValue`             | `update:modelValue` |
| `TicketForm`   | `categories`             | `submit`            |

Alur data menggunakan prinsip **props turun, event naik**.

`App.vue` merupakan pemilik state utama sehingga komponen anak tidak mengubah props secara langsung.

## Ownership State

State utama berada di `App.vue`, yaitu:

* `tickets`
* `selectedStatus`
* `showForm`
* `formVersion`
* `message`
* `lastSubmission`

`filteredTickets` merupakan derived state yang dibuat menggunakan `computed`.

Komponen `TicketForm` memiliki draft form lokal karena input belum menjadi data tiket sampai form berhasil dikirim.

Alur data:

```text
App.vue
   │
   ├── props ↓
   │
   ├── TicketFilter
   ├── TicketList
   │      └── TicketCard
   └── TicketForm
          │
          └── event ↑
```

Perubahan state utama dilakukan oleh `App.vue`.

## Perilaku Filter

Filter menggunakan `selectedStatus` dan `filteredTickets`.

Pilihan filter:

* Semua
* Terbuka
* Diproses
* Selesai

Filtering tidak menghapus data sumber.

Pada data awal:

| Filter   | Jumlah hasil | `tickets.length` |
| -------- | -----------: | ---------------: |
| Semua    |            3 |                3 |
| Terbuka  |            1 |                3 |
| Diproses |            1 |                3 |
| Selesai  |            1 |                3 |

Perubahan filter hanya memengaruhi `filteredTickets`, sedangkan `tickets` tetap menjadi sumber data utama.

## Perilaku Perubahan Status

Status tiket mengikuti alur:

```text
open → pending → closed
```

Tiket yang sudah `closed` tidak dapat dilanjutkan lagi.

Ketika status tiket berubah, `App.vue` memperbarui state `tickets`. Setelah itu `filteredTickets` dihitung kembali sehingga tampilan mengikuti state terbaru.

Contoh:

```text
Filter open
    ↓
Tiket #1 dipindahkan
open → pending
    ↓
Tiket #1 hilang dari hasil filter open
    ↓
Tiket #1 muncul pada filter pending
```

## Perilaku Reset Form

Setelah form berhasil dikirim:

* tiket baru ditambahkan ke `tickets`;
* status tiket baru adalah `open`;
* filter dikembalikan ke `all`;
* payload terakhir disimpan pada `lastSubmission`;
* `formVersion` bertambah;
* komponen `TicketForm` dibuat ulang sehingga draft form menjadi kosong.

Jika form ditutup menggunakan `v-if`, komponen `TicketForm` dihancurkan sehingga draft lokal yang belum dikirim ikut hilang.

## Perilaku Refresh

Data tiket hanya disimpan di memori Vue.

Karena belum menggunakan database atau API untuk penyimpanan data tiket, refresh halaman akan mengembalikan aplikasi ke data awal:

```text
3 tiket
1 open
1 pending
1 closed
```

Data yang ditambahkan atau perubahan status yang dilakukan selama sesi tidak bertahan setelah halaman di-refr
