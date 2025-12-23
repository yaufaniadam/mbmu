# MBM App - Catatan Penting
================================================

## Server Production Setup

### 1. Cron Job untuk Laravel Scheduler
Tambahkan ke crontab server (`crontab -e`):
```
* * * * * cd /path/to/mbm-app && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Scheduled Tasks (Otomatis)
- `distribution:generate-daily` - Generate rencana distribusi (00:01)
- `invoice:generate` - Generate invoice SPPG_SEWA (00:05)
- `app:generate-bills` - Generate bills (setiap 10 detik)


## Artisan Commands Penting

### Database
```bash
php artisan migrate                    # Jalankan migrasi
php artisan migrate:fresh --seed       # Reset database + seed
php artisan db:seed --class=RolePermissionSeeder  # Seed roles & permissions
```

### Cache
```bash
php artisan cache:clear               # Clear application cache
php artisan config:clear              # Clear config cache
php artisan view:clear                # Clear view cache
php artisan permission:cache-reset    # Reset Spatie permission cache
```

### Distribusi & Invoice
```bash
php artisan distribution:generate-daily  # Manual generate rencana distribusi
php artisan invoice:generate             # Manual generate invoice
```

### Queue (untuk import)
```bash
php artisan queue:work --once --tries=1  # Proses 1 job queue
php artisan queue:work                    # Worker terus jalan
```


## Fitur Otomatis

### Auto-generate Rencana Distribusi
- Berjalan setiap hari pukul 00:01
- Skip hari Minggu
- Skip hari libur nasional (dari tabel holidays)

### Auto-generate Invoice SPPG_SEWA
- Berjalan setiap hari pukul 00:05
- Setiap 10 hari aktif
- Amount = jumlah hari aktif × Rp 6.000.000
- Hari aktif = record production_schedules dengan status Selesai/Didistribusikan/Terverifikasi


## Panel URLs
- Admin Panel: `/admin`
- SPPG Panel: `/sppg`
- Production Panel: `/production`
