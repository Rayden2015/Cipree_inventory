# Banner Configuration Guide

## Overview

The banner popup system is now fully configurable. You can control when the banner appears, the disable date, and whether users can dismiss it.

## Configuration File

**File:** `config/banner.php`

## Environment Variables

Add these to your `.env` file to configure the banner:

```env
# Enable/disable the banner (true/false)
BANNER_ENABLED=true

# Date when service will be disabled (format: Y-m-d)
BANNER_DISABLE_DATE=2026-01-01

# Whether users can dismiss the banner (true/false)
BANNER_DISMISSIBLE=false
```

## Configuration Options

### `BANNER_ENABLED`

- **Type:** `boolean`
- **Default:** `true`
- **Description:** Enable or disable the banner popup
- **Options:**
  - `true` - Banner is enabled and will show
  - `false` - Banner is disabled and will not show at all

### `BANNER_DISABLE_DATE`

- **Type:** `string` (date format: Y-m-d)
- **Default:** `2026-01-01`
- **Description:** The date when the service will be disabled/moved out of testing phase
- **Format:** `YYYY-MM-DD` (e.g., `2026-01-01`)
- **Example:** `2026-01-15`, `2026-02-01`, etc.

### `BANNER_DISMISSIBLE`

- **Type:** `boolean`
- **Default:** `false`
- **Description:** Whether users can dismiss/close the banner
- **Options:**
  - `false` - Banner cannot be dismissed (permanent, no close button)
  - `true` - Banner can be dismissed (original behavior with "Maybe Later" button)

## Usage Examples

### Example 1: Permanent Banner (Non-Dismissible)

```env
BANNER_ENABLED=true
BANNER_DISABLE_DATE=2026-01-01
BANNER_DISMISSIBLE=false
```

**Result:**
- Banner shows after login
- Banner appears on every page load after login
- No close button (X) in header
- No "Maybe Later" button
- Users cannot dismiss it

### Example 2: Dismissible Banner

```env
BANNER_ENABLED=true
BANNER_DISABLE_DATE=2026-01-01
BANNER_DISMISSIBLE=true
```

**Result:**
- Banner shows after login
- Banner shows only once per login (if not dismissed)
- Close button (X) in header
- "Maybe Later" button available
- Users can dismiss it for session or permanently

### Example 3: Disabled Banner

```env
BANNER_ENABLED=false
```

**Result:**
- Banner does not show at all
- Other settings are ignored when banner is disabled

## Current Default Configuration

As of January 1st, the default configuration is:

```env
BANNER_ENABLED=true
BANNER_DISABLE_DATE=2026-01-01
BANNER_DISMISSIBLE=false
```

This means:
- ✅ Banner is enabled
- 📅 Service disable date: January 1, 2026
- 🔒 Banner is non-dismissible (permanent)

## How to Change Configuration

### Step 1: Update `.env` File

Edit your `.env` file and add/update the banner configuration:

```env
BANNER_ENABLED=true
BANNER_DISABLE_DATE=2026-01-15
BANNER_DISMISSIBLE=false
```

### Step 2: Clear Config Cache

After updating `.env`, clear the configuration cache:

```bash
php artisan config:clear
php artisan config:cache
```

### Step 3: Test

1. Log out and log back in
2. Verify the banner appears with the correct date
3. Verify the banner behavior matches your settings

## Banner Behavior Details

### Non-Dismissible Banner (`BANNER_DISMISSIBLE=false`)

- ✅ Shows after login on first page load
- ✅ Shows on every subsequent page load (until session ends)
- ❌ No close button (X) in modal header
- ❌ No "Maybe Later" button
- ❌ Cannot be closed by clicking outside modal
- ❌ Cannot be closed by pressing ESC key
- ✅ "Explore Plans" button still works (shows contact admin modal)

### Dismissible Banner (`BANNER_DISMISSIBLE=true`)

- ✅ Shows after login on first page load
- ✅ Shows only once per login session (if not dismissed)
- ✅ Close button (X) in modal header
- ✅ "Maybe Later" button available
- ✅ Can be dismissed for session or permanently
- ✅ "Explore Plans" button works (can dismiss banner)

## Files Involved

- `config/banner.php` - Configuration file
- `resources/views/partials/banner.blade.php` - Banner view/template
- `app/Http/Controllers/Auth/LoginController.php` - Sets banner flag on login
- `app/Http/Controllers/HomeController.php` - Banner dismissal handlers

## Troubleshooting

### Banner Not Showing

1. Check `BANNER_ENABLED=true` in `.env`
2. Clear config cache: `php artisan config:clear`
3. Verify user is logged in
4. Check browser console for JavaScript errors

### Date Not Updating

1. Update `BANNER_DISABLE_DATE` in `.env`
2. Clear config cache: `php artisan config:clear`
3. Format must be: `YYYY-MM-DD` (e.g., `2026-01-01`)

### Banner Still Dismissible When Set to False

1. Verify `BANNER_DISMISSIBLE=false` in `.env`
2. Clear config cache: `php artisan config:clear`
3. Clear browser cache and cookies
4. Log out and log back in

### Banner Shows on Every Page (Expected for Non-Dismissible)

This is the expected behavior when `BANNER_DISMISSIBLE=false`. The banner will show on every page load after login. If you want it to show only once per login, set `BANNER_DISMISSIBLE=true`.

## Quick Reference

| Setting | Values | Description |
|---------|--------|-------------|
| `BANNER_ENABLED` | `true` / `false` | Enable/disable banner |
| `BANNER_DISABLE_DATE` | `YYYY-MM-DD` | Service disable date |
| `BANNER_DISMISSIBLE` | `true` / `false` | Can users dismiss banner? |

