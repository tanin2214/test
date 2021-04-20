<?php


interface StorageInterface
{

    public function save(string $fileFromPath): void;

}