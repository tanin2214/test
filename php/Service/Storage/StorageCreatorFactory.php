<?php


class StorageCreatorFactory
{

    public static function getStorage(array $ftpParams): StorageInterface
    {
        if (self::validateFtpParams($ftpParams)) {
            $storage = new FtpStorage($ftpParams);
        } else {
            $storage = new LocalStorage(
                dirname(__DIR__, 2) .
                DIRECTORY_SEPARATOR .
                'LocalStorageDirectory' .
                DIRECTORY_SEPARATOR
            );
        }

        return $storage;
    }

    private static function validateFtpParams(array $ftpParams): bool
    {
        $isParamsValid = !empty($ftpParams);

        foreach ($ftpParams as $param) {
            if (empty($param)) {
                $isParamsValid = false;
                break;
            }
        }

        return $isParamsValid;
    }
}