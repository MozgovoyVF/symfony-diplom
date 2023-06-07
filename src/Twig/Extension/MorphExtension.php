<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AgoExtensionRuntime;
use App\Twig\Runtime\AppUploadedAsset;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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
