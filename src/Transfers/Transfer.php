<?php

namespace Esyede\NobuBifast\Transfers;

use Esyede\NobuBifast\Requests\Request;
use Esyede\NobuBifast\Exceptions\NobuBiFastException;

class Transfer
{
    private $request;
    private $amount;
    private $partnerReferenceNo;
    private $beneficiaryAccountName;
    private $beneficiaryAccountNo;
    private $customerReference;
    private $beneficiaryBankCode;
    private $sourceAccountNo;
    private $transactionDate;
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

    public function setPartnerReferenceNo($refNo)
    {
        $this->partnerReferenceNo = $refNo;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount . '.00';
        return $this;
    }

    public function setBeneficiaryAccountName($accountName)
    {
        $this->beneficiaryAccountName = $accountName;
        return $this;
    }

    public function setBeneficiaryAccountNo($accountNo)
    {
        $this->beneficiaryAccountNo = $accountNo;
        return $this;
    }

    public function setSourceAccountNo($sourceNo)
    {
        $this->sourceAccountNo = $sourceNo;
        return $this;
    }

    public function setTransactionDate($dateAtom)
    {
        $this->transactionDate = $dateAtom;
        return $this;
    }

    // optional
    public function setCustomerReference($reference)
    {
        $this->customerReference = $reference;
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
            'amount' => $this->amount,
            'partnerReferenceNo' => $this->partnerReferenceNo,
            'beneficiaryAccountName' => $this->beneficiaryAccountName,
            'beneficiaryAccountNo' => $this->beneficiaryAccountNo,
            'customerReference' => $this->customerReference,
            'beneficiaryBankCode' => $this->beneficiaryBankCode,
            'sourceAccountNo' => $this->sourceAccountNo,
            'transactionDate' => $this->transactionDate,
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
            if (! in_array($key, ['additionalInfo', 'customerReference'])
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
        $this->vallidate();

        $endpoint = '/v1.0/transfer/fast-payment';
        $payloads = $this->toArray();
        $payloads['amount'] = (object) [
            'value' => $payloads['amount'],
            'currency' => 'IDR',
        ];

        return $this->request->post($endpoint, $payloads);
    }
}
