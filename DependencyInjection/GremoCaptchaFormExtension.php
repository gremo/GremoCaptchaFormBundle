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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class GremoCaptchaFormExtension extends Extension
{
    /**
     * @var \Gremo\CaptchaFormBundle\DependencyInjection\Factory\AdapterFactoryInterface[]
     */
    private $adapterFactories;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->loadAdapterFactories());
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('gremo_captcha.template', $config['template']);
        $container->setParameter('gremo_captcha.default_adapter', $config['default_adapter']);

        $this->loadAdapters($config['adapters'], $container);
    }

    /**
     * @return array
     */
    private function loadAdapterFactories()
    {
        if (null !== $this->adapterFactories) {
            return $this->adapterFactories;
        }

        $container = new ContainerBuilder();
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('factories.xml');

        $factories = [];
        foreach (array_keys($container->findTaggedServiceIds('gremo_captcha.adapter_factory')) as $id) {
            $factory = $container->get($id);
            $factories[str_replace('-', '_', $factory->getKey())] = $factory;
        }

        return $this->adapterFactories = $factories;
    }

    /**
     * @param array $adapters
     * @param ContainerBuilder $container
     */
    private function loadAdapters(array $adapters, ContainerBuilder $container)
    {
        foreach ($adapters as $key => $config) {
            $factory = $this->adapterFactories[$key];
            $factory->create($container, $key, $config);
        }
    }
}
