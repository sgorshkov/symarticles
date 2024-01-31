<?php

namespace App\DataFixtures;

use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use App\Util\ArticleTagUtil;
use App\Util\StringFromPartsGeneratorUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private const int ARTICLE_COUNT = 1000;
    private const int TAGS_PER_ARTICLE_COUNT = 20;
    private const array TITLE_PARTS = [
        'New day',
        'Sunny beach',
        'Dark night',
        'Right choice',
        'Newer stop',
        'Cut rope',
        'Jingle bells',
        'White space',
        'Led lamp',
        'Done right',
    ];

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly TagRepository $tagRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::ARTICLE_COUNT; $i++) {
            $articleTitle = StringFromPartsGeneratorUtil::generate($i, static::TITLE_PARTS);

            $article = $this->articleRepository->createArticle($articleTitle);

            $tags = $this->tagRepository->findBy([], null, static::TAGS_PER_ARTICLE_COUNT);
            ArticleTagUtil::setTagsForArticle($article, $tags);

            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
        ];
    }
}
