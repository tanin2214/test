<?php

error_reporting(E_ALL & ~E_DEPRECATED);
require_once (__DIR__.'/../vendor/autoload.php');
require_once (__DIR__.'/Service/Storage/FtpStorage.php');
require_once (__DIR__.'/Service/Storage/LocalStorage.php');
require_once (__DIR__.'/Service/XlsExchange.php');

$result = getJsonFromFile();

$ftpInfo = [
    'ftpHost'     => 'files.000webhost.com',
    'ftpLogin'    => 'ivantanin',
    'ftpPassword' => 'ivan6012',
    'ftpDir'      => '/',
    'ftpPort'     => 21,
];

$storage = getStorageClass($ftpInfo);

(new XlsExchange('test_export'))
    ->renderExcel($result)
    ->saveFile($storage);

function getJsonFromFile(): array
{
    $jsonFilePath = __DIR__.'/../order.json';
    $orderData = getJsonData($jsonFilePath);

    return prepareItemDataForReport($orderData['items']);
}

function getJsonData(string $fromFilePath): array
{
    return json_decode(file_get_contents($fromFilePath), true);
}

function prepareItemDataForReport(array $orderItems): array
{
    $preparedData = [];

    foreach ($orderItems as $orderItem) {
        $preparedData[] = [
            "id"             => $orderItem['item']['id'],
            "barcode"        => $orderItem['item']['barcode'],
            "isBarcodeValid" => isBarcodeValid($orderItem['item']['barcode']),
            "name"           => $orderItem['item']['name'],
            "quantity"       => $orderItem['quantity'],
            "amount"         => $orderItem['amount'],
        ];
    }

    return $preparedData;
}

function isBarcodeValid(string $barcodeToValidate): bool
{
    return boolval(clsLibGTIN::GTINCheck($barcodeToValidate));
}

function getStorageClass(array $ftpParams): StorageInterface
{
    if(validateFtpParams($ftpParams)){
        $storage = new FtpStorage($ftpParams);
    } else {
        $storage = new LocalStorage(
            __DIR__ . DIRECTORY_SEPARATOR . 'LocalStorageDirectory' . DIRECTORY_SEPARATOR
        );
    }

    return $storage;
}

function validateFtpParams(array $ftpParams): bool
{
    $isParamsValid = !empty($ftpParams);

    foreach ($ftpParams as $param) {
        if (empty($param)) {
            $isParamsValid = false;
        }
    }

    return $isParamsValid;
}