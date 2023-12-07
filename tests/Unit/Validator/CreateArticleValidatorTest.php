<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Tag;
use App\Enum\DomainErrorCode;
use App\Exception\ValidationException;
use App\Repository\TagRepository;
use App\Tests\Unit\Constant\CommonConstants;
use App\Validator\Article\CreateArticleValidator;
use Generator;
use Random\Randomizer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Validator\Article\CreateArticleValidator
 */
final class CreateArticleValidatorTest extends KernelTestCase
{
    /**
     * @dataProvider successDataProvider
     */
    public function testValidateAndMapSuccess(array $params): void
    {
        $this->mockTagRepository($params);
        $validator = self::getContainer()->get(CreateArticleValidator::class);
        $createArticleRequest = $validator->validateAndMap($params);

        $this->assertEquals($params['title'], $createArticleRequest->title);
        $this->assertCount(count($params['tags'] ?? []), $createArticleRequest->tags ?? []);
    }

    /**
     * @dataProvider failDataProvider
     */
    public function testValidateAndMapFail(array $params, array $errorCodes): void
    {
        $validator = self::getContainer()->get(CreateArticleValidator::class);
        try {
            $validator->validateAndMap($params);
            $this->fail('no validation exception caught');
        } catch (ValidationException $e) {
            $this->assertEquals(array_map(fn(DomainErrorCode $e) => $e->value, $errorCodes), $e->getMappedCodes());
            $this->assertTrue(true);
        }
    }

    public static function successDataProvider(): Generator
    {
        yield 'without tags' => [
            [
                'title' => 'some title string',
            ],
        ];

        yield 'with empty tags' => [
            [
                'title' => 'some title string',
                'tags' => [],
            ],
        ];

        yield 'with tags' => [
            [
                'title' => 'some other string',
                'tags' => [CommonConstants::TAG_ID_1, CommonConstants::TAG_ID_2],
            ],
        ];
    }

    public static function failDataProvider(): Generator
    {
        $defaultParams = [
            'tags' => [CommonConstants::TAG_ID_1, CommonConstants::TAG_ID_2],
        ];

        yield 'no title' => [
            $defaultParams,
            [
                DomainErrorCode::ARTICLE_TITLE_REQUIRED,
            ],
        ];
        $alphanum = implode([
            ...range('a', 'z'),
            ...range('A', 'Z'),
            ...range('0', '9'),
        ]);
        $titleCases = [
            ['number title', 1, DomainErrorCode::ARTICLE_TITLE_SHOULD_BE_STRING],
            ['array title', [1, 2, 3], DomainErrorCode::ARTICLE_TITLE_SHOULD_BE_STRING],
            ['blank title', '', DomainErrorCode::ARTICLE_TITLE_EMPTY],
            ['short title', 'so', DomainErrorCode::ARTICLE_TITLE_TOO_SHORT],
            [
                'long title',
                (new Randomizer())->getBytesFromString($alphanum, 256),
                DomainErrorCode::ARTICLE_TITLE_TOO_LONG,
            ],
            ['invalid character title', random_bytes(5), DomainErrorCode::ARTICLE_TITLE_INVALID_CHARACTERS],
        ];

        foreach ($titleCases as $case) {
            yield $case[0] => [
                array_merge($defaultParams, ['title' => $case[1]]),
                [$case[2]],
            ];
        }

        $defaultParams = [
            'title' => 'some valid title string',
        ];

        $tagCases = [
            ['string tags', 'tag1', DomainErrorCode::ARTICLE_TAGS_SHOULD_BE_ARRAY],
            ['number tags', 123, DomainErrorCode::ARTICLE_TAGS_SHOULD_BE_ARRAY],
            ['not unique tags', ['tag1', 'tag1'], DomainErrorCode::ARTICLE_TAGS_SHOULD_BE_UNIQUE],
            ['empty tag', [''], DomainErrorCode::ARTICLE_TAG_IS_BLANK],
            ['not uuid tag', ['tag1'], DomainErrorCode::ARTICLE_TAG_SHOULD_BE_UUID],
            ['not uuid tag wrong characters', [random_bytes(5)], DomainErrorCode::ARTICLE_TAG_SHOULD_BE_UUID],
        ];

        foreach ($tagCases as $case) {
            yield $case[0] => [
                array_merge($defaultParams, ['tags' => $case[1]]),
                [$case[2]],
            ];
        }
    }

    private function mockTagRepository(array $params): void
    {
        $tagRepositoryMock = $this->createMock(TagRepository::class);
        if ($params['tags'] ?? null) {
            $tagRepositoryMock
                ->expects($this->once())
                ->method('findByIds')
                ->with($params['tags'])
                ->willReturn([new Tag('tag1'), new Tag('tag2')]);
        }

        self::getContainer()->set(TagRepository::class, $tagRepositoryMock);
    }
}
