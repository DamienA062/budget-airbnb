<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        /**
         * Création d'un nouveau filtre twig pour les longs textes
         */
        return [
            new TwigFilter('ellipsis', [$this, 'ellipsisFilter']),
        ];
    }

    /**
     * Fonction qui permet de tronquer un texte à partir d'une max length et de le finir par "..."
     *
     * @param [type] $text
     * @param integer $maxLen
     * @param string $ellipsis
     * @return void
     */
    public function ellipsisFilter($text, $maxLen = 50, $ellipsis = ' ...')
    {
        if(strlen($text) <= $maxLen)
        {
            return $text;
        }
        return substr($text, 0, $maxLen-3).$ellipsis;
    }
}