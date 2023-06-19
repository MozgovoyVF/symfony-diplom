<?php

namespace App\Interface;

interface ThemesInterface
{
    /**
     * @param string $paragraph
     * @return array
     */
    public function addParagraph(string $paragraph): array;
    /**
     * @param array $keywords
     * @return array
     */
    public function addKeywords(array $keywords): array;
    /**
     * @param string $image
     * @return array
     */
    public function addImages(string $image): array;
}
