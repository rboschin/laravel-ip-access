# Laravel IP Access Package - Installation & Usage

## Installation

1. Install the package via Composer:
```bash
composer require rboschin/laravel-ip-access
```

2. Publish the configuration file:
```bash
php artisan vendor:publish --tag="ip-access-config"
```

3. Publish the database migrations (if using database mode):
```bash
php artisan vendor:publish --tag="ip-access-migrations"
```

4. Run the migrations:
```bash
php artisan migrate
```

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
# Access mode: whitelist or blacklist
IP_ACCESS_MODE=whitelist

# Source for whitelist: ".env" or "IpAccessWhite"
IP_ACCESS_WHITELIST_SOURCE=.env

# Source for blacklist: ".env" or "IpAccessBlack"
IP_ACCESS_BLACKLIST_SOURCE=.env

# Trust proxies for real IP detection
IP_ACCESS_TRUST_PROXIES=true

# Forbidden message
IP_ACCESS_FORBIDDEN_MESSAGE=Access denied
```

### Using .env for IP Lists

Set `IP_ACCESS_WHITELIST_SOURCE=.env` and `IP_ACCESS_BLACKLIST_SOURCE=.env`:

```env
IP_WHITELIST=127.0.0.1,192.168.1.100,10.0.0.*
IP_BLACKLIST=123.45.67.89,98.76.54.32
```

### Using Database for IP Lists

Set `IP_ACCESS_WHITELIST_SOURCE=IpAccessWhite` and `IP_ACCESS_BLACKLIST_SOURCE=IpAccessBlack`:

```env
IP_ACCESS_WHITELIST_SOURCE=IpAccessWhite
IP_ACCESS_BLACKLIST_SOURCE=IpAccessBlack
```

## Usage

### Middleware

Apply the middleware to your routes or groups:

```php
// In app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \Rboschin\LaravelIpAccess\Middleware\CheckIpAccess::class,
    ],
];

// Or apply to specific routes
Route::middleware('ip.access')->group(function () {
    Route::get('/admin', function () {
        // Protected routes
    });
});
```

### API Endpoints

When using database mode, you can manage IP lists via API:

#### Whitelist Management
- `GET /api/ip-access/whitelist` - Get all whitelisted IPs
- `POST /api/ip-access/whitelist` - Add IP to whitelist
- `PUT /api/ip-access/whitelist/{id}` - Update whitelist entry
- `DELETE /api/ip-access/whitelist/{id}` - Remove IP from whitelist

#### Blacklist Management
- `GET /api/ip-access/blacklist` - Get all blacklisted IPs
- `POST /api/ip-access/blacklist` - Add IP to blacklist
- `PUT /api/ip-access/blacklist/{id}` - Update blacklist entry
- `DELETE /api/ip-access/blacklist/{id}` - Remove IP from blacklist

### Database Schema

#### ip_access_whites table
- `id` - Primary key
- `ip_address` - IP address or CIDR notation
- `description` - Optional description
- `is_active` - Boolean flag to enable/disable entry
- `created_at`, `updated_at` - Timestamps

#### ip_access_blacks table
- `id` - Primary key
- `ip_address` - IP address or CIDR notation
- `description` - Optional description
- `is_active` - Boolean flag to enable/disable entry
- `created_at`, `updated_at` - Timestamps

## IP Format Support

The package supports the following IP formats:
- Single IP: `192.168.1.100`
- Wildcard: `192.168.1.*`
- CIDR notation: `192.168.1.0/24`

## Example API Usage

### Add IP to Whitelist
```bash
curl -X POST http://your-app.com/api/ip-access/whitelist \
  -H "Content-Type: application/json" \
  -d '{"ip_address": "192.168.1.100", "description": "Office network"}'
```

### Get Whitelist
```bash
curl http://your-app.com/api/ip-access/whitelist
```

Response:
```json
{
  "whitelist": ["192.168.1.100", "10.0.0.*"],
  "count": 2
}
```
