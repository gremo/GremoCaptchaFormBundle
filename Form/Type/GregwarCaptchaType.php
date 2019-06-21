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

use Gregwar\Captcha\CaptchaBuilder;
use Gremo\CaptchaFormBundle\Validator\Constraints\StoredCaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Gregwar captcha form type.
 */
class GregwarCaptchaType extends AbstractType
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CaptchaBuilder
     */
    private $captchaBuilder;

    /**
     * @var array
     */
    private $config;

    public function __construct(SessionInterface $session, CaptchaBuilder $captchaBuilder, array $config)
    {
        $this->session = $session;
        $this->captchaBuilder = $captchaBuilder;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Configure the captcha builder
        $captchaBuilder = $this->captchaBuilder;
        foreach (array('distorsion', 'interpolation', 'ignore_all_effects') as $option) {
            if (null !== $value = $options[$option]) {
                $setter = 'set'.implode(null, array_map('ucfirst', explode('_', $option)));
                if (method_exists($captchaBuilder, $setter)) {
                    call_user_func(array($captchaBuilder, $setter), $value);
                }
            }
        }

        // Build build method arguments
        $arguments = array();
        if (null !== $options['width']) {
            $arguments[] = $options['width'];
            $arguments[] = $options['height'];
        }

        if (null !== $options['font']) {
            $arguments[] = $options['font'];
        }

        // Build the captcha
        call_user_func_array(array($captchaBuilder, $options['ocr'] ? 'buildAgainstOCR' : 'build'), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->session->set($options['storage_key'], $this->captchaBuilder->getPhrase());

        $view->vars['quality'] = $options['quality'];
        $view->vars['builder'] = $this->captchaBuilder;
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
                'label' => 'CAPTCHA test',
                'mapped' => false,
                'constraints' => function (Options $options) {
                    return array(
                        new StoredCaptcha(array(
                            'storageKey' => $options['storage_key'],
                            'caseInsensitive' => true,
                        ))
                    );
                },

                // Custom options
                'storage_key' => $this->config['storage_key'],
                'width' => $this->config['width'],
                'height' => $this->config['height'],
                'quality' => $this->config['quality'],
                'font' => $this->config['font'],
                'distorsion' => $this->config['distorsion'],
                'interpolation' => $this->config['interpolation'],
                'ignore_all_effects' => $this->config['ignore_all_effects'],
                'ocr' => $this->config['ocr'],
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gremo_captcha_gregwar';
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
        if (version_compare(Kernel::VERSION, '2.7', '<')) {
            return 'text';
        }

        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }
}
