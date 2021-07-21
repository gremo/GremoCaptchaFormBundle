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

class RecaptchaV3Challange extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The captcha challenge was not solved.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'gremo_captcha_recaptcha_v3_validator';
    }
}
