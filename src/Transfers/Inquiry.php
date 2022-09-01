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
    private $additionalInfo = [];
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

    public function setAmount($amount)
    {
        $this->amount = $amount . '.00';
        return $this;
    }

    public function setSourceAccountBankId($bankId)
    {
        $this->sourceAccountBankId = $bankId;
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
           'beneficiaryBankCode' => $this->beneficiaryBankCode,
           'beneficiaryAccountNo' => $this->beneficiaryAccountNo,
           'partnerReferenceNo' => $this->partnerReferenceNo,
           'sourceAccountBankId' => $this->sourceAccountBankId,
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

       $endpoint = '/v1.0/account-inquiry/fast-payment';
       $payloads = $this->toArray();
       $payloads['amount'] = (object) [
           'value' => $payloads['amount'],
           'currency' => 'IDR',
       ];

       return $this->request->post($endpoint, $payloads);
   }
}
