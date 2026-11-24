<?php

namespace BeautyStore\Config;

class Connection
{
    private $host;
    private $user;
    private $password;
    private $database;
    private $port;
    public $koneksi;

    public function __construct()
    {
        // Credentials are loaded from the server's local .env file.
        // Never put real database credentials in source code.
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->user = $_ENV['DB_USER'] ?? '';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->database = $_ENV['DB_NAME'] ?? '';
        $this->port = (int) ($_ENV['DB_PORT'] ?? 3306);

        $this->koneksi = $this->connect();
    }

    private function connect()
    {
        if ($this->user === '' || $this->database === '') {
            error_log('Database configuration is incomplete. Check the server .env file.');
            return null;
        }

        $koneksi = @mysqli_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );

        if (!$koneksi) {
            error_log('Database connection failed: ' . mysqli_connect_error());
            return null;
        }

        return $koneksi;
    }
}
