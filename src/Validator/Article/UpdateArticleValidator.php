<?php

declare(strict_types=1);

namespace App\Validator\Article;

use App\Enum\FormTypeEnum;
use App\Request\Article\UpdateArticleRequest;
use App\Validator\ValidatorInterface;
use App\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;

class UpdateArticleValidator implements ValidatorInterface
{
    private const FormTypeEnum TYPE_NAME = FormTypeEnum::Article;

    use ValidatorTrait;

    public function __construct(private readonly CommonArticleValidators $articleValidators)
    {
    }

    public function validateAndMap(array $requestData): UpdateArticleRequest
    {
        $this->validateRequestData(
            $requestData,
            new Collection([
                'title' => new Optional($this->articleValidators->getTitleValidatorConstraint()),
                'tags' => new Optional($this->articleValidators->getTagsValidatorConstraint()),
            ]),
            static::TYPE_NAME
        );

        $tagIds = $requestData['tags'] ?? null;
        $tags = null === $tagIds ? null : [];
        if ($tagIds) {
            $tags = $this->articleValidators->validateTagIds($tagIds);
        }

        return new UpdateArticleRequest($requestData['title'] ?? null, $tags);
    }
}
