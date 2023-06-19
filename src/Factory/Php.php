<?php

namespace App\Factory;

use App\Interface\ThemesInterface;

class Php implements ThemesInterface
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

    /**
     * @param string $paragraph
     * @return string
     */
    public function addParagraph(string $paragraph): array
    {
        $this->paragraphs[] = $paragraph;

        return $this->paragraphs;
    }

    /**
     * @param array $keyword
     * @return array
     */
    public function addKeywords(array $keyword): array
    {
        $this->keywords[] = $keyword;

        return $this->keywords;
    }

    /**
     * @param string $image
     * @return array
     */
    public function addImages(string $image): array
    {
        $this->images[] = $image;

        return $this->images;
    }
}
