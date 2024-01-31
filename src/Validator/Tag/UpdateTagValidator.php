<?php

declare(strict_types=1);

namespace App\Validator\Tag;

use App\Enum\FormTypeEnum;
use App\Request\Tag\UpdateTagRequest;
use App\Validator\ValidatorInterface;
use App\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints\Collection;

class UpdateTagValidator implements ValidatorInterface
{
    use ValidatorTrait;

    private const FormTypeEnum TYPE_NAME = FormTypeEnum::Tag;

    public function __construct(private readonly CommonTagValidator $tagValidator)
    {
    }

    public function validateAndMap(array $requestData): UpdateTagRequest
    {
        $this->validateRequestData(
            $requestData,
            new Collection([
                'name' => $this->tagValidator->getTagNameValidatorConstraint(),
            ]),
            static::TYPE_NAME
        );

        $name = $requestData['name'];
        $this->tagValidator->validateTagNotExist($name);

        return new UpdateTagRequest($name);
    }
}
