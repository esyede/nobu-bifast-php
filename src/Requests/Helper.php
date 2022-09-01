<?php

namespace Esyede\NobuBifast\Requests;

use DateTime;
use DateTimeZone;

class Helper
{
    public static function makeSignature(
        $clientSecret,
        $httpMethod,
        $relativeURL,
        $accessToken,
        $dateAtom,
        array $payloads = []
    ) {
        $httpMethod = strtoupper($httpMethod);
        $relativeURL = '/' . trim($relativeURL, '/') . '/';
        $hash = '';

        if (count($payloads) > 0) {
            $hash = json_encode($payloads);
        }

        $hash = hash('sha256', $hash);
        $hash = strtolower($hash);
        $stringToSign = $httpMethod
            . ':' . $relativeURL
            . ':' . $accessToken
            . ':' . $hash
            . ':' . $dateAtom;

        return hash_hmac('sha512', $stringToSign, $clientSecret);
    }

    public static function getDateAtom($time = 'now', $timezone = 'Asia/Jakarta')
    {
        return (new DateTime($time, new DateTimeZone($timezone)))->format(DateTime::ATOM);
    }

    public static function makeBase64RsaSignature($data, $privateKey)
    {
        $generateSignature = openssl_sign(
            $data,
            $output,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (true !== $generateSignature) {
            return null;
        }

        return base64_encode($output);
    }

    public static function getClientIp()
    {
        $ip = '';

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = $ips[0];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        // localhost, ambil IP ISP
        if (in_array($ip, ['::1', '127.0.0.1'])) {
            $ip = file_get_contents('https://api.ipify.org');
        }

        return $ip;
    }
}
