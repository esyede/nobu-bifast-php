<?php

namespace Esyede\NobuBifast\Transfers;

use Esyede\NobuBifast\Requests\Request;
use Esyede\NobuBifast\Exceptions\NobuBiFastException;

class Status
{
    private $request;
    private $serviceCode;
    private $originalPartnerReferenceNo;
    private $originalReferenceNo;
    private $amount;

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

    public function setOriginalPartnerReferenceNo($ref)
    {
        $this->originalPartnerReferenceNo = $ref;
        return $this;
    }

    public function setOriginalReferenceNo($no)
    {
        $this->originalReferenceNo = $no;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount . '.00';
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
            'amount' => $this->amount,
            'serviceCode' => $this->serviceCode,
            'originalPartnerReferenceNo' => $this->originalPartnerReferenceNo,
            'originalReferenceNo' => $this->originalReferenceNo,
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
            if (is_null($properties[$key]) || empty($properties[$key])) {
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
        $payloads['amount'] = [
            'value' => $payloads['amount'],
            'currency' => 'IDR',
        ];

        return $this->request->post($endpoint, $payloads);
    }
}
