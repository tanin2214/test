<?php


class DataProvider
{

    public function getPreparedDataFrom(string $sourcePath): array
    {
        $orderData = $this->getDataFromJsonBy($sourcePath);

        return $this->prepareItemData($orderData['items']);
    }

    private function getDataFromJsonBy(string $filePath): array
    {
        return json_decode(file_get_contents($filePath), true);
    }

    function prepareItemData(array $orderItems): array
    {
        $preparedData = [];

        foreach ($orderItems as $orderItem) {
            $preparedData[] = [
                "id"             => $orderItem['item']['id'],
                "barcode"        => $orderItem['item']['barcode'],
                "isBarcodeValid" => $this->isBarcodeValid($orderItem['item']['barcode']),
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

}