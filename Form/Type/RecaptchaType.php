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

use Gremo\CaptchaFormBundle\Validator\Constraints\RecaptchaChallenge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Google reCAPTCHA form type.
 */
class RecaptchaType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $config;

    public function __construct(RequestStack $requestStack, array $defaults)
    {
        $this->requestStack = $requestStack;
        $this->config = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $self = $this;
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($self) {
            if (null !== $request = $self->requestStack->getCurrentRequest()) {
                $event->setData($request->request->get($self->config['request_parameter']));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach (array_keys($this->config) as $key) {
            if (array_key_exists($key, $options)) {
                $view->vars[$key] = $options[$key];
            }
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
                'error_bubbling' => false,
                'compound' => false,
                'constraints' => array(new RecaptchaChallenge()),

                // Custom options
                'key' => $this->config['key'],
                'secret' => $this->config['secret'],
                'type' => $this->config['type'],
                'theme' => $this->config['theme'],
                'size' => $this->config['size'],
                'tabindex' => $this->config['tabindex'],
                'callback' => $this->config['callback'],
                'expired_callback' => $this->config['expired_callback'],
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gremo_captcha_recaptcha';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
