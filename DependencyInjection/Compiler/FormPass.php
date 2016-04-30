<?php

/*
 * This file is part of the captcha-form-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\CaptchaFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($definition = 'gremo_captcha.captcha_form_type')) {
            return;
        }

        $formDefinition = $container->getDefinition($definition);

        $formsMap = array();
        foreach ($container->findTaggedServiceIds('captcha_form.type') as $id => $attributes) {
            $definition = $container->getDefinition($id);

            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            if (!is_a($class, $interface = 'Symfony\Component\Form\FormTypeInterface', true)) {
                throw new \InvalidArgumentException(
                    sprintf('The service "%s" tagged as a captcha_form.type must implement %s.', $id, $interface)
                );
            }

            $attributes = call_user_func_array('array_merge', $attributes);
            if (!isset($attributes['adapter'])) {
                throw new \InvalidArgumentException(
                    sprintf('The service "%s" must specify the adapter attribute.', $id)
                );
            }

            $formsMap[$attributes['adapter']] = $id;
        }

        $defaultAdapter = $container->getParameter('gremo_captcha.default_adapter');
        if (!array_key_exists($defaultAdapter, $formsMap)) {
            throw new \InvalidArgumentException(sprintf(
                'Seems you are missing the form for the default captcha adapter "%s". '.
                'Please ensure that the form is tagged as captcha_form.type',
                $defaultAdapter
            ));
        }

        $formDefinition->replaceArgument(0, new Reference($formsMap[$defaultAdapter]));
    }
}
