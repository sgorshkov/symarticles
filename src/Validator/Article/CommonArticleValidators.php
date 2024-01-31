<?php

declare(strict_types=1);

namespace App\Validator\Article;

use App\Entity\Tag;
use App\Enum\DomainErrorCode;
use App\Exception\ValidationException;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Uuid;

final class CommonArticleValidators
{
    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    public function getTitleValidatorConstraint(): Constraint
    {
        return new Sequentially([
            new NotBlank(),
            new Type('string'),
            new Length(
                null,
                3,
                255,
                'UTF-8'
            ),
        ]);
    }

    public function getTagsValidatorConstraint(): Constraint
    {
        return new Sequentially([
            new Type('array'),
            new Unique(),
            new All([
                new NotBlank(),
                new Type('string'),
                new Uuid(['strict' => false]),
            ]),
        ]);
    }

    /**
     * @return Tag[]
     */
    public function validateTagIds(array $tagIds): array
    {
        $tags = $this->tagRepository->findByIds($tagIds);
        if (count($tags) !== count($tagIds)) {
            throw ValidationException::fromArray([
                'tags' => 'tags not found by given ids',
                'error_code' => DomainErrorCode::ARTICLE_TAGS_NOT_FOUND,
            ]);
        }

        return $tags;
    }
}
