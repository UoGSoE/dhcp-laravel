# DHCP Laravel

A web app for managing DHCP host registrations on a university network. Staff can add, edit and search for hosts by MAC address, hostname or owner. The app generates a complete `dhcpd.conf` configuration file that DHCP servers pull via an API.

## What it does

The application maintains a database of network hosts, each with a MAC address, optional fixed IP, owner and status. Hosts can be enabled, disabled or marked as up/down by automated monitoring. A `DhcpConfigGenerator` service assembles the full DHCP configuration by combining editable config sections (header, subnets, groups, footer) with per-host entries, and serves it over an unauthenticated API so that DHCP servers can poll for changes.

A legacy API layer translates query-string parameters from the old system into the new REST-style endpoints, so existing scripts continue to work without modification.

Authentication is handled through Keycloak SSO, with a local login fallback for development.

## Prerequisites

- [Lando](https://lando.dev/) (for local development)
- [Composer](https://getcomposer.org/)
- Node.js and npm
- A [FluxUI](https://fluxui.dev/) licence (the project uses `livewire/flux-pro`)

## Getting started

```bash
git clone <your-repo-url> dhcp-laravel
cd dhcp-laravel
cp .env.example .env
lando start
lando mfs
```

`lando mfs` runs a fresh migration and seeds the database with test data. The `.env.example` file is pre-configured for Lando's local services.

Once running, log in with:

- Username: **admin2x**
- Password: **secret**

## Useful Lando commands

| Command | Description |
|---------|-------------|
| `lando artisan` | Run Artisan commands |
| `lando composer` | Run Composer commands |
| `lando npm` | Run npm commands |
| `lando mfs` | Drop, migrate and seed the database |
| `lando test` | Run the test suite in parallel |
| `lando testf SomeTest` | Run a filtered subset of tests |
| `lando horizon` | Start Laravel Horizon |

## Running tests

```bash
php artisan test --compact
```

Or through Lando:

```bash
lando test
```

The test suite uses Pest and covers the web UI, API endpoints, DHCP config generation, MAC normalisation, and a golden-master test for config output.

## Contributing

1. Clone the repository and follow the "Getting started" steps above
2. Create a branch for your changes
3. We do TDD, so write a failing test first, then make it pass
4. Run `php artisan test --compact` and make sure everything is green
5. Open a pull request

## Licence

MIT. See [LICENSE](LICENSE) for details.
