<?php

/*
 * This file is part of the captcha-form-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\CaptchaFormBundle\DependencyInjection\Factory\Adapter;

use Gremo\CaptchaFormBundle\DependencyInjection\Factory\AdapterFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Factory for honeypot technique adapter.
 */
class HoneypotAdapter implements AdapterFactoryInterface
{
    /**
     * @var array
     */
    private static $typesMap = array(
        'hidden' => 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
        'text'   => 'Symfony\Component\Form\Extension\Core\Type\TextType',
    );

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('type')
                    ->validate()
                        ->ifNotInArray($types = array_merge(self::$typesMap, array_keys(self::$typesMap)))
                        ->thenInvalid(
                            '%s type is not allowed (allowed: '.implode(', ', array_map(function ($t) {
                                return '"'.$t.'"';
                            }, $types)).').'
                        )
                    ->end()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $key, array $config)
    {
        if (version_compare(Kernel::VERSION, '2.7', '>=')) {
            // Use the FQCN if type name is provided
            if (array_key_exists($config['type'], self::$typesMap)) {
                $config['type'] = self::$typesMap[$config['type']];
            }
        } else {
            // Use the type name if a FQCN name is provided
            if (!array_key_exists($config['type'], self::$typesMap)) {
                $config['type'] = array_search($config['type'], self::$typesMap);
            }
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../../Resources/config'));
        $loader->load('adapter/honeypot.xml');

        $container->getDefinition('gremo_captcha.form.type.honeypot')
            ->replaceArgument(0, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'honeypot';
    }
}
