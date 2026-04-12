<?php

namespace Rboschin\LaravelIpAccess\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rboschin\LaravelIpAccess\Models\IpAccessWhite;
use Rboschin\LaravelIpAccess\Models\IpAccessBlack;

class CheckIpAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $mode = config('ip-access.mode');
        $clientIp = $this->getClientIp($request);

        // Check access based on mode
        if ($mode === 'whitelist') {
            $whitelist = $this->getWhitelist();
            if (!$this->isIpAllowed($clientIp, $whitelist)) {
                return $this->denyAccess();
            }
        } elseif ($mode === 'blacklist') {
            $blacklist = $this->getBlacklist();
            if ($this->isIpAllowed($clientIp, $blacklist)) {
                return $this->denyAccess();
            }
        }

        return $next($request);
    }

    /**
     * Get the whitelist from the configured source.
     *
     * @return array
     */
    protected function getWhitelist(): array
    {
        $source = config('ip-access.whitelist_source', '.env');
        
        if ($source === '.env') {
            return config('ip-access.whitelist', []);
        } elseif ($source === 'IpAccessWhite') {
            return IpAccessWhite::getActiveIpAddresses();
        }
        
        return [];
    }

    /**
     * Get the blacklist from the configured source.
     *
     * @return array
     */
    protected function getBlacklist(): array
    {
        $source = config('ip-access.blacklist_source', '.env');
        
        if ($source === '.env') {
            return config('ip-access.blacklist', []);
        } elseif ($source === 'IpAccessBlack') {
            return IpAccessBlack::getActiveIpAddresses();
        }
        
        return [];
    }

    /**
     * Get the client IP address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getClientIp(Request $request)
    {
        if (config('ip-access.trust_proxies') && $request->header('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            return trim($ips[0]);
        }

        return $request->ip();
    }

    /**
     * Check if an IP address is in the allowed list.
     *
     * @param  string  $ip
     * @param  array  $allowedIps
     * @return bool
     */
    protected function isIpAllowed($ip, array $allowedIps)
    {
        if (empty($allowedIps)) {
            return false;
        }

        foreach ($allowedIps as $allowedIp) {
            if ($this->matchIp($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match an IP address against a pattern (supports wildcards).
     *
     * @param  string  $ip
     * @param  string  $pattern
     * @return bool
     */
    protected function matchIp($ip, $pattern)
    {
        // Exact match
        if ($ip === $pattern) {
            return true;
        }

        // Wildcard support (e.g., 192.168.1.*)
        if (strpos($pattern, '*') !== false) {
            $regex = '/^' . str_replace(['.', '*'], ['\.', '.*'], $pattern) . '$/';
            return preg_match($regex, $ip) === 1;
        }

        // CIDR notation support (e.g., 192.168.1.0/24)
        if (strpos($pattern, '/') !== false) {
            return $this->matchCidr($ip, $pattern);
        }

        return false;
    }

    /**
     * Match an IP address against a CIDR range.
     *
     * @param  string  $ip
     * @param  string  $cidr
     * @return bool
     */
    protected function matchCidr($ip, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
            !filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    /**
     * Deny access and return a 403 Forbidden response.
     *
     * @return \Illuminate\Http\Response
     */
    protected function denyAccess()
    {
        $message = config('ip-access.forbidden_message', 'Access denied.');
        
        return response($message, 403);
    }
}
