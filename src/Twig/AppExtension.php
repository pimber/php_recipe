<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;



class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('truncateString', [$this, 'truncateString']),
        ];
    }

    public function truncateString($string, $length)
    {
        if (mb_strlen($string, 'UTF-8') > $length) {
            $string = mb_substr($string, 0, $length, 'UTF-8');
        }
        return $string;
    }

}