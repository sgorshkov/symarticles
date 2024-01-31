<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ArticleController;
use App\Entity\Article;
use App\Entity\Tag;
use App\Request\Article\UpdateArticleRequest;
use App\Tests\Unit\Constant\CommonConstants;
use App\Tests\Unit\Traits\WebClientTrait;
use App\Validator\Article\UpdateArticleValidator;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\ArticleController::updateArticle
 */
final class UpdateArticleActionTest extends WebTestCase
{
    use WebClientTrait;
    use ArticleControllerTestsTrait;

    /**
     * @dataProvider updateArticleDataProvider
     */
    public function testUpdateArticle(Article $article, array $requestBody): void
    {
        $webClient = $this->getWebClient();

        $this->mockArticleRepository(
            'update',
            array_merge([
                'id' => CommonConstants::DEFAULT_ARTICLE_ID,
                'article' => $article,
            ], $requestBody)
        );
        $this->mockUpdateArticleValidator($requestBody);

        $webClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/articles/%s', self::API_BASE_PREFIX, CommonConstants::DEFAULT_ARTICLE_ID),
            [],
            [],
            [],
            json_encode($requestBody)
        );

        $response = $webClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseContent = json_decode($response->getContent(), true);
        $this->assertArticleStructure($responseContent);
    }

    public static function updateArticleDataProvider(): Generator
    {
        $article = new Article('test title');
        yield 'with tags' => [
            $article,
            [
                'title' => 'test title string',
                'tags' => [CommonConstants::TAG_ID_1, CommonConstants::TAG_ID_2],
            ],
        ];

        $article = new Article('test title string');
        yield 'without tags' => [
            $article,
            [
                'title' => 'test title string 2',
            ],
        ];
    }

    private function mockUpdateArticleValidator(array $requestBody): void
    {
        $updateArticleValidatorMock = self::createMock(UpdateArticleValidator::class);
        if ($requestBody['tags'] ?? null) {
            $tags = [];
            $tags[] = new Tag('tag1');
            $tags[] = new Tag('tag2');
            $updateArticleRequest = new UpdateArticleRequest($requestBody['title'], $tags);
        } else {
            $updateArticleRequest = new UpdateArticleRequest($requestBody['title'], null);
        }
        $updateArticleValidatorMock
            ->expects($this->once())
            ->method('validateAndMap')
            ->willReturn($updateArticleRequest);

        self::getContainer()->set(UpdateArticleValidator::class, $updateArticleValidatorMock);
    }
}
