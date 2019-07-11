<?php

namespace Damien\RecaptchaBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Cette classe va permettre d'ajouter un template dans le tableau (php bin/console deebug:container --parameter=twig.form.resources)
 *  que twig doit charger
 */
class RecaptchaCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //si notre container a le param twig.form.resources
        if($container->hasParameter('twig.form.resources'))
        {
            //si oui on récup les params dans une variable (si il est vide on le définit sous forme de tableau vide)
            $resources = $container->getParameter('twig.form.resources') ? : [];

            //on ajoute à ce tableau un nouveau chemin qui sera 
            array_unshift($resources, '@Recaptcha/fields.html.twig');

            //maintenant que le tableau est modifié, dans le container on lui dit de redéfinir twig.form.resources en y ajoutant notre variable $resources
            $container->setParameter('twig.form.resources', $resources);
        }
    }
}