<?php

return [
    'admin_guids' => explode(',', env('DHCP_ADMIN_GUIDS', '')),

    'guid_names' => collect(explode(',', env('GUID_NAMES', '')))
        ->mapWithKeys(function (string $pair) {
            $parts = explode(':', $pair, 2);

            return count($parts) === 2 ? [$parts[0] => $parts[1]] : [];
        })
        ->all(),

    'alert_email' => env('DHCP_ALERT_EMAIL'),
    'alert_subject_prefix' => env('DHCP_ALERT_SUBJECT_PREFIX', 'DHCPMON : '),

    'allowed_ip_ranges' => env('DHCP_ALLOWED_IP_RANGES', '130\\.209|172\\.20'),

    'ssd_dns_servers' => env('DHCP_SSD_DNS_SERVERS', '130.209.16.85, 130.209.16.177'),

    'ipseen_url' => env('IPSEEN_URL'),
    'ipseen_api_key' => env('IPSEEN_API_KEY'),
];
