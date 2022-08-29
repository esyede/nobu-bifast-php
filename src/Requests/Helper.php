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
        $relativeURL = '/' . ltrim($relativeURL, '/');
        $hash = '';

        if (count($payloads) > 0) {
            ksort($payloads);
            $hash = json_encode($payloads);
            $hash = preg_replace('/[[:blank:]]+/', '', $hash);
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
}
