<?php

namespace App\Service\Themes;

use App\Interface\ThemesInterface;

class Php extends ThemesInterface
{

  public string $title = 'PHP и все что с ним связано';
  public string $description = 'Как же он популярен, этот ваш PHP! Давайте скажем о нем немного больше';
  public array $paragraphs = [
    'templates/themes/php/paragraph1.html.twig',
    'templates/themes/php/paragraph2.html.twig',
    'templates/themes/php/paragraph3.html.twig',
  ];
  public array $keywords = [
    ['языке', 'языка', 'языку', 'языком', 'языке', 'языки'],
    ['программирование', 'программирования', 'программированию', 'программированием', 'программировании', 'программирования'],
    ['баг', 'бага', 'багу', 'багом', 'баге', 'баги']
  ];
  public array $images = [
    'build/images/themes/php/php1.jpeg',
    'build/images/themes/php/php2.jpg',
    'build/images/themes/php/php3.jpeg',
  ];

}
