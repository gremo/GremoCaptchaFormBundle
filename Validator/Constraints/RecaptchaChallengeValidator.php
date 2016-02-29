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

use ReCaptcha\ReCaptcha;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Custom validator for Google reCAPTCHA service.
 */
class RecaptchaChallengeValidator extends ConstraintValidator
{
    /**
     * @var ReCaptcha
     */
    private $recaptcha;

    public function __construct(ReCaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof RecaptchaChallenge) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\RecaptchaChallenge');
        }

        if (!$this->recaptcha->verify($value)->isSuccess()) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            } else {
                if (method_exists($this->context, 'buildViolation')) {
                    $this->buildViolation($constraint->message)
                        ->addViolation();
                } else {
                    $this->context->addViolation($constraint->message);
                }
            }
        }
    }
}
