<?php

namespace Damien\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    //construit l'arbre qui définit la configuration
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        //on définit le rootnode en lui donnant en nom recaptcha
        //le nom du rootnode doit être le nom du bundle mis en minuscule en snakecase sans le mot bundle
        $rootNode = $treeBuilder->root('recaptcha');
        $rootNode
            ->children()
                ->scalarNode('key')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
                ->scalarNode('secret')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->end();

        return $treeBuilder;
    }
}