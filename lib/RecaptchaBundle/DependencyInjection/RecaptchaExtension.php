<?php

namespace Damien\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RecaptchaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config') //spécifie le chemin vers le fichier
        );
        $loader->load('services.yaml'); //on charge notre config
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //Ces params pourront être injectés dans le système de service
        $container->setParameter('recaptcha.key', $config['key']);
        $container->setParameter('recaptcha.secret', $config['secret']);
    }
}