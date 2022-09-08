<?php

namespace Esyede\NobuBifast\Requests;

class Token
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get()
    {
        $endpoint = '/v2.0/access-token/b2b';
        $dateAtom = Helper::getDateAtom();
        $strSign = $this->config->getClientKey() . '|' . $dateAtom;
        $privateKey = file_get_contents($this->config->getPrivateKeyFile());
        $base64RsaSignature = Helper::makeBase64RsaSignature(
            $strSign,
            $privateKey
        );
        $body = [
            'grantType' => 'client_credentials',
            'additionalInfo' => [
                'partnerId' => $this->config->getPartnerId(),
            ],
        ];

        $headers = [
            'Content-Type: application/json',
            'X-TIMESTAMP: ' . $dateAtom,
            'X-CLIENT-KEY: ' . $this->config->getClientKey(),
            'X-SIGNATURE: ' . $base64RsaSignature,
        ];

        $endpoint = $this->config->getBaseUrl() . $endpoint;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_HEADER => false,
            CURLOPT_FAILONERROR => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($body),
        ]);

        if ($this->config->isDevelopment()) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        $raw = curl_exec($ch);
        $errors = curl_error($ch);
        curl_close($ch);

        $decoded = json_decode($raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $decoded = false;
            $errors = $errors ?: 'Unable to decode json response.';
        }

        $results = [
            'endpoint' => $endpoint,
            'method' => 'POST',
            'headers' => $headers,
            'body' => $body,
            'response' => [
                'decoded' => $decoded,
                'raw' => $raw,
                'errors' => $errors,
            ],
            'config' => $this->config->toArray(),
        ];

        return json_encode($results);
    }
}
