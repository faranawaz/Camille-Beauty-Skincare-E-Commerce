<?php

namespace BeautyStore\Config;

use BeautyStore\Config\Connection;

class Database
{
  private $koneksi;

  public function __construct(Connection $connection)
  {
    $this->koneksi = $connection->koneksi;
  }

  // Menampilkan Jumlah Data ( Tabel )
  public function numData($tabel)
  {
    $num = mysqli_query($this->koneksi, "SELECT * FROM $tabel");
    return mysqli_num_rows($num);
  }

  // Menampilkan Jumlah Data ( Query )
  public function numQueryData($query)
  {
    $num = mysqli_query($this->koneksi, $query);
    return mysqli_num_rows($num);
  }

  // Menampilkan Data ( Query )
  public function viewData($query)
  {
    $result = mysqli_query($this->koneksi, $query);

    // Jika hasilnya hanya 1 data
    if (mysqli_num_rows($result) == 1) {
      return mysqli_fetch_assoc($result);
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  // Menampilkan Semua Data ( Tabel )
  public function viewAllData($tabel)
  {
    $query = "SELECT * FROM $tabel ORDER BY id DESC";
    $result = mysqli_query($this->koneksi, $query);

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  // Menambahkan Data
  public function addData($tabel, $insert)
  {
    $query = "INSERT INTO $tabel VALUES ($insert)";
    if (!mysqli_query($this->koneksi, $query)) {
      die("Query Error: " . mysqli_error($this->koneksi));
    }
    return mysqli_affected_rows($this->koneksi);
  }

  // Mengedit Data
  public function updateData($tabel, $update, $id)
  {
    $query = "UPDATE $tabel SET $update WHERE id = '$id'";
    if (!mysqli_query($this->koneksi, $query)) {
      die("Query Error: " . mysqli_error($this->koneksi));
    }
    return mysqli_affected_rows($this->koneksi);
  }

  // Menghapus Data ( Tabel, Id )
  public function deleteData($tabel, $id)
  {
    mysqli_query($this->koneksi, "DELETE FROM $tabel WHERE id = '$id'") or die(mysqli_error($this->koneksi));
    return mysqli_affected_rows($this->koneksi);
  }

  // Query Data ( Query )
  public function queryData($query)
  {
    $result = mysqli_query($this->koneksi, $query);
    if (!$result) {
      die("Query Error: " . mysqli_error($this->koneksi));
    }
    return mysqli_affected_rows($this->koneksi);
  }

  // Membuat Kode Otomatis
  public function autoId($tabel, $char = 'AKD')
  {
    $query = "SELECT max(id) as maxKode FROM $tabel";
    $hasil = mysqli_query($this->koneksi, $query);
    $data = mysqli_fetch_array($hasil);
    $kode = $data['maxKode'];
    $noUrut = (int) substr($kode, 3, 3);

    $noUrut++;

    $huruf = $char;
    $kode = $huruf . sprintf("%03s", $noUrut);
    return $kode;
  }

  // Mengupload Gambar ( Lokasi Upload )
  public function uploadImage($uploadLocation)
  {
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $error    = $_FILES['image']['error'];
    $fileTmp  = $_FILES['image']['tmp_name'];

    // Cek apakah yang di upload adalah gambar
    $fileExtensionValid = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension      = explode('.', $fileName);
    $fileExtension      = strtolower(end($fileExtension));
    if (!in_array($fileExtension, $fileExtensionValid)) {
      echo "<script>alert('What you upload is not an image !!!');</script>";
      return false;
    }

    // Cek apakah ukuran gambar lebih besar dari 3 MB
    if ($fileSize > 3048000) {
      echo "<script>alert('Image size is more than 3 MB !!!');</script>";
      return false;
    }

    // Nama file baru
    $fileNewName = rand(0, 999) . $_FILES['image']['name'];

    // Upload gambar
    $uploadDir = "./$uploadLocation/";
    if (is_uploaded_file($fileTmp)) {
      $result = move_uploaded_file($fileTmp, $uploadDir . $fileNewName);
      if ($result) {
        return $fileNewName;
      } else {
        echo "<script>alert('Image upload filed !!!');</script>";
      }
    }
  }
}
