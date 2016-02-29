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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Blank;

/**
 * Honeypot captcha form type.
 */
class HoneypotType extends AbstractType
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
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
        $options = array();
        $options['mapped'] = false;
        $options['constraints'] = array(new Blank(array('message' => 'The captcha challenge was not solved.')));
        switch ($this->config['type']) {
            case 'text':
            case 'Symfony\Component\Form\Extension\Core\Type\TextType':
                $options['label'] = 'CAPTCHA test';
                $options['trim']  = false;
                $options['attr']  = array('placeholder' => 'Leave this field blank');

                break;
        }

        $resolver->setDefaults($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->config['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gremo_captcha_honeypot';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
