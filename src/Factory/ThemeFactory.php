<?php

namespace App\Factory;

use App\Interface\ThemesInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ThemeFactory
{
    /** @var ContainerBagInterface */
    private ContainerBagInterface $containerBag;

    public function __construct(ContainerBagInterface $containerBag)
    {
        $this->containerBag = $containerBag;
    }

    /**
     * @param string $themeCode
     * @return ThemesInterface
     */
    public function createThemeInterface(string $themeCode): ThemesInterface
    {
        switch ($themeCode) {
            case $this->containerBag->get('PHP'):
                return new Php();
            case $this->containerBag->get('WORD'):
                return new Word();
        }
    }
}
