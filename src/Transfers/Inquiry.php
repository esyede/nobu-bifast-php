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

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */

   public function toArray()
   {
       return [
           'beneficiaryBankCode' => $this->beneficiaryBankCode,
           'beneficiaryAccountNo' => $this->beneficiaryAccountNo,
           'partnerReferenceNo' => $this->partnerReferenceNo,
           'additionalInfo' => [
                'sourceAccountBankId' => $this->sourceAccountBankId,
                'sourceAccountBankNo' => $this->sourceAccountBankNo,
                'proxyUser' => $this->proxyUser
            ]
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

       $endpoint = '/v1.0/account-inquiry/fast-payment';
       $payloads = $this->toArray();

       return $this->request->post($endpoint, $payloads);
   }
}
