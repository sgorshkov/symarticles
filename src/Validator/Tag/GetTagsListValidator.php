<?php

declare(strict_types=1);

namespace App\Validator\Tag;

use App\Enum\FormTypeEnum;
use App\Request\Tag\GetTagsListRequest;
use App\Validator\PaginatorValidatorTrait;
use App\Validator\ValidatorInterface;
use App\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;

class GetTagsListValidator implements ValidatorInterface
{
    use ValidatorTrait;
    use PaginatorValidatorTrait;

    private const FormTypeEnum TYPE_NAME = FormTypeEnum::TagList;

    public function validateAndMap(array $requestData): GetTagsListRequest
    {
        $this->validateRequestData(
            $requestData,
            new Collection([
                'page' => new Optional($this->getPageValidatorConstraint()),
                'per_page' => new Optional($this->getPerPageValidatorConstraint()),
            ]),
            static::TYPE_NAME
        );

        return new GetTagsListRequest(
            $this->getPageOrDefault($requestData),
            $this->getPerPageOrDefault($requestData)
        );
    }
}
