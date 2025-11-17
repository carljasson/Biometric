<?php

namespace App\Helpers;

class GeoHelper
{
    public static function lookup($ip)
    {
        $response = @file_get_contents("http://ip-api.com/json/{$ip}");

        if (!$response) {
            return ['city' => 'Unknown', 'country' => 'Unknown'];
        }

        $data = json_decode($response, true);

        if ($data['status'] !== 'success') {
            return ['city' => 'Unknown', 'country' => 'Unknown'];
        }

        return [
            'city' => $data['city'] ?? 'Unknown',
            'country' => $data['country'] ?? 'Unknown'
        ];
    }
}
