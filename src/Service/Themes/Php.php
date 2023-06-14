<?php

namespace App\Service\Themes;

use App\Interface\ThemesInterface;

class Php implements ThemesInterface
{

    private string $title = 'PHP и все что с ним связано';
    private string $description = 'Как же он популярен, этот ваш PHP! Давайте скажем о нем немного больше';
    private array $paragraphs = [
        'templates/themes/php/paragraph1.html.twig',
        'templates/themes/php/paragraph2.html.twig',
        'templates/themes/php/paragraph3.html.twig',
    ];
    private array $keywords = [
        ['языке', 'языка', 'языку', 'языком', 'языке', 'языки'],
        ['программирование', 'программирования', 'программированию', 'программированием', 'программировании', 'программирования'],
        ['баг', 'бага', 'багу', 'багом', 'баге', 'баги']
    ];
    private array $images = [
        'build/images/themes/php/php1.jpeg',
        'build/images/themes/php/php2.jpg',
        'build/images/themes/php/php3.jpeg',
    ];

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return self
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }
    public function setKeywords($keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getParagraphs(): array
    {
        return $this->paragraphs;
    }
    public function setParagraphs($paragraphs): self
    {
        $this->paragraphs = $paragraphs;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }
    public function setImages($images): self
    {
        $this->images = $images;

        return $this;
    }
}
