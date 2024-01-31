<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\FormTypeEnum;
use App\Util\ValidationErrorToDomainMapperUtil;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

final class ValidationException extends RuntimeException implements HttpExceptionInterface
{
    private const int STATUS_CODE = Response::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * @var ConstraintViolationInterface[] $errors
     */
    private iterable $errors = [];
    private iterable $errorCodes = [];
    private iterable $mappedCodes = [];

    private FormTypeEnum $typeName;

    /**
     * @param ConstraintViolationInterface[] $errors
     */
    public static function fromViolationArray(iterable $errors, FormTypeEnum $typeName): self
    {
        $exception = new self(
            'Validation error',
            self::STATUS_CODE
        );
        $exception->setErrors($errors);
        $exception->setTypeName($typeName);
        $exception->prepareMappedCodes();

        return $exception;
    }

    public static function fromArray(array $errors): self
    {
        $exception = new self(
            'Validation error',
            self::STATUS_CODE
        );
        $exception->setErrors($errors);

        return $exception;
    }

    public function getStatusCode(): int
    {
        return self::STATUS_CODE;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setTypeName(FormTypeEnum $typeName): void
    {
        $this->typeName = $typeName;
    }

    public function getMappedCodes(): array
    {
        return $this->mappedCodes;
    }

    private function prepareMappedCodes(): void
    {
        $mappedCodes = [];
        foreach ($this->errorCodes as $name => $codes) {
            $typedName = sprintf('%s.%s', $this->typeName->name, $name);
            foreach ($codes as $code) {
                $mappedCodes[] = ValidationErrorToDomainMapperUtil::map($typedName, $code)->value;
            }
        }

        $this->mappedCodes = $mappedCodes;
    }

    /**
     * @param ConstraintViolationInterface[] $errors
     */
    private function setErrors(iterable $errors): void
    {
        foreach ($errors as $error) {
            $path = $error->getPropertyPath();
            preg_match_all("/\[[a-zA-Z0-9]*\]/", $path, $names);
            $name = array_reduce($names[0], function ($acc, $item) {
                $filtered = preg_replace("/[\[|\]]/", '', $item);
                if (is_numeric($filtered)) {
                    $acc .= '.';
                } else {
                    $acc .= $filtered;
                }

                return $acc;
            }, '');
            $this->errors[$name][] = $error->getPropertyPath() . ': ' . $error->getMessage();
            $this->errorCodes[$name][] = $error->getCode();
        }
    }
}
