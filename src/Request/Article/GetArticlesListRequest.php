<?php

declare(strict_types=1);

namespace App\Request\Article;

use App\Entity\Tag;
use App\Util\ArrayTypeCheckUtil;

readonly class GetArticlesListRequest
{
    /**
     * @param Tag[]|null $tags
     */
    public function __construct(public ?array $tags, public int $page, public int $perPage)
    {
        ArrayTypeCheckUtil::check(Tag::class, $tags ?? []);
    }
}
