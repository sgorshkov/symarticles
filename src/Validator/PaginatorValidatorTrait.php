<?php

declare(strict_types=1);

namespace App\Validator;

use App\Constant\PaginationConstants;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;

trait PaginatorValidatorTrait
{
    public function getPageValidatorConstraint(): Constraint
    {
        return new Sequentially([
            new NotBlank(),
            new Positive(),
        ]);
    }

    private function getPerPageValidatorConstraint(): Constraint
    {
        return new Sequentially([
            new NotBlank(),
            new Positive(),
        ]);
    }

    private function getPageOrDefault(array $requestData): int
    {
        return (int)$this->getOrDefault($requestData, 'page', 1);
    }

    private function getPerPageOrDefault(array $requestData): int
    {
        return (int)$this->getOrDefault($requestData, 'per_page', PaginationConstants::DEFAULT_PER_PAGE);
    }

    private function getOrDefault(array $array, string $key, mixed $default)
    {
        return $array[$key] ?? null ? $array[$key] : $default;
    }
}
