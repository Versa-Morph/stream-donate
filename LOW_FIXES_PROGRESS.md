# LOW PRIORITY FIXES - StreamDonate

**Date:** 2026-03-17  
**Session:** Phase 4 - Low Priority Issues  
**Status:** IN PROGRESS

---

## Executive Summary

This document tracks **LOW priority** code quality and maintainability improvements. These fixes don't affect functionality but improve code quality, developer experience, and follow best practices.

---

## COMPLETED FIXES

### 1. Bug Fix: CleanupOrphanedFilesJob Wrong Column Name

**Issue:** `CleanupOrphanedFilesJob` referenced non-existent column `alert_sound` instead of `notification_sound`

**File:** `app/Jobs/CleanupOrphanedFilesJob.php`

**Fix:** Changed column reference from `alert_sound` to `notification_sound`

```php
// Before (broken):
$referencedPaths = Streamer::whereNotNull('alert_sound')
    ->where('alert_sound', 'like', 'storage/%')
    ->pluck('alert_sound')

// After (fixed):
$referencedPaths = Streamer::whereNotNull('notification_sound')
    ->where('notification_sound', 'like', 'storage/%')
    ->pluck('notification_sound')
```

---

### 2. Remove Unused Import

**File:** `app/Http/Controllers/Auth/OtpController.php`

**Fix:** Removed unused `Illuminate\Support\Facades\Hash` import

---

### 3. Add Return Type Hints

**Files Modified:**
- `app/Http/Controllers/ObsController.php` - Added `: View` to all methods
- `app/Http/Controllers/ObsCanvasController.php` - Added `: View|RedirectResponse`, `: JsonResponse`, `: View`
- `app/Http/Controllers/QrController.php` - Added `: View` to `obsWidget()`
- `app/Http/Controllers/PolicyController.php` - Added `: View` to all methods
- `app/Http/Controllers/SseController.php` - Added `: JsonResponse` to `stats()`
- `app/Http/Controllers/ReportController.php` - Added `: Response|RedirectResponse` to `exportPdf()`

---

### 4. Fix Inline FQCNs

**File:** `app/Http/Controllers/Auth/OtpController.php`

**Fix:** Replaced inline fully qualified class names with proper `use` statements

```php
// Before:
\Illuminate\Support\Facades\DB::transaction(...)
\Illuminate\Support\Facades\Log::error(...)

// After:
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// ...
DB::transaction(...)
Log::error(...)
```

---

### 5. Add PHPDoc to Controllers and Models

**Files Modified:**
- `app/Http/Controllers/ObsController.php` - Added PHPDoc to all public methods
- `app/Http/Controllers/ObsCanvasController.php` - Improved existing PHPDoc
- `app/Http/Controllers/PolicyController.php` - Added comprehensive PHPDoc
- `app/Http/Controllers/SseController.php` - Improved PHPDoc on `stats()`
- `app/Http/Controllers/ReportController.php` - Improved PHPDoc on `exportPdf()`
- `app/Models/Streamer.php` - Added PHPDoc to all public methods including:
  - `casts()`, `generateApiKey()`, `user()`, `donations()`, `alertQueues()`
  - `activityLogs()`, `getWidgetSettings()`, `getCanvasConfig()`
  - `resetSubathonTimer()`, `addSubathonTime()`, `getTotalDonationsAttribute()`
  - `getTodayDonationsAttribute()`

---

### 6. Move Hardcoded Values to Config

**Sound Presets:**
- Added `sound_presets` array to `config/alert.php`
- Updated `StreamerDashboardController::saveAlertSettings()` to use config

**OTP Settings:**
- Created `config/otp.php` with configurable:
  - `length` - OTP code length (default: 8)
  - `expires_minutes` - Expiration time (default: 5)
  - `max_attempts` - Max verification attempts (default: 3)
  - `characters` - Allowed characters for OTP generation
- Updated `OtpController::sendOtp()` to use config values
- Added environment variables to `.env.example`

**Pagination Settings:**
- Created `config/pagination.php` with all pagination/limit values:
  - `dashboard_donations` - Streamer dashboard (50)
  - `running_text_donations` - OBS widget (20)
  - `admin_users`, `admin_donations`, `admin_logs` - Admin panel pagination
  - `admin_recent_donations`, `admin_recent_logs` - Admin dashboard limits
  - `admin_streamer_stats` - Admin dashboard top streamers (25)
  - `admin_banned_words` - Admin banned words list (50)
  - `report_donations` - Streamer reports (25)
  - `sse_max_alerts_batch` - SSE alert batch size (50)
  - `max_banned_words` - Safety limit (1000)

**Export Settings:**
- Created `config/export.php` with:
  - `pdf_max_records` - Max records in PDF export (1000)
  - `csv_chunk_size` - CSV streaming chunk size (500)
  - `qr_cache_ttl` - QR code cache TTL in seconds (3600)

---

### 7. Standardize Authentication Pattern

**Issue:** Inconsistent use of `Auth::user()` (Facade) vs `auth()->user()` (Helper)

**Fix:** Standardized all controllers to use `auth()->user()` helper for consistency

**Files Modified:**
- `app/Http/Controllers/StreamerDashboardController.php` - 15 instances
- `app/Http/Controllers/ReportController.php` - 3 instances
- `app/Http/Controllers/Streamer/BannedWordController.php` - 3 instances
- `app/Http/Controllers/AdminController.php` - 1 instance

---

### 8. Update Controllers to Use Config Values

**Files Modified:**
- `app/Http/Controllers/StreamerDashboardController.php` - Uses `config('pagination.dashboard_donations')`
- `app/Http/Controllers/ObsController.php` - Uses `config('pagination.running_text_donations')`
- `app/Http/Controllers/AdminController.php` - Uses all admin pagination configs
- `app/Http/Controllers/Admin/BannedWordController.php` - Uses `config('pagination.admin_banned_words')`
- `app/Http/Controllers/Streamer/BannedWordController.php` - Uses `config('pagination.max_banned_words')`
- `app/Http/Controllers/ReportController.php` - Uses `config('pagination.report_donations')` and `config('export.pdf_max_records')`
- `app/Http/Controllers/SseController.php` - Uses `config('pagination.sse_max_alerts_batch')`
- `app/Http/Controllers/QrController.php` - Uses `config('export.qr_cache_ttl')`

---

### 9. Extract buildStats() to Service

**Issue:** The `Streamer::buildStats()` method was 90+ lines with complex business logic mixed in the model.

**Solution:** Created `app/Services/StreamerStatsService.php` that:
- Encapsulates all stats calculation logic
- Provides `buildStats(Streamer $streamer)` for cached stats
- Provides `computeStats(Streamer $streamer)` for fresh stats
- Provides `invalidateCache(Streamer $streamer)` for cache management
- Implements dynamic cache TTL based on activity

**Backward Compatibility:** The `Streamer::buildStats()` method still works - it now delegates to the service.

```php
// Usage (both work):
$stats = $streamer->buildStats(); // Still works (model method)
$stats = app(StreamerStatsService::class)->buildStats($streamer); // Direct service call
```

---

## New Files Created

| File | Purpose |
|------|---------|
| `config/otp.php` | OTP configuration settings |
| `config/pagination.php` | Centralized pagination/limit values |
| `config/export.php` | Export settings (PDF, CSV, QR) |
| `app/Services/StreamerStatsService.php` | Stats calculation service |
| `LOW_FIXES_PROGRESS.md` | This documentation file |

---

## Files Modified

| File | Changes |
|------|---------|
| `app/Jobs/CleanupOrphanedFilesJob.php` | Fixed column name bug |
| `app/Http/Controllers/Auth/OtpController.php` | Fixed imports, use config |
| `app/Http/Controllers/ObsController.php` | Return types, PHPDoc, pagination config |
| `app/Http/Controllers/ObsCanvasController.php` | Return types, imports |
| `app/Http/Controllers/QrController.php` | Return type, import, cache TTL config |
| `app/Http/Controllers/PolicyController.php` | Return types, PHPDoc |
| `app/Http/Controllers/SseController.php` | Return type, PHPDoc, pagination config |
| `app/Http/Controllers/ReportController.php` | Return type, PHPDoc, pagination & export config |
| `app/Http/Controllers/StreamerDashboardController.php` | Auth pattern, pagination config, sound presets |
| `app/Http/Controllers/AdminController.php` | Auth pattern, pagination config |
| `app/Http/Controllers/Admin/BannedWordController.php` | Pagination config |
| `app/Http/Controllers/Streamer/BannedWordController.php` | Auth pattern, pagination config |
| `app/Models/Streamer.php` | PHPDoc, delegate to service |
| `config/alert.php` | Added sound_presets array |
| `.env.example` | Added OTP and cache TTL variables |

---

## Environment Variables Added

```env
# OTP Settings
OTP_LENGTH=8
OTP_EXPIRES_MINUTES=5
OTP_MAX_ATTEMPTS=3

# Dynamic Cache TTL (for streamer stats)
CACHE_STREAMER_STATS_RECENT=60
CACHE_STREAMER_STATS_IDLE=180
CACHE_STREAMER_STATS_INACTIVE=300

# Export Settings
EXPORT_PDF_MAX_RECORDS=1000
EXPORT_CSV_CHUNK_SIZE=500
EXPORT_QR_CACHE_TTL=3600
```

---

## Remaining LOW Priority Issues

### Code Quality
- [ ] Consider splitting `StreamerDashboardController` into smaller focused controllers
  - Could extract: SettingsController, SubathonController, WidgetController, AlertController

### Testing (Future Work)
- [ ] Add feature tests for donation flow
- [ ] Add feature tests for SSE endpoints
- [ ] Add unit tests for ProfanityFilter service
- [ ] Add unit tests for QrCodeGenerator service
- [ ] Add unit tests for StreamerStatsService
- [ ] Add feature tests for admin operations

---

## Statistics

| Metric | Count |
|--------|-------|
| Bugs Fixed | 1 |
| Files Modified | 15 |
| Files Created | 5 |
| Return Types Added | 12 |
| PHPDoc Blocks Added | 20+ |
| Config Values Extracted | 18 |
| Environment Variables Added | 9 |
| Auth Pattern Standardized | 22 instances |
| Services Created | 1 |

---

## Overall Progress Summary

| Phase | Status | Completion |
|-------|--------|------------|
| Phase 1: CRITICAL | ✅ Complete | 14/14 (100%) |
| Phase 2: HIGH | ✅ Complete | 15/15 (100%) |
| Phase 3: MEDIUM | ✅ Complete | 13/13 (100%) |
| Phase 4: LOW | 🚧 In Progress | ~15/27 (~55%) |

**Total Issues Fixed:** 57+ issues across all phases
