<?php

namespace App\Service\Themes;

use App\Interface\ThemesInterface;

class Word extends ThemesInterface
{

  public string $title = 'Все о навыках, знаниях и словах';
  public string $description = 'Что мы знаем о словах и знаниях? Практически ничего! Нам стоит постараться ушлубиться в эту тему и раскрыть ее полностью';
  public array $paragraphs = [
    'templates/themes/word/paragraph1.html.twig',
    'templates/themes/word/paragraph2.html.twig',
    'templates/themes/word/paragraph3.html.twig',
  ];
  public array $keywords = [
    ['знание', 'знания', 'знанию', 'знанием', 'знании', 'знания'],
    ['навык', 'навыка', 'навыку', 'навыком', 'навыке', 'навыки'],
    ['слово', 'слова', 'слову', 'словом', 'слове', 'слова']
  ];
  public array $images = [
    'build/images/themes/word/word1.jpeg',
    'build/images/themes/word/word2.jpeg',
    'build/images/themes/word/word3.jpeg',
  ];

}
