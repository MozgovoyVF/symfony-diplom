<?php

namespace App\Factory;

use App\Interface\ThemesInterface;

class Word implements ThemesInterface
{

    private string $title = 'Все о навыках, знаниях и словах';
    private string $description = 'Что мы знаем о словах и знаниях? Практически ничего! Нам стоит постараться ушлубиться в эту тему и раскрыть ее полностью';
    private array $paragraphs = [
        'templates/themes/word/paragraph1.html.twig',
        'templates/themes/word/paragraph2.html.twig',
        'templates/themes/word/paragraph3.html.twig',
    ];
    private array $keywords = [
        ['знание', 'знания', 'знанию', 'знанием', 'знании', 'знания'],
        ['навык', 'навыка', 'навыку', 'навыком', 'навыке', 'навыки'],
        ['слово', 'слова', 'слову', 'словом', 'слове', 'слова']
    ];
    private array $images = [
        'build/images/themes/word/word1.jpeg',
        'build/images/themes/word/word2.jpeg',
        'build/images/themes/word/word3.jpeg',
    ];

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
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
