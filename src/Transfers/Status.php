<?php

namespace Esyede\NobuBifast\Transfers;

use Esyede\NobuBifast\Requests\Request;
use Esyede\NobuBifast\Exceptions\NobuBiFastException;

class Status
{
    private $request;
    private $serviceCode;
    private $amount;
    private $additionalInfo = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    public function setServiceCode($code)
    {
        $this->serviceCode = $code;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount . '.00';
        return $this;
    }

    // optional
    public function setAdditionalInfo(array $infos)
    {
        $this->additionalInfo = $infos;
        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */

    public function toArray()
    {
        return [
            'serviceCode' => $this->serviceCode,
            'amount' => $this->amount,
            'additionalInfo' => $this->additionalInfo,
        ];
    }

    public function toJson($jsonOptions = 0)
    {
        return json_encode($this->toArray(), $jsonOptions);
    }

    /*
    |--------------------------------------------------------------------------
    | Validator
    |--------------------------------------------------------------------------
    */

    private function validate()
    {
        $properties = $this->toArray();

        foreach ($properties as $key => $value) {
            if (! in_array($key, ['additionalInfo'])
            && (is_null($properties[$key]) || empty($properties[$key]))) {
                throw new NobuBiFastException(sprintf(
                    'The %s needs to be set before calling %s::get()',
                    $key,
                    __CLASS__
                ));
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Actual request
    |--------------------------------------------------------------------------
    */

    public function get()
    {
        $this->validate();

        $endpoint = '/v1.0/transfer/status';
        $payloads = $this->toArray();
        $payloads['amount'] = (object) [
            'value' => $payloads['amount'],
            'currency' => 'IDR',
        ];

        return $this->request->post($endpoint, $payloads);
    }
}
