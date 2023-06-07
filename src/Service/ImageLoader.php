<?php 

namespace App\Service;

use Exception;

class ImageLoader 
{
  private $fileSizeLimit = 2 * 1024 * 1024; // 5Mb
  private $allowedTypes  = [
    'image/jpeg',
    'image/jpg',
    'image/png',
  ];
  private $theme;

  public function __construct($theme)
  {
    $this->theme = $theme;
  } 

  public function load ($files)
  {
    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/build/images/themes/' . $this->theme . '/';
    $images = $files['images'];
    $imagePaths = [];

    if (count($images['name']) > 5) {
      throw new Exception('Максимальное количество загружаемых файлов - 5');
    }

    for ($i = 0; $i < count($images['name']); $i++) {
      if ($images['error'][$i] !== 0) {
        throw new Exception('При загрузке ' . $i + 1 .  ' файла произошла ошибка');
      }

      $destName = $uploaddir . $images['name'][$i];
      if (!$this->moveFile($images['tmp_name'][$i], $destName)) {
        throw new Exception('При загрузке ' . $i + 1 .  ' файла произошла ошибка');
      }
  
      if (!$this->validateFile($images['type'][$i], $images['size'][$i], $this->fileSizeLimit, $this->allowedTypes)) {
        $this->removeFile($destName);
        throw new Exception('При загрузке ' . $i + 1 .  ' файла произошла ошибка');
      }
      $imagePaths[] = 'build/images/themes/' . $this->theme . '/' . $images['name'][$i];
    }  

    return $imagePaths;
  }

  // Helper functions
private function moveFile($from, $to)
  {
    return move_uploaded_file($from, $to);
  }

private function removeFile($name)
  {
    unlink($name);
  }

private function validateFile($type, $fileSize, $size, $typeList)
  {
    if (!$this->validateFileSize($fileSize, $size)) {
      return false;
    }

    return $this->validateFileType($type, $typeList);
  }

private function validateFileSize($fileSize, $maxSizeLimit)
  {
      return $fileSize <= $maxSizeLimit;
  }

private function validateFileType($type, $types)
  {
    return in_array(
      $type,
      $types,
      true // Strict
    );
  }

}