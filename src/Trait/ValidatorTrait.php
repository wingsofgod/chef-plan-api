<?php
declare(strict_types=1);

namespace App\Trait;

use App\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ValidatorTrait
{
    protected ValidatorInterface $validator;

    #[Required]
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    public function checkViolationList(ConstraintViolationListInterface $violationList): void
    {
        if ($violationList->count() > 0) {
            $messages = [];
            foreach ($violationList as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            throw new ValidationException($messages);
        }
    }
}