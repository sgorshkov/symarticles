<?php

declare(strict_types=1);

namespace App\Validator\Article;

use App\Enum\FormTypeEnum;
use App\Request\Article\CreateArticleRequest;
use App\Validator\ValidatorInterface;
use App\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

class CreateArticleValidator implements ValidatorInterface
{
    use ValidatorTrait;

    private const FormTypeEnum TYPE_NAME = FormTypeEnum::Article;

    public function __construct(private readonly CommonArticleValidators $articleValidators)
    {
    }

    public function validateAndMap(array $requestData): CreateArticleRequest
    {
        $this->validateRequestData(
            $requestData,
            new Collection([
                'title' => new Required($this->articleValidators->getTitleValidatorConstraint()),
                'tags' => new Optional($this->articleValidators->getTagsValidatorConstraint()),
            ]),
            static::TYPE_NAME
        );

        $tagIds = $requestData['tags'] ?? null;
        $tags = null;
        if ($tagIds) {
            $tags = $this->articleValidators->validateTagIds($tagIds);
        }

        return new CreateArticleRequest($requestData['title'], $tags);
    }
}
