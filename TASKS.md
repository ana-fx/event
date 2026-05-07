# Scanner Feature — Task Checklist

Implementasi fitur scanner INGATE. Semua file sudah dibuat/diubah di Windows,
tinggal **build & test di Mac lokal**.

---

## Status File

### Backend (Go)

| File | Status | Keterangan |
|------|--------|------------|
| `backend/internal/handlers/scanner/events.go` | ✅ Dibuat | Handler `GET /api/scanner/events` — daftar event yg ditugaskan ke scanner |
| `backend/internal/handlers/scanner/scan.go` | ✅ Diupdate | `Verify` & `Redeem` sekarang require `event_id` + validasi assignment |
| `backend/cmd/api/main.go` | ✅ Diupdate | Scanner routes sekarang pakai `RoleMiddleware("scanner")` |

### Frontend (Next.js)

| File | Status | Keterangan |
|------|--------|------------|
| `frontend/src/components/auth/ScannerGuard.tsx` | ✅ Dibuat | Guard: hanya role `scanner` yang bisa akses |
| `frontend/src/app/scanner/layout.tsx` | ✅ Dibuat | Layout scanner dengan header + nav + logout |
| `frontend/src/app/scanner/page.tsx` | ✅ Diubah | Dashboard — daftar event yang ditugaskan |
| `frontend/src/app/scanner/scan/[eventId]/page.tsx` | ✅ Dibuat | Halaman scan per event (verify → confirm → redeem) |

---

## TODO di Mac

### 1. Build & Cek Error Go
```bash
cd backend
go build ./...
```

### 2. Jalankan Dev Server
```bash
# macOS
bash start_dev.sh
```

### 3. Test Alur Scanner

1. Login sebagai user role `scanner` di `/admin/login`
   - Jika belum ada akun scanner: buat dulu via admin panel `/admin/users`
2. Setelah login → harus redirect ke `/scanner`
3. Dashboard tampil daftar event yang di-assign admin
   - Assign scanner ke event dulu via admin: `/admin/events` → edit event → tab Assignments
4. Klik tombol **Scan** pada salah satu event
5. Masukkan kode tiket yang statusnya `paid`
6. Cek: muncul detail pemegang tiket + tombol **Izinkan Masuk**
7. Klik **Izinkan Masuk** → tiket ter-redeem, tampil layar hijau sukses

### 4. Test Kasus Edge

| Skenario | Expected |
|----------|----------|
| Login bukan role scanner → akses `/scanner` | Redirect ke `/` |
| Akses `/api/scanner/events` dengan token organizer | HTTP 403 |
| Scan tiket event yang bukan tugasan scanner | Error "Forbidden: You are not assigned to this event" |
| Scan kode tiket yang salah | Error "Ticket not found" |
| Scan tiket yang sudah di-redeem | Error "Already Redeemed" |
| Scan tiket status bukan `paid` | Error "Ticket status: ..." |

---

## Perubahan Teknis Penting

- **Backend**: `Verify` dan `Redeem` sekarang wajib kirim `event_id` di body request.
  Field lama (`code` saja) tidak cukup — frontend scan page sudah menyertakan `event_id` otomatis.
- **Frontend**: `scanner/page.tsx` lama (halaman scan langsung) sudah **diganti** dengan dashboard.
  Halaman scan sekarang ada di `/scanner/scan/[eventId]`.
