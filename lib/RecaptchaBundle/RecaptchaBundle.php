<?php

namespace Damien\RecaptchaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * On modifie le container pour ajouter notre template
 */
class RecaptchaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        //instanciation de notre class qui ajoute notre template au tablea twig.form.resources
        $container->addCompilerPass(new RecaptchaCompilerPass());
    }
}