<?php

namespace App\DataFixtures;

use App\Repository\TagRepository;
use App\Util\StringFromPartsGeneratorUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    private const int TAG_COUNT = 10000;
    private const array TAG_NAMES = [
        'blue',
        'yellow',
        'red',
        'gray',
        'carbon',
        'blank',
        'black',
        'green',
        'white',
        'cian',
    ];

    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::TAG_COUNT; $i++) {
            $tagName = StringFromPartsGeneratorUtil::generate($i, static::TAG_NAMES);

            $this->tagRepository->createTag($tagName);
            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
        $manager->clear();
    }
}
