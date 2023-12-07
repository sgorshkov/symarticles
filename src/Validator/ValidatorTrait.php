<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enum\FormTypeEnum;
use App\Exception\ValidationException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

trait ValidatorTrait
{
    private function validateRequestData(
        array $requestData,
        Collection $constraints,
        FormTypeEnum $typeName
    ): void {
        $errors = Validation::createValidator()->validate($requestData, $constraints);
        if (0 !== count($errors)) {
            throw ValidationException::fromViolationArray($errors, $typeName);
        }
    }
}
