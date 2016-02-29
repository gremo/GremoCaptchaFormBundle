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

/**
 * Factory for gregwar/captcha library adapter.
 */
class GregwarCaptchaAdapter implements AdapterFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->validate()
                ->ifTrue(function () {
                    return !class_exists('Gregwar\Captcha\CaptchaBuilder');
                })
                ->thenInvalid("Gregwar captcha requires gregwar/captcha library (add it to your composer.json file).")
            ->end()
            ->validate()
                ->ifTrue(function ($v) {
                    return null !== $v['width'] && null === $v['height']
                        || null === $v['width'] && null !== $v['height'];
                })
                ->thenInvalid('you must specify both width and height or none of them.')
            ->end()
            ->children()
                ->scalarNode('storage_key')
                    ->cannotBeEmpty()
                    ->defaultValue('_gregwar_captcha')
                ->end()
                ->integerNode('width')->defaultNull()->end()
                ->integerNode('height')->defaultNull()->end()
                ->integerNode('quality')->defaultNull()->end()
                ->scalarNode('font')->defaultNull()->end()
                ->booleanNode('distorsion')->defaultNull()->end()
                ->booleanNode('interpolation')->defaultNull()->end()
                ->booleanNode('ignore_all_effects')->defaultNull()->end()
                ->booleanNode('ocr')->defaultNull()->end()
            ->end();
    }

    /**
     * @param ContainerBuilder $container
     * @param string $key
     * @param array $config
     */
    public function create(ContainerBuilder $container, $key, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../../Resources/config'));
        $loader->load('adapter/gregwar_captcha.xml');

        $definition = $container->getDefinition('gremo_captcha.gregwar_builder');
        if (null !== $config['distorsion']) {
            $definition->addMethodCall('setDistortion', array($config['distorsion']));
        }

        if (null !== $config['interpolation']) {
            $definition->addMethodCall('setInterpolation', array($config['interpolation']));
        };

        // Inject the configuration into the form type
        $container
            ->getDefinition('gremo_captcha.form.type.gregwar_captcha')
            ->replaceArgument(2, $config);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'gregwar_captcha';
    }
}
