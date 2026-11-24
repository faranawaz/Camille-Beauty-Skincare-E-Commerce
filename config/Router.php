<?php

namespace BeautyStore\Config;

class Router
{
  private $routes = [];

  public function __construct($baseDir)
  {
    $this->routes = $this->getRoutesFromDirectory($baseDir);
    // Menambahkan halaman default
    $this->routes['default'] = 'page/home.php';
  }

  private function getRoutesFromDirectory($baseDir)
  {
    $routes = [];
    $directories = scandir($baseDir);

    foreach ($directories as $dir) {
      if ($dir === '.' || $dir === '..') {
        continue;
      }

      $subDir = $baseDir . '/' . $dir;
      if (is_dir($subDir)) {
        $files = scandir($subDir);
        foreach ($files as $file) {
          if ($file === '.' || $file === '..') {
            continue;
          }

          $filename = pathinfo($file, PATHINFO_FILENAME);
          if ($filename === 'index') {
            $filename = $dir;
          } else {
            $filename = $dir . '/' . $filename;
          }
          $routes[$filename] = $subDir . '/' . $file;
        }
      } else {
        $filename = pathinfo($dir, PATHINFO_FILENAME);
        $routes[$filename] = $baseDir . '/' . $dir;
      }
    }

    return $routes;
  }

  public function getRoute($page)
  {
    if (array_key_exists($page, $this->routes)) {
      return $this->routes[$page];
    }
    return $this->routes['default'];
  }

  public function getBaseUrl()
  {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    return $protocol . $host . '/';
  }
}
