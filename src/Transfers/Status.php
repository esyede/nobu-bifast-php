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

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */

    public function toArray()
    {
        return [
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

        return $this->request->post($endpoint, $payloads);
    }
}
