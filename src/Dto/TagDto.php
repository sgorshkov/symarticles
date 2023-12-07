<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Tag;
use JsonSerializable;
use Symfony\Component\Uid\Ulid;

final readonly class TagDto implements JsonSerializable
{
    public function __construct(
        public Ulid $id,
        public string $name
    ) {
    }

    public static function fromEntity(Tag $tag): self
    {
        return new self($tag->getId(), $tag->getName());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'name' => $this->name,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
