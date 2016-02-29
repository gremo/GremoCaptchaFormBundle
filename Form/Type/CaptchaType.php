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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Captcha form type.
 *
 * This is a sort of factory for captcha forms since parent type comes from configuration.
 */
class CaptchaType extends AbstractType
{
    /**
     * @var FormTypeInterface
     */
    private $adapterForm;

    public function __construct(FormTypeInterface $adapterForm)
    {
        $this->adapterForm = $adapterForm;
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
    public function getBlockPrefix()
    {
        return 'gremo_captcha';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        if (version_compare(Kernel::VERSION, '2.7', '<')) {
            return $this->adapterForm->getName();
        }

        return get_class($this->adapterForm);
    }
}
