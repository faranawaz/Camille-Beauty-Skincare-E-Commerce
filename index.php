<?php

require __DIR__ . '/vendor/autoload.php';

use BeautyStore\Config\Connection;
use BeautyStore\Config\Router;
use BeautyStore\Config\Database;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
  $connection = new Connection();
  $koneksi = $connection->koneksi;
  if ($connection->koneksi === null) {
    throw new Exception('Koneksi ke database gagal!');
  }

  $db = new Database($connection);
  $router = new Router('page');

  $uri = trim($_SERVER['REQUEST_URI'], '/');
  $baseUri = trim(parse_url($router->getBaseUrl(), PHP_URL_PATH), '/');

  $uriParts = explode('?', $uri);
  $path = $uriParts[0];

  $page = str_replace($baseUri, '', $path) ?: 'home';

  $route = $router->getRoute($page);

  define('BASE_URL', $router->getBaseUrl());
  define('APP_NAME', $_ENV['APP_NAME']);
  if (file_exists($route)) {
    include_once $route;
  } else {
    throw new Exception('Halaman tidak ditemukan!');
  }
} catch (Exception $e) {
  header("Location: error?message=" . urlencode($e->getMessage()) . "&file=" . urlencode($e->getFile()) . "&line=" . urlencode($e->getLine()));
  exit();
}
