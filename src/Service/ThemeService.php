<?php

namespace App\Service;

use App\Repository\ThemeRepository;

class ThemeService
{
    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * @return array
     */
    public function getLastThemes(): array
    {
        return $this->themeRepository->findBy([], ['name' => 'ASC'], 10);
    }
}
