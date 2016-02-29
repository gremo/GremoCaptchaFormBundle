<?php

/*
 * This file is part of the captcha-form-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\CaptchaFormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class StoredCaptcha extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The captcha challenge was not solved.';

    /**
     * @var bool
     */
    public $caseInsensitive = false;

    /**
     * @var string
     */
    public $storageKey;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'storageKey';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return array('storageKey');
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'gremo_captcha_validator_stored_captcha';
    }
}
