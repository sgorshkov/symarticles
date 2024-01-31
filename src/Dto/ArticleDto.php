<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Article;
use App\Entity\ArticleTag;
use JsonSerializable;
use Symfony\Component\Uid\Ulid;

final readonly class ArticleDto implements JsonSerializable
{
    /**
     * @param ArticleTagDto[]|null $articleTags
     */
    public function __construct(
        public Ulid $id,
        public string $title,
        public array $articleTags = []
    ) {
    }

    public static function fromEntity(Article $article): self
    {
        return new self(
            $article->getId(),
            $article->getTitle(),
            array_map(fn(ArticleTag $at) => ArticleTagDto::fromEntity($at), $article->getArticleTags())
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'title' => $this->title,
            'tags' => array_map(fn(ArticleTagDto $at) => $at->toArray(), $this->articleTags),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
