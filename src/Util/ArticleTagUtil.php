<?php

declare(strict_types=1);

namespace App\Util;

use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Entity\Tag;

final readonly class ArticleTagUtil
{
    /**
     * @param Tag[] $tags
     */
    public static function setTagsForArticle(Article $article, array $tags): void
    {
        $newArticleTags = array_map(function (Tag $t) use ($article): ArticleTag {
            return new ArticleTag($article, $t);
        }, $tags);

        $article->setArticleTags($newArticleTags);
    }
}
