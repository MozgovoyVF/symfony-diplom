<?php

namespace App\Provider;

class ArticleContentProvider 
{

  public function get(string $content, string $word)
  {
    if (!$word) return $content;
  
    $arr = explode(PHP_EOL, $content);
    $text = [];
    $last = $arr[0];

    for ($i = 0; $i < count($arr); $i++) {
      if (!$last) {
        $last = $arr[$i+1]; 
        continue;
      }
      if (!isset($arr[$i+1])) {
        $text[] = $last;
        break;
      }
      if (!$arr[$i+1]) {
        $text[] = $last;
        $last = $arr[$i+1]; 
         continue;
      }
      $last .= $arr[$i+1];
    }

    $resultArr = [];
    foreach ($text as $paragraph) {
      $arr = explode(' ', $paragraph);
      $randKey = array_rand($arr, 1);
      $resultArray = array_slice($arr, 0, $randKey, true) + ['' => '**' . $word . '**'] +  array_slice($arr, $randKey, count($arr) - 1, true);
      $result = implode(' ', $resultArray);
      $resultArr[] = $result;
    }
    $result = implode(PHP_EOL . PHP_EOL, $resultArr);

    return $result;
  }
}
