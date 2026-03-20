# Technical overview

Last updated: 2026-03-20

## What this is

A web application for managing DHCP host registrations, generating `dhcpd.conf` files, and exposing an API for DHCP servers to poll for configuration updates.

## Stack

- PHP 8.4 / Laravel 13
- Livewire 4 / Flux UI Pro 2
- Pest 4 (testing)
- Keycloak SSO via Laravel Socialite
- Laravel Horizon (queues)
- Tailwind CSS 4 / Vite 8
- Lando (local development)

## Directory structure

```
app/
  Enums/
    HostStatus.php           # Enabled, Disabled, Up, Down
  Http/
    Controllers/
      Api/
        DhcpApiController.php    # check-updates, config, hosts, flag-error
        HostStatusController.php # POST online/offline status
      Auth/
        SSOController.php        # Keycloak SSO + local login fallback
      ExportController.php       # CSV and JSON host exports
    Middleware/
      LegacyApiRouter.php       # Translates old ?action= query params to new API routes
  Livewire/
    HostList.php                # Searchable host listing (home page)
    HostForm.php                # Create/edit/delete a host
    DhcpSectionEditor.php      # Edit raw DHCP config sections (admin only)
  Models/
    Host.php                   # Core model: hostname, MAC, IP, owner, status
    DhcpSection.php            # Named config blocks (Header, Subnets, Groups, Footer)
    Checkin.php                # Tracks when DHCP servers last fetched config
    User.php
  Services/
    DhcpConfigGenerator.php    # Assembles full dhcpd.conf from sections + hosts
config/
  dhcp.php                    # Admin GUIDs, DNS servers, IP ranges, alert settings
```

## Domain model

```
User (auth only, no host ownership FK)

Host
  ├── hostname (nullable, auto-generated as eng-pool-{id} if blank)
  ├── mac (normalised to lowercase colon-separated on save)
  ├── ip (nullable, unique when set)
  ├── owner (email)
  ├── added_by (username)
  ├── status → HostStatus enum
  ├── ssd (Yes/No)
  ├── wireless (Yes/No)
  └── last_updated (touched on every save)

DhcpSection
  ├── section (Header, Subnets, Groups, Footer)
  └── body (raw config text)

Checkin
  ├── hostname (DHCP server name)
  └── checked_in_at
```

### HostStatus enum

| Value | Colour | In config | Notes |
|-------|--------|-----------|-------|
| Enabled | green | Active line | Default for new hosts |
| Disabled | red | Commented out (`### DISABLED`) | Manually disabled |
| Up | green | Active line | Set by monitoring API |
| Down | amber | Active line | Set by monitoring API |

`uiEquivalent()` maps Up/Down back to Enabled/Disabled for the edit form radio buttons.

## Routes overview

### Web routes (all require auth)

| Route | Handler | Notes |
|-------|---------|-------|
| `GET /` | `HostList` | Searchable host listing, shows 50 most recent by default |
| `GET /hosts/create` | `HostForm` | New host form |
| `GET /hosts/{host}/edit` | `HostForm` | Edit existing host |
| `GET /subnet-usage` | Blade view | Subnet usage page |
| `GET /export/csv` | `ExportController@csv` | Download hosts as CSV |
| `GET /export/json` | `ExportController@json` | JSON host export |
| `GET /dhcp-sections/{sectionName}/edit` | `DhcpSectionEditor` | Admin only (Gate: `dhcp-admin`) |

### API routes (unauthenticated)

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/dhcp/check-updates?host=` | GET | Returns "Yes"/"No" if config changed since last check-in |
| `/api/dhcp/config` | GET | Full generated `dhcpd.conf` |
| `/api/dhcp/hosts` | GET | CSV export of all hosts |
| `/api/dhcp/flag-error` | GET/POST | Sends alert email about corrupt DHCP file |
| `/api/dhcp/hosts/{id}/online` | POST | Mark host as Up |
| `/api/dhcp/hosts/{id}/offline` | POST | Mark host as Down |

### Legacy API compatibility

`LegacyApiRouter` middleware intercepts `?action=` query parameters and redirects:

| Old format | New endpoint |
|-----------|-------------|
| `?action=api_checkupdates` | `/api/dhcp/check-updates` |
| `?action=api_getdhcp` | `/api/dhcp/config` |
| `?action=api_gethosts` | `/api/dhcp/hosts` |
| `?action=api_flagerror` | `/api/dhcp/flag-error` |
| `?action=api_setonline&id=N` | `/api/dhcp/hosts/N/online` |
| `?action=api_setoffline&id=N` | `/api/dhcp/hosts/N/offline` |

## Key business logic

| Location | Purpose |
|----------|---------|
| `Host::toDhcpConfigLine()` | Generates a single `dhcpd.conf` host entry, with SSD DNS and disabled commenting |
| `DhcpConfigGenerator::generate()` | Assembles full config: Header + host lines + Subnets + Groups + Footer |
| `Host::normaliseMac()` | Strips separators, lowercases, re-joins as `aa:bb:cc:dd:ee:ff` |
| `Host::booted()` | Auto-sets added_date, normalises MAC, auto-generates hostname, touches last_updated |
| `HostForm::save()` | Validates, saves, then warns if duplicate MAC exists |
| `Checkin` model + `checkUpdates` API | Change-detection so DHCP servers only pull config when something changed |
| `SSOController` | Keycloak SSO with student filtering (matric number detection) |

## Authorization

- **Web UI**: all routes require authentication via `auth` middleware
- **DHCP section editing**: requires the `dhcp-admin` gate
- **Admin users**: `is_admin` boolean on the User model, plus `DHCP_ADMIN_GUIDS` env var
- **API routes**: unauthenticated (consumed by DHCP servers on the network)
- **SSO**: can restrict to admins only (`sso.admins_only`), block students (`sso.allow_students`), auto-create users (`sso.autocreate_new_users`)

## Testing

- Framework: Pest 4
- Pattern: mostly feature tests, a few unit tests for pure logic
- Run: `php artisan test --compact`
- Tests cover: host CRUD, API endpoints, DHCP config generation, MAC normalisation, golden-master config output, CSV/JSON exports, legacy route redirects, subnet usage
- Database: in-memory SQLite via `RefreshDatabase` trait

## Configuration

Key environment variables in `config/dhcp.php`:

| Variable | Purpose |
|----------|---------|
| `DHCP_ADMIN_GUIDS` | Comma-separated admin user GUIDs |
| `GUID_NAMES` | Maps GUIDs to display names (`guid:Name,...`) |
| `DHCP_ALERT_EMAIL` | Email for DHCP error alerts |
| `DHCP_SSD_DNS_SERVERS` | DNS servers for SSD-flagged hosts |
| `DHCP_ALLOWED_IP_RANGES` | Regex for valid IP ranges (default: `130.209` and `172.20`) |
| `IPSEEN_URL` / `IPSEEN_API_KEY` | External IP-seen service integration |

## Local development

```bash
cp .env.example .env
lando start
lando mfs          # migrate:fresh + seed with TestDataSeeder
npm run dev        # or: lando npmd
```

Test user: `admin2x` / `secret`
