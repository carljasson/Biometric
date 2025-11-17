<?php

namespace App\Helpers;

class GeoHelper
{
    public static function lookup($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return [
                'city' => 'Localhost',
                'country' => 'Local Machine'
            ];
        }

        try {
            $json = @file_get_contents("https://ipinfo.io/{$ip}/json");

            if ($json) {
                $data = json_decode($json, true);

                return [
                    'city' => $data['city'] ?? 'Unknown',
                    'region' => $data['region'] ?? 'Unknown',
                    'country' => $data['country'] ?? 'Unknown'
                ];
            }
        } catch (\Exception $e) {
            // ignore errors
        }

        return [
            'city' => 'Unknown',
            'country' => 'Unknown'
        ];
    }
}
