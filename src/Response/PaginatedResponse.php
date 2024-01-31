<?php

declare(strict_types=1);

namespace App\Response;

use JsonSerializable;

final readonly class PaginatedResponse implements JsonSerializable
{
    public function __construct(
        public array $items,
        public int $page,
        public int $perPage
    ) {
    }

    public function toArray(): array
    {
        return [
            'items' => array_map(fn(mixed $item) => $item->toArray(), $this->items),
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
