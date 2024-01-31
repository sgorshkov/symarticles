<?php

declare(strict_types=1);

namespace App\Request\Article;

use App\Entity\Tag;
use App\Util\ArrayTypeCheckUtil;

final readonly class CreateArticleRequest
{
    /**
     * @param Tag[]|null $tags
     */
    public function __construct(public string $title, public ?array $tags = null)
    {
        ArrayTypeCheckUtil::check(Tag::class, $tags ?? []);
    }
}
