<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MorphExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [$this, 'keywordMorph']),
        ];
    }

    /**
     * @param array @keyword
     * @param int @morphCase
     * @return string
     */
    public function keywordMorph($keyword, $morphCase): string
    {
        return $keyword[$morphCase];
    }
}
