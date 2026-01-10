# Laravel Resilience Protocol
**Status:** ACTIVE
**Role:** Principal Software Architect & Security Auditor

This document outlines the strict coding standards and security protocols for this Laravel Enterprise-Grade project. All contributions must adhere to these rules.

## 1. ARSITEKTUR & STRUKTUR (The "Skinny Controller" Mandate)
* **Controller:** HARUS "Skinny". Controller dilarang mengandung *business logic*. Tugas controller hanya: Validasi Request -> Panggil Service/Action -> Return Response (JSON/View).
* **Service/Action Layer:** Pindahkan semua logika bisnis kompleks ke dalam `App\Services` atau `App\Actions`.
* **DTO (Data Transfer Objects):** JANGAN pass `Request` object mentah atau array tak bertipe ke dalam Service. Gunakan DTO (misal: `spatie/laravel-data` atau class PHP native) untuk memastikan type safety antar layer.
* **Model:** Model dilarang gemuk ("Fat Model"). Hindari logic kompleks di dalam Model. Model hanya untuk relasi dan scope query.

## 2. KEAMANAN & VULNERABILITY (Zero-Tolerance)
* **SQL Injection:** DILARANG menggunakan `DB::raw()` atau query manual kecuali mutlak diperlukan dan TERJALIN (bound) dengan benar. Gunakan Eloquent atau Query Builder dengan parameter binding.
* **CSRF:** Pastikan semua form HTML menggunakan directive `@csrf`. Untuk API, pastikan Sanctum/Passport terkonfigurasi benar.
* **XSS:** Gunakan `{{ $var }}` (escape) secara default di Blade. Jangan gunakan `{!! $var !!}` kecuali data sudah disanitasi menggunakan `e()` atau HTML Purifier.
* **Mass Assignment:** Semua Model harus menggunakan `$fillable` yang spesifik. DILARANG menggunakan `$guarded = []`.

## 3. DATA & VALIDASI
* **Form Request:** DILARANG melakukan validasi `$request->validate([...])` di dalam Controller. Wajib buat file terpisah `FormRequest` (`php artisan make:request`).
* **Type Hinting:** Semua function arguments dan return types HARUS ditulis secara eksplisit (Strong Typing). Gunakan `declare(strict_types=1);` di baris pertama file PHP.

## 4. ANTI-PATTERNS (Daftar Haram)
* **Helpers:** DILARANG menggunakan helper function `env()` di luar file `config/`. Gunakan `config('app.name')`, bukan `env('APP_NAME')`.
* **N+1 Query Problem:** Selalu gunakan Eager Loading (`with()`) saat mengambil data relasi. Anda wajib mengecek query count.
* **Logic in View:** DILARANG menulis query database atau logic PHP kompleks di file Blade.
* **Hardcoding:** Dilarang hardcode ID, credentials, atau magic strings. Pindahkan ke Config atau Enums.

## 5. IMPLEMENTASI FITUR (Workflow)
Setiap implementasi fitur baru harus mengikuti Chain of Thought ini:
1.  **Analisis Keamanan:** Potensi celah apa yang ada? (misal: IDOR, SQLi).
2.  **Struktur Data:** Buat DTO dan Migration terlebih dahulu.
3.  **Implementasi:** Tulis kode Service, lalu Controller, lalu Route.
