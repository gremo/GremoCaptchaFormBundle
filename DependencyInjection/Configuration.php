<?php

/*
 * This file is part of the captcha-form-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\CaptchaFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var \Gremo\CaptchaFormBundle\DependencyInjection\Factory\AdapterFactoryInterface[]
     */
    private $adapterFactories;

    public function __construct(array $adapterFactories)
    {
        $this->adapterFactories = $adapterFactories;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('gremo_captcha_form');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('gremo_captcha_form');
        }

        $availableAdapters = array();
        foreach ($this->adapterFactories as $factory) {
            $availableAdapters[] = $factory->getKey();
        }

        $adaptersNode = $rootNode
            ->fixXmlConfig('adapter', 'adapters')
            ->validate()
                ->ifTrue(function ($v) {
                    return !array_key_exists('adapters', $v) || !count($v['adapters']);
                })
                ->thenInvalid('you need to configure at least one adapter.')
            ->end()
            ->beforeNormalization()
            ->always(function ($v) {
                if (!isset($v['default_adapter']) && isset($v['adapters'])) {
                    $v['default_adapter'] = key($v['adapters']);
                }

                return $v;
            })
            ->end()
            ->children()
                ->scalarNode('template')
                    ->defaultValue('GremoCaptchaFormBundle::default.html.twig')
                    ->cannotBeEmpty()
                ->end()

                ->scalarNode('default_adapter')
                    ->defaultNull()
                    ->validate()
                        ->ifNotInArray($availableAdapters)
                        ->thenInvalid(
                            '%s is not a valid adapter key (available adapter keys: '.
                            implode(', ', array_map(function ($s) {
                                return '"'.$s.'"';
                            }, $availableAdapters)).').'
                        )
                    ->end()
                ->end()

                ->arrayNode('adapters')
                    ->children();

        foreach ($this->adapterFactories as $factory) {
            $factory->addConfiguration($adaptersNode->arrayNode($factory->getKey()));
        }

        return $treeBuilder;
    }
}
