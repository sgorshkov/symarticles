<?php

declare(strict_types=1);

namespace App\Request\Tag;

final readonly class GetTagsListRequest
{
    public function __construct(public int $page, public int $perPage)
    {
    }
}
