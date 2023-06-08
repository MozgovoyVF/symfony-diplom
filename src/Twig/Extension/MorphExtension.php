<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MorphExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [$this, 'keywordMorph']),
        ];
    }

    public function keywordMorph($keyword, $morphCase)
    {
        return $keyword[$morphCase];
    }
}
