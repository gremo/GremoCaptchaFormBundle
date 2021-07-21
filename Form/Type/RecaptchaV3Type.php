<?php

/*
 * This file is part of the captcha-form-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\CaptchaFormBundle\Form\Type;

use Gremo\CaptchaFormBundle\Validator\Constraints\RecaptchaV3Challange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Google reCAPTCHA v3 form type.
 */
class RecaptchaV3Type extends AbstractType
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $defaults)
    {
        $this->config = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($this->config as $key => $value) {
            $view->vars[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /** @noinspection PhpParamsInspection */
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                // Form options
                'label' => false,
                'mapped' => false,
                'error_bubbling' => true,
                'constraints' => array(new RecaptchaV3Challange()),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gremo_captcha_recaptcha_v3';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
    * {@inheritdoc}
    */
    public function getParent()
    {
        if (version_compare(Kernel::VERSION, '2.8', '<')) {
            return 'hidden';
        }

        return 'Symfony\Component\Form\Extension\Core\Type\HiddenType';
    }
}
