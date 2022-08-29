<?php

namespace Esyede\NobuBifast\Requests;

use ReflectionClass;

class Config
{
    private $devBaseUrl;
    private $liveBaseUrl;

    private $privateKeyFile;
    private $publicKeyFile;
    private $base64Signature;
    private $clientKey;
    private $clientSecret;
    private $partnerId;
    private $isDevelopment = true;

    public function __construct($devBaseUrl = null, $liveBaseUrl = null)
    {
        $this->devBaseUrl = is_null($devBaseUrl) ? 'https://sandbox.nobubank.com:8065' : rtrim($devBaseUrl, '/');
        $this->liveBaseUrl = is_null($liveBaseUrl) ? 'https://nobubank.com:8065' : rtrim($liveBaseUrl, '/');
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    public function setPrivateKeyFile($path)
    {
        $this->privateKeyFile = $path;
        return $this;
    }

    public function setPublicKeyFile($path)
    {
        $this->publicKeyFile = $path;
        return $this;
    }

    public function setClientKey($keyString)
    {
        $this->clientKey = $keyString;
        return $this;
    }

    public function setPartnerId($partnerIdString)
    {
        $this->partnerId = $partnerIdString;
        return $this;
    }

    public function setClientSecret($clientSecretString)
    {
        $this->clientSecret = $clientSecretString;
        return $this;
    }

    public function setBase64Signature($base64SignatureString)
    {
        $this->base64Signature = $base64SignatureString;
        return $this;
    }

    public function setDevelopment($state = true)
    {
        $this->isDevelopment = boolval($state);
        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */
    public function getPrivateKeyFile()
    {
        return $this->privateKeyFile;
    }

    public function getPublicKeyFile()
    {
        return $this->publicKeyFile;
    }

    public function getClientKey()
    {
        return $this->clientKey;
    }

    public function getPartnerId()
    {
        return $this->partnerId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getBase64Signature()
    {
        return $this->base64Signature;
    }

    public function isDevelopment()
    {
        return $this->isDevelopment;
    }

    public function getBaseUrl()
    {
        return $this->isDevelopment() ? $this->devBaseUrl : $this->liveBaseUrl;
    }

    public function toArray()
    {
        $this->validate();

        return [
            'privateKeyFile' => $this->privateKeyFile,
            'publicKeyFile' => $this->publicKeyFile,
            'base64Signature' => $this->base64Signature,
            'clientKey' => $this->clientKey,
            'clientSecret' => $this->clientSecret,
            'partnerId' => $this->partnerId,
            'isDevelopment' => $this->isDevelopment,
            'devBaseUrl' => $this->devBaseUrl,
            'liveBaseUrl' => $this->liveBaseUrl,
        ];
    }

    public function toObject()
    {
        return (object) $this->toArray();
    }

    public function toJson($jsonOptions = 0)
    {
        return json_encode($this->toArray(), $jsonOptions);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validate()
    {
        $properties = (new ReflectionClass(__CLASS__))->getProperties();

        foreach ($properties as $property) {
            if (null === $this->{$property->name}) {
                throw new Exceptions\NobuBiFastException(sprintf(
                    'The %s config needs to be set before using this library',
                    $property->name
                ));
            }
        }
    }
}
