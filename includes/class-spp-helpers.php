<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SPP_Helpers {

    public static function get_public_ip() {
        $public_ip_services = array(
            'https://api.ipify.org',
            'https://ipecho.net/plain',
            'https://icanhazip.com',
            'https://ident.me'
        );

        foreach ($public_ip_services as $service) {
            $ip = self::fetch_ip_from_service($service);
            if ($ip) {
                return $ip;
            }
        }

        return self::get_ip_from_server();
    }

    private static function fetch_ip_from_service($url) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 seconds timeout
            $ip = curl_exec($ch);
            curl_close($ch);
        } else {
            $ip = @file_get_contents($url);
        }

        return (filter_var($ip, FILTER_VALIDATE_IP) !== false) ? trim($ip) : false;
    }

    private static function get_ip_from_server() {
        $headers = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($headers as $header) {
            if (array_key_exists($header, $_SERVER)) {
                $ip_list = explode(',', $_SERVER[$header]);
                foreach ($ip_list as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return 'UNKNOWN';
    }
}
