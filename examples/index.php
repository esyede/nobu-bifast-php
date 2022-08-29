<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Esyede\NobuBifast\Requests\Config;
use Esyede\NobuBifast\Requests\Token;
use Esyede\NobuBifast\Requests\Request;

use Esyede\NobuBifast\Transfers\Transfer;
use Esyede\NobuBifast\Transfers\Status;
use Esyede\NobuBifast\Transfers\Inquiry;

$privateKeyFile = __DIR__ . '/../private_key.pem';
$publicKeyFile = __DIR__ . '/../public_key.pem';

$clientKey = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx1';
$partnerId = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
$clientSecret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
$signatureBase64 = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

/*
|--------------------------------------------------------------------------
| Step 1: Set config
|--------------------------------------------------------------------------
*/
$config = (new Config())
    ->setPrivateKeyFile($privateKeyFile)
    ->setPublicKeyFile($publicKeyFile)
    ->setClientKey($clientKey) // X-CLIENT-KEY
    ->setPartnerId($partnerId) // X-PARTNER-ID
    ->setClientSecret($clientSecret)
    ->setDevelopment(true);

/*
|--------------------------------------------------------------------------
| Step 2: grant token
|--------------------------------------------------------------------------
*/
$token = (new Token($config))->get(); // Json data berisi string bearer token

echo $token;
exit;

/*
|--------------------------------------------------------------------------
| Step 3: ambil access token + generate nomor referensi unik harian
|--------------------------------------------------------------------------
*/
$data = json_decode($token);
$accessToken = $data->accessToken;
$uniqueRefDaily = (new \DateTime('now', new \DateTimezone('Asia/Jakarta')))->format('YmdHi'); // max 12 char

/*
|--------------------------------------------------------------------------
| Step 4: Setup request
|--------------------------------------------------------------------------
*/
$request = new Request($config, $accessToken, $uniqueRefDaily);

/*
|--------------------------------------------------------------------------
| Sampai disini seharusnya sudah bisa request transfer
|--------------------------------------------------------------------------
*/

//! Contoh transfer
$transfer = (new Transfer())
    ->setPartnerReferenceNo('2022041309130002')
    ->setAmount(100000)
    ->setBeneficiaryAccountName('ANUGERAH QUBA MANDIRI')
    ->setBeneficiaryAccountNo('3604107554096')
    ->setSourceAccountNo('10110889307')
    // ->setAdditionalInfo(['foo' => 'bar']) // opsional
    // ->setCustomerReference('T00000001') // opsional
    ->setTransactionDate('2022-04-13T09:08:56-07:00');

echo $transfer->get();
exit;


//! Contoh req inquiry
$inquiry = (new Inquiry())
    ->setBeneficiaryBankCode('SIHBIDJ1')
    ->setBeneficiaryAccountNo('11234567890')
    ->setPartnerRefNo('202010290000000NOB0017')
    ->setAmount(100000)
    // ->setSourceAccountBankNo('10110889307') // optional
    // ->setAdditionalInfo(['foo' => 'bar']) // optional
    ->setSourceAccountBankId('LFIBIDJ1');

echo $inquiry->get();
exit;


//! Contoh req cek status
$status = (new Status())
    ->setServiceCode('36')
    // ->setAdditionalInfo(['foo' => 'bar']) // optional
    ->setAmount(100000);

echo $status->get();
exit;
