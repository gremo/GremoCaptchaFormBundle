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

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StoredCaptchaValidator extends ConstraintValidator
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof StoredCaptcha) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\StoredCaptcha');
        }

        $storedValue = $this->session->get($constraint->storageKey);
        $this->session->remove($constraint->storageKey);

        if (!is_string($value) || !is_string($storedValue) || 0 !== strcasecmp($value, $storedValue)) {
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
