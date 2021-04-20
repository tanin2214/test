<?php

require_once ('StorageInterface.php');


class FtpStorage implements StorageInterface
{
    const TIMEOUT = 30;

    private string $ftpHost;
    private string $ftpLogin;
    private string $ftpPassword;
    private string $ftpDir;
    private int $ftpPort;
    private $connection;

    public function __construct(array $ftpInfo)
    {
        $this->ftpHost = $ftpInfo['ftpHost'];
        $this->ftpLogin = $ftpInfo['ftpLogin'];
        $this->ftpPassword = $ftpInfo['ftpPassword'];
        $this->ftpDir = $ftpInfo['ftpDir'];
        $this->ftpPort = $ftpInfo['ftpPort'];
    }

    public function save(string $fileFromPath): void
    {
        $this->openConnection();
        $this->login();
        $this->uploadFileToServer($fileFromPath);
    }

    private function openConnection(): void
    {
        $this->connection = ftp_connect($this->ftpHost, $this->ftpPort, self::TIMEOUT);
    }

    private function login(): void
    {
        ftp_login($this->connection, $this->ftpLogin, $this->ftpPassword);
    }

    private function uploadFileToServer(string $fileFromPath): void
    {
        $ret = ftp_nb_put(
            $this->connection,
            $this->ftpDir . $this->getFileNameFromPath($fileFromPath),
            $fileFromPath
        );

        while ($ret == FTP_MOREDATA) {
            $ret = ftp_nb_continue($this->connection);
        }

        if ($ret != FTP_FINISHED) {
            echo "При загрузке файла произолшла ошибка...";
            throw new Exception('upload file error', 500);
        }
    }

    private function getFileNameFromPath(string $fileFromPath): string
    {
        return basename($fileFromPath);
    }

}