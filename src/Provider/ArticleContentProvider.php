<?php

namespace App\Provider;

use App\Repository\ThemeRepository;
use App\Service\ImageLoader;
use Exception;
use Twig\Environment;

class ArticleContentProvider
{
  /**
   * @var Environment
   */
  private $templating;
  /**
   * @var ThemeRepository
   */
  private $themeRepository;

  public function __construct(Environment $templating, ThemeRepository $themeRepository)
  {
    $this->templating = $templating;
    $this->themeRepository = $themeRepository;
  }

  /** Создание статьи по заданным параметрам */
  public function get($data, $files): array
  {
    $themeEntity = $this->themeRepository->findOneBy(['code' => $data['theme']]);
    $themeClass = '\\App\\Service\\Themes\\' . ucfirst($themeEntity->getName());
    $theme = new $themeClass();
    /** @var ImageLoader $imageLoader */
    $imageLoader = new ImageLoader($data['theme']);

    //Производим загрузку картинок либо выдаем предупреждение
    if ($files['images']['name'][0]) {
      try {
        $images = $imageLoader->load($files);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
    } else {
      $images = $theme->images;
    }

    $title = ($data['title'] == '') ? $theme->title : $data['title'];

    //При отсутствии ключевого слова - выбираем его из указанной темы
    if (count($data['keywords']) === 0) {
      $randKey = array_rand($theme->keywords, 1);
      $keywords = $theme->keywords[$randKey];
    }

    $words = [];
    $wordsCount = [];

    // Создаем удобные для использования массивы слов и их количества
    for ($i = 0; $i < count($data['word']); $i++) {
      if ($data['word'][$i] != '') {
        $words[] = $data['word'][$i];
        if ($data['wordCount'][$i] == '') {
          $wordsCount[] = 1;
        } else {
          $wordsCount[] = (int) $data['wordCount'][$i];
        }
      }
    }

    //Для подписки free заполняем словоформы именительным падежом
    if (count($data['keywords']) < 7) {
      $keywords = [$data['keywords'][0]];
      for ($i = 0; $i < 6; $i++) {
        $keywords[] = $data['keywords'][0];
      }
    } else {
      $keywords = $data['keywords'];
    }

    // Определяем количество модулей из параметров пользователя
    $count = ($data['size'][1] != '') ? (int) rand($data['size'][0], $data['size'][1]) : (int) $data['size'][0];
    $count = ($count == 0) ?? 1;

    //Создаем контент
    $content = implode(PHP_EOL . PHP_EOL, $this->createContent($title, $theme->paragraphs, $keywords, $count, $images, $words, $wordsCount));

    return [
      'title' => $title,
      'description' => mb_substr($theme->description, 0, 30) . '...',
      'content' => $content
    ];
  }

  /** Создание контента статьи по заданным параметрам */
  private function createContent($title, $paragraphs, $keywords, $count, $images, $words, $wordsCount): array
  {
    $modules = scandir($_SERVER['DOCUMENT_ROOT'] . '/build/modules/');
    $addWords = array_fill(0, count($words), []);
    $nonRecurringImages = $images;

    for ($i = 0; $i < count($addWords); $i++) {
      for ($j = 0; $j < $count; $j++) {
        if (array_sum($addWords[$i]) < $wordsCount[$i]) {
          if ((ceil($wordsCount[$i] / $count) + array_sum($addWords[$i])) > $wordsCount[$i]) {
            $addWords[$i][$j] = $wordsCount[$i] - array_sum($addWords[$i]);
          } else {
            $addWords[$i][$j] = ceil($wordsCount[$i] / $count);
          }
        } else {
          $addWords[$i][$j] = 0;
        }
      }
    }

    $content = [$this->createTitle($title)];

    for ($i = 0; $i < $count; $i++) {
      $wordsInModule = [];
      $wordsCountInModule = [];
      for ($j = 0; $j < count($addWords); $j++) {
        $wordsCountInModule[] = $addWords[$j][$i];
        $wordsInModule[] = $words[$j];
      }

      $module = $modules[array_rand($modules, 1)];
      if (count($nonRecurringImages) !== 0) {
        $key = array_rand($nonRecurringImages, 1);
        $image = $nonRecurringImages[$key];
        array_splice($nonRecurringImages, $key, 1);
      } else {
        $image = $images[array_rand($images, 1)];
      }

      while (!str_ends_with($module, 'twig')) {
        $module = $modules[array_rand($modules, 1)];
      }

      $content[] = $this->templating->render('/public/build/modules/' . $module, [
        'paragraph' => $this->createParagraph($paragraphs, $keywords, $wordsInModule, $wordsCountInModule),
        'paragraphs' => $this->createParagraphs($paragraphs, $keywords, $wordsInModule, $wordsCountInModule),
        'title' => $title,
        'imageSrc' => $image,
      ]);
    }

    return $content;
  }

  /** Создание одного параграфа */
  private function createParagraph($paragraphs, $keywords, $wordsInModule, $wordsCountInModule)
  {
    $text = $this->templating->render($paragraphs[array_rand($paragraphs, 1)], [
      'keywords' => $keywords,
    ]);

    $arr = explode(' ', $text);

    $resultArray = $this->insertKeywords($arr, $wordsCountInModule, $wordsInModule,  $keywords);

    return implode(' ', $resultArray);
  }

  /** Создание от 1 до 3 параграфов */
  private function createParagraphs($paragraphs, $keywords, $wordsInModule, $wordsCountInModule): string
  {
    $count = rand(1, 3);
    $result = [];

    $wordsInParagraph = array_fill(0, $count, array_fill(0, count($wordsCountInModule), 0));

    for ($i = 0; $i < $count; $i++) {
      for ($j = 0; $j < count($wordsCountInModule); $j++) {
        $sum = 0;
        for ($k = 0; $k < count($wordsInParagraph); $k++) {
          $sum += $wordsInParagraph[$k][$j];
        }
        if ($sum < $wordsCountInModule[$j]) {
          if ((ceil($wordsCountInModule[$j] / $count) + $sum) > $wordsCountInModule[$j]) {
            $wordsInParagraph[$i][$j] = $wordsCountInModule[$j] - $sum;
          } else {
            $wordsInParagraph[$i][$j] = ceil($wordsCountInModule[$j] / $count);
          }
        } else {
          $wordsInParagraph[$i][$j] = 0;
        }
      }
    }

    for ($i = 0; $i < $count; $i++) {
      $paragraph = '<p>' . $this->templating->render($paragraphs[array_rand($paragraphs, 1)], [
        'keywords' => $keywords,
      ]) . '</p>';

      $arr = explode(' ', $paragraph);

      $resultArr = $this->insertKeywords($arr, $wordsInParagraph[$i], $wordsInModule,  $keywords);

      $result[] = implode(' ', $resultArr);
    }

    return implode(PHP_EOL . PHP_EOL, $result);
  }

  /** Создание заголовка статьи */
  private function createTitle($title): string
  {
    return $this->templating->render('/public/build/modules/title/title.html.twig', [
      'title' => $title,
    ]);
  }

  /** Вставка ключевых слов в массив контента */
  private function insertKeywords($arr, $wordsCountInModule, $wordsInModule,  $keywords): array
  {
    for ($j = 0; $j < count($wordsCountInModule); $j++) {

      for ($k = 0; $k < $wordsCountInModule[$j]; $k++) {
        $randKey = array_rand($arr, 1);
        for ($i = 0; $i < count($keywords); $i++) {
          if (stripos($arr[$randKey], $keywords[$i]) || ($randKey == 0) || ($randKey == count($arr) - 1)) {
            $i = 0;
            $randKey = array_rand($arr, 1);
          }
        }
        $arr = array_slice($arr, 0, $randKey, true) + ["$randKey" =>  $wordsInModule[$j] . ' ' . $arr[$randKey]] +  array_slice($arr, $randKey, count($arr) - 1, true);
      }
    }

    return $arr;
  }
}
