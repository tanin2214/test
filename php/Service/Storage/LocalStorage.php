<?php


require_once ('StorageInterface.php');

class LocalStorage implements StorageInterface
{
    private string $localStoragePath;

    public function __construct(string $localStoragePath)
    {
        $this->localStoragePath = $localStoragePath;
    }

    public function save(string $fileFromPath): void
    {
        copy($fileFromPath, $this->localStoragePath . basename($fileFromPath));
    }

}