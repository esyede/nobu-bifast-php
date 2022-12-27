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

    public function setBeneficiaryBankCode($bankCode)
    {
        $this->beneficiaryBankCode = $bankCode;
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
        $data = [
            'amount' => $this->amount,
            'partnerReferenceNo' => $this->partnerReferenceNo,
            'beneficiaryBankCode' => $this->beneficiaryBankCode,
            'beneficiaryAccountName' => $this->beneficiaryAccountName,
            'customerReference' => $this->customerReference,
            'sourceAccountNo' => $this->sourceAccountNo,
            'transactionDate' => $this->transactionDate,
            'additionalInfo' => $this->additionalInfo,
        ];

        if ($this->beneficiaryAccountNo) {
            $data['beneficiaryAccountNo'] = $this->beneficiaryAccountNo;
        }

        return $data;
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
        $this->validate();

        $endpoint = '/v1.1/transfer-interbank';
        $payloads = $this->toArray();
        $payloads['amount'] = [
            'value' => $payloads['amount'],
            'currency' => 'IDR',
        ];

        return $this->request->post($endpoint, $payloads);
    }
}
