<?php

namespace App\Factory;

use App\Interface\ThemesInterface;
use App\Repository\ThemeRepository;

class ThemeFactory
{
    /** @var ThemeRepository */
    private $themeRepository;

    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param ThemesInterface $themesInterface
     * @return void
     */
    public function createThemeInterface(string $themeCode)
    {
        $themeEntity = $this->themeRepository->findOneBy(['code' => $themeCode]);

        return  new ('\\App\\Service\\Themes\\' . ucfirst($themeEntity->getName()))();

    }
}
