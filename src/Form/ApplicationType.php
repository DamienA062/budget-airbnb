<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * DRY function pour avoir le placeholder d'un champs
     *
     * @param string $placeholder
     * @return Array
     */
    protected function getConfiguration($placeholder)
    {
        return ['attr' => ['placeholder' => $placeholder]];
    }
}