<?php

require_once(dirname(__DIR__, 1) . '/vendor/autoload.php');
require_once(__DIR__ . '/Service/Storage/FtpStorage.php');
require_once(__DIR__ . '/Service/Storage/LocalStorage.php');
require_once(__DIR__ . '/Service/Storage/StorageCreatorFactory.php');
require_once(__DIR__ . '/Service/DataProvider/DataProvider.php');
require_once(__DIR__ . '/Service/XlsExchange.php');

/**
 *  Входящие параметры для сохранения на FTP-сервер
 *  Если $ftpInfo или один из параметров пустой,
 *  то сохранение будет происходить
 *  в директорию LocalStorageDirectory
 */
$ftpInfo = [
    'ftpHost'     => '',
    'ftpLogin'    => '',
    'ftpPassword' => '',
    'ftpDir'      => '',
    'ftpPort'     => 21,
];

$storage = StorageCreatorFactory::getStorage($ftpInfo);

$jsonSourcePath = dirname(__DIR__, 1) . '/order.json';

$jsonData = (new DataProvider())->getPreparedDataFrom($jsonSourcePath);

(new XlsExchange('test_export'))
    ->renderExcel($jsonData)
    ->saveFile($storage);
