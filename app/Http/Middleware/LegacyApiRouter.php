<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyApiRouter
{
    private const ACTION_MAP = [
        'api_checkupdates' => '/api/dhcp/check-updates',
        'api_getdhcp' => '/api/dhcp/config',
        'api_gethosts' => '/api/dhcp/hosts',
        'api_flagerror' => '/api/dhcp/flag-error',
    ];

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $action = $request->query('action');

        if (! $action) {
            return $next($request);
        }

        if ($action === 'api_setonline' && $request->query('id')) {
            return redirect('/api/dhcp/hosts/'.$request->query('id').'/online');
        }

        if ($action === 'api_setoffline' && $request->query('id')) {
            return redirect('/api/dhcp/hosts/'.$request->query('id').'/offline');
        }

        if (isset(self::ACTION_MAP[$action])) {
            $query = $request->except(['action']);

            return redirect(self::ACTION_MAP[$action].($query ? '?'.http_build_query($query) : ''));
        }

        return $next($request);
    }
}
