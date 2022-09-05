<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Esyede\NobuBifast\Requests\Config;
use Esyede\NobuBifast\Requests\Token;
use Esyede\NobuBifast\Requests\Request;

use Esyede\NobuBifast\Transfers\Transfer;
use Esyede\NobuBifast\Transfers\Status;
use Esyede\NobuBifast\Transfers\Inquiry;

$privateKeyFile = dirname(__DIR__) . '/private_key.pem';
$publicKeyFile = dirname(__DIR__) . '/public_key.pem';

$clientKey = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$partnerId = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$clientSecret = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

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

/*
|--------------------------------------------------------------------------
| Step 3: ambil access token + generate nomor referensi unik harian
|--------------------------------------------------------------------------
*/
$data = json_decode($token);
$accessToken = $data->response->decoded->accessToken;
$uniqueRefDaily = (new \DateTime('now', new \DateTimezone('Asia/Jakarta')))->format('His').uniqid(); // max 12 char

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
$transfer = (new Transfer($request))
    ->setPartnerReferenceNo(date('YmdHis'))
    ->setAmount(100000)
    ->setBeneficiaryBankCode('SIHBIDJ1')
    ->setBeneficiaryAccountNo('510654300')
    ->setBeneficiaryAccountName('ANUGERAH QUBA MANDIRI')
    ->setSourceAccountNo('10110889307')
    ->setAdditionalInfo([
        'beneficiaryAccountType' => 'SVGS',
        'beneficiaryType' => '01',
        'beneficiaryNat' => '032456378311000',
        'beneficiaryResStatus' => '01',
        'beneficiaryCityCode' => '2391',
        'sourceAccountBankId' => 'LFIBIDJI',
        'proxyUser' => 'testing@gmail.com',
    ])
    ->setCustomerReference(uniqid()) // opsional
    ->setTransactionDate((new \DateTime('now', new \DateTimezone('Asia/Jakarta')))->format('c'));

$tf = $transfer->get();
echo json_encode(json_decode($tf), JSON_PRETTY_PRINT);
exit;


//! Contoh req inquiry
$inquiry = (new Inquiry($request))
    ->setBeneficiaryBankCode('SIHBIDJ1')
    ->setBeneficiaryAccountNo('510654300')
    ->setPartnerReferenceNo(date('YmdHis'))
    ->setSourceAccountBankId('LFIBIDJ1')
    ->setSourceAccountBankNo('10110889307')
    ->setProxyUser('testing@gmail.com');

$inq = $inquiry->get();
echo json_encode(json_decode($inq), JSON_PRETTY_PRINT);
exit;


//! Contoh req cek status
$status = (new Status($request))
    ->setServiceCode('36')
    ->setOriginalPartnerReferenceNo('XXXXXXXXXXXXXXXXX') // sama dengan saat request API transfer
    ->setOriginalReferenceNo('XXXXXXXXXXXXX'); // sama dengan saat request API transfer

$s = $status->get();
echo json_encode(json_decode($s), JSON_PRETTY_PRINT);
exit;
