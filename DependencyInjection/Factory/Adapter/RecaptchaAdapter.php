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
 * Factory for Google reCAPTCHA service adapter.
 */
class RecaptchaAdapter implements AdapterFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $builder
            ->validate()
                ->ifTrue(function () {
                    return !class_exists('ReCaptcha\ReCaptcha');
                })
                ->thenInvalid("Google reCAPTCHA requires google/recaptcha library (add it to your composer.json file).")
            ->end()
            ->children()
                ->scalarNode('key')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('theme')->defaultNull()->end()
                ->scalarNode('type')->defaultNull()->end()
                ->scalarNode('size')->defaultNull()->end()
                ->integerNode('tabindex')->defaultNull()->end()
                ->scalarNode('callback')->defaultNull()->end()
                ->scalarNode('expired_callback')->defaultNull()->end()
                ->scalarNode('request_parameter')
                    ->defaultValue('g-recaptcha-response')
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $key, array $config)
    {
        $container->setParameter('gremo_captcha.recaptcha.key',    $config['key']);
        $container->setParameter('gremo_captcha.recaptcha.secret', $config['secret']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../../Resources/config'));
        $loader->load('adapter/recaptcha.xml');

        // Inject the secret in the recaptcha service
        $container
            ->getDefinition('gremo_captcha.recaptcha')
            ->replaceArgument(0, $config['secret']);

        // Inject the configuration into the form type
        $container
            ->getDefinition('gremo_captcha.form.type.recaptcha_type')
            ->replaceArgument(1, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'recaptcha';
    }
}
