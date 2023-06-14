<?php

namespace App\Interface;

interface ThemesInterface
{
    public function getTitle(): string;
    public function setTitle(string $title): self;

    public function getDescription(): string;
    public function setDescription(string $description): self;

    public function getKeywords(): array;
    public function setKeywords(array $keywords): self;

    public function getParagraphs(): array;
    public function setParagraphs(array $paragraph): self;

    public function getImages(): array;
    public function setImages(array $images): self;
}
