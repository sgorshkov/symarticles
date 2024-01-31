<?php

declare(strict_types=1);

namespace App\Validator\Article;

use App\Enum\FormTypeEnum;
use App\Request\Article\GetArticlesListRequest;
use App\Validator\PaginatorValidatorTrait;
use App\Validator\ValidatorInterface;
use App\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;

class GetArticlesListValidator implements ValidatorInterface
{
    use ValidatorTrait;
    use PaginatorValidatorTrait;

    private const FormTypeEnum TYPE_NAME = FormTypeEnum::ArticleList;

    public function __construct(private readonly CommonArticleValidators $articleValidators)
    {
    }

    public function validateAndMap(array $requestData): GetArticlesListRequest
    {
        $this->validateRequestData(
            $requestData,
            new Collection([
                'tags' => new Optional($this->articleValidators->getTagsValidatorConstraint()),
                'page' => new Optional($this->getPageValidatorConstraint()),
                'per_page' => new Optional($this->getPerPageValidatorConstraint()),
            ]),
            static::TYPE_NAME
        );

        $tagIds = $requestData['tags'] ?? null;
        $tags = null;
        if ($tagIds) {
            $tags = $this->articleValidators->validateTagIds($tagIds);
        }

        return new GetArticlesListRequest(
            $tags,
            $this->getPageOrDefault($requestData),
            $this->getPerPageOrDefault($requestData)
        );
    }
}
