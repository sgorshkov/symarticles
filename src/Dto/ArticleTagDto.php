<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\ArticleTag;
use DateTimeInterface;
use JsonSerializable;
use Symfony\Component\Uid\Ulid;

final readonly class ArticleTagDto implements JsonSerializable
{
    public function __construct(
        public Ulid $id,
        public string $name,
        public DateTimeInterface $createdAt
    ) {
    }

    public static function fromEntity(ArticleTag $articleTag): self
    {
        $tag = $articleTag->getTag();

        return new self($tag->getId(), $tag->getName(), $articleTag->getCreatedAt());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'name' => $this->name,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
