# Laravel IP Access Control

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Un package Laravel per controllare l'accesso al sistema tramite whitelist o blacklist di indirizzi IP, configurabile tramite file `.env`.

## Caratteristiche

- ✅ Supporto modalità **whitelist** (solo IP specificati possono accedere)
- ✅ Supporto modalità **blacklist** (tutti gli IP possono accedere tranne quelli specificati)
- ✅ Configurazione semplice tramite file `.env`
- ✅ Supporto per **wildcards** (es. `192.168.1.*`)
- ✅ Supporto per **notazione CIDR** (es. `192.168.1.0/24`)
- ✅ Gestione proxy e load balancer (X-Forwarded-For)
- ✅ Risposta HTTP **403 Forbidden** quando l'accesso è negato
- ✅ Middleware applicabile globalmente, per gruppo di route o singola route

## Requisiti

- PHP >= 7.4
- Laravel >= 8.0

## Installazione

### 1. Installazione del package

Se il package è ospitato su Packagist:

```bash
composer require rboschin/laravel-ip-access
composer update
```

Se stai usando un repository locale, aggiungi nel `composer.json` del tuo progetto Laravel:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../packages/laravel-ip-access"
        }
    ],
    "require": {
        "rboschin/laravel-ip-access": "*"
    }
}
```
oppure

```bash
composer config repositories.laravel-ip-access path ../packages/laravel-ip-access
composer require rboschin/laravel-ip-access:@dev
```

Quindi esegui:

```bash
composer update
```

### 2. Pubblicazione della configurazione (opzionale)

Se desideri personalizzare la configurazione, pubblica il file di config:

```bash
php artisan vendor:publish --tag=ip-access-config
```

Questo creerà il file `config/ip-access.php`.

## Configurazione

### File .env

Aggiungi le seguenti variabili al tuo file `.env`:

#### Modalità Whitelist

```env
# Modalità: whitelist o blacklist
IP_ACCESS_MODE=whitelist

# Lista di IP consentiti (separati da virgola)
IP_WHITELIST=127.0.0.1,192.168.1.100,10.0.0.*,172.16.0.0/12

# Messaggio personalizzato di accesso negato (opzionale)
IP_ACCESS_FORBIDDEN_MESSAGE="Accesso negato. Il tuo IP non è autorizzato."

# Trust proxies per X-Forwarded-For (opzionale, default: true)
IP_ACCESS_TRUST_PROXIES=true
```

#### Modalità Blacklist

```env
# Modalità: whitelist o blacklist
IP_ACCESS_MODE=blacklist

# Lista di IP bloccati (separati da virgola)
IP_BLACKLIST=123.45.67.89,98.76.54.32,192.168.100.*

# Messaggio personalizzato di accesso negato (opzionale)
IP_ACCESS_FORBIDDEN_MESSAGE="Accesso negato. Il tuo IP è stato bloccato."
```

### Formati IP supportati

- **IP esatto**: `192.168.1.100`
- **Wildcard**: `192.168.1.*` (tutti gli IP da 192.168.1.0 a 192.168.1.255)
- **CIDR**: `192.168.1.0/24` (notazione CIDR standard)

## Utilizzo

### Applicazione globale del middleware

Per applicare il controllo IP a tutte le route, aggiungi il middleware in `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ...
    \Rboschin\LaravelIpAccess\Middleware\CheckIpAccess::class,
];
```

### Applicazione a gruppo di route

```php
// routes/web.php
Route::middleware(['ip.access'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### Applicazione a singola route

```php
// routes/web.php
Route::get('/admin', [AdminController::class, 'index'])->middleware('ip.access');
```

### Applicazione condizionale

Puoi anche applicare il middleware solo in determinati ambienti:

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ...
    ],
    
    'admin' => [
        // middlewares per admin
        env('APP_ENV') === 'production' ? \Rboschin\LaravelIpAccess\Middleware\CheckIpAccess::class : null,
    ],
];
```

## Esempi di configurazione

### Esempio 1: Solo localhost in sviluppo

```env
IP_ACCESS_MODE=whitelist
IP_WHITELIST=127.0.0.1,::1
```

### Esempio 2: Bloccare IP specifici

```env
IP_ACCESS_MODE=blacklist
IP_BLACKLIST=123.45.67.89,98.76.54.32
```

### Esempio 3: Consentire solo rete aziendale

```env
IP_ACCESS_MODE=whitelist
IP_WHITELIST=192.168.1.0/24,10.0.0.0/8
```

### Esempio 4: Consentire range con wildcard

```env
IP_ACCESS_MODE=whitelist
IP_WHITELIST=192.168.1.*,192.168.2.*,127.0.0.1
```

## Testing

Per testare il middleware, puoi simulare diversi IP:

```bash
# Testare con curl specificando l'IP
curl -H "X-Forwarded-For: 192.168.1.100" http://tuodominio.test/admin
```

Oppure creare un test PHPUnit:

```php
public function test_ip_whitelist_blocks_unauthorized_ip()
{
    config(['ip-access.mode' => 'whitelist']);
    config(['ip-access.whitelist' => ['192.168.1.100']]);
    
    $response = $this->get('/admin', ['REMOTE_ADDR' => '10.0.0.1']);
    
    $response->assertStatus(403);
}

public function test_ip_whitelist_allows_authorized_ip()
{
    config(['ip-access.mode' => 'whitelist']);
    config(['ip-access.whitelist' => ['192.168.1.100']]);
    
    $response = $this->get('/admin', ['REMOTE_ADDR' => '192.168.1.100']);
    
    $response->assertStatus(200);
}
```

## Come funziona

1. Il middleware `CheckIpAccess` intercetta ogni richiesta
2. Ottiene l'IP del client (considerando X-Forwarded-For se configurato)
3. Controlla se l'IP è consentito o bloccato in base alla modalità configurata
4. Se l'accesso è negato, restituisce una risposta HTTP 403 Forbidden
5. Se l'accesso è consentito, passa la richiesta al prossimo middleware

## Sicurezza

- Assicurati di configurare correttamente `IP_ACCESS_TRUST_PROXIES` in base al tuo ambiente
- Se usi un load balancer o proxy, imposta `IP_ACCESS_TRUST_PROXIES=true`
- Se l'applicazione è direttamente esposta, imposta `IP_ACCESS_TRUST_PROXIES=false` per evitare spoofing

## Licenza

MIT License. Vedi il file [LICENSE](LICENSE) per maggiori dettagli.

## Autore

Roberto Boschin

## Supporto

Per bug, richieste di funzionalità o domande, apri una issue su GitHub.
