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
 * Factory for Google reCAPTCHA v3 service adapter.
 */
class RecaptchaV3Adapter implements AdapterFactoryInterface
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
                ->thenInvalid("Google reCAPTCHA v3 requires google/recaptcha library (add it to your composer.json file).")
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
                ->scalarNode('score_threshold')->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $key, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../../Resources/config'));
        $loader->load('adapter/recaptcha_v3.xml');

        // Inject the secret in the recaptcha service
        $recaptcha = $container->getDefinition('gremo_captcha.recaptcha_v3');
        $recaptcha->replaceArgument(0, $config['secret']);
        if (isset($config['score_threshold'])) {
            $recaptcha->addMethodCall('setScoreThreshold', array($config['score_threshold']));
        }

        // Inject the configuration into the form type
        $container
            ->getDefinition('gremo_captcha.form.type.recaptcha_v3_type')
            ->replaceArgument(0, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'recaptcha_v3';
    }
}
