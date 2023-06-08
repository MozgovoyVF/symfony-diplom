<?php

namespace App\Service;

use App\Repository\ThemeRepository;

class ThemeService
{
    /**
   * @var ThemeRepository
   */
  private $themeRepository;

  public function __construct (ThemeRepository $themeRepository)
  {
     $this->themeRepository = $themeRepository;
  }

  public function getAll (): array
  {
    return $this->themeRepository->findAll();
  }
}