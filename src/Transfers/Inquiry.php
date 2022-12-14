<?php

namespace Esyede\NobuBifast\Transfers;

use Esyede\NobuBifast\Requests\Request;
use Esyede\NobuBifast\Exceptions\NobuBiFastException;

class Inquiry
{
    private $request;
    private $beneficiaryBankCode;
    private $beneficiaryAccountNo;
    private $partnerReferenceNo;
    private $sourceAccountBankId;
    private $sourceAccountBankNo;
    private $proxyUser = null;
    private $amount = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    public function setBeneficiaryBankCode($bankCode)
    {
        $this->beneficiaryBankCode = $bankCode;
        return $this;
    }

    public function setBeneficiaryAccountNo($accountNo)
    {
        $this->beneficiaryAccountNo = $accountNo;
        return $this;
    }

    public function setPartnerReferenceNo($refNo)
    {
        $this->partnerReferenceNo = $refNo;
        return $this;
    }

    public function setAdditionalInfo(array $infos)
    {
        $this->additionalInfo = $infos;
        return $this;
    }

    public function setSourceAccountBankId($bankId)
    {
        $this->sourceAccountBankId = $bankId;
        return $this;
    }

    public function setSourceAccountBankNo($bankNo)
    {
        $this->sourceAccountBankNo = $bankNo;
        return $this;
    }

    public function setProxyUser($user)
    {
        $this->proxyUser = $user;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
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
           'beneficiaryBankCode' => $this->beneficiaryBankCode,
           'partnerReferenceNo' => $this->partnerReferenceNo,
           'additionalInfo' => [
                'sourceAccountBankId' => $this->sourceAccountBankId,
                'sourceAccountBankNo' => $this->sourceAccountBankNo,
                'amount' => [
                    'value' => number_format($this->amount, 2, '.', ''),
                    'currency' => 'IDR'
                ]
            ],

       ];

       if ($this->beneficiaryAccountNo) {
            $data['beneficiaryAccountNo'] = $this->beneficiaryAccountNo;
       }

       if ($this->proxyUser) {
            $data['additionalInfo']['proxyUser'] = $this->proxyUser;
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

       $endpoint = '/v1.1/account-inquiry-external';
       $payloads = $this->toArray();

       return $this->request->post($endpoint, $payloads);
   }
}
