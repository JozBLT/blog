<?php

namespace App\Blog;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DemoExtension extends AbstractExtension
{

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('demo', [$this, 'demo'])
        ];
    }

    public function demo(): string
    {
        return 'Hey';
    }
}
