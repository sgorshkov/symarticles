<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ArticleController;
use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Entity\Tag;
use App\Request\Article\GetArticlesListRequest;
use App\Tests\Unit\Traits\WebClientTrait;
use App\Validator\Article\GetArticlesListValidator;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Ulid;

/**
 * @covers \App\Controller\ArticleController::getArticlesList
 */
final class GetArticlesListActionTest extends WebTestCase
{
    use WebClientTrait;
    use ArticleControllerTestsTrait;

    public function testGetArticleSuccess(): void
    {
        $this->markTestIncomplete();
        $webClient = $this->getWebClient();

        $this->mockArticleRepository('getArticlesList', []);
        $this->mockGetArticlesListValidator();

        $webClient->request(
            Request::METHOD_GET,
            sprintf('%s/articles', self::API_BASE_PREFIX),
        );

        $response = $webClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertEquals($responseContent['title'], $article->getTitle());
        $responseTags = $responseContent['tags'];
        $this->assertCount(count($article->getArticleTags()), $responseTags);
        foreach ($article->getArticleTags() as $key => $articleTag) {
            $this->assertEquals($responseTags[$key]['name'], $articleTag->getTag()->getName());
        }
        $this->assertArticleStructure($responseContent);
    }

    /**
     * @dataProvider getArticleNotFoundDataProvider
     */
    public function testGetArticleNotFound(string|int $articleId, int $statusCode): void
    {
        $this->markTestIncomplete();
        $webClient = $this->getWebClient();
        if (Response::HTTP_NOT_FOUND === $statusCode) {
            $this->mockArticleRepository('getArticleNotFound', ['id' => $articleId]);
        }

        $webClient->request(
            Request::METHOD_GET,
            sprintf('%s/articles/%s', self::API_BASE_PREFIX, $articleId),
        );

        $response = $webClient->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    public static function getArticleSuccessDataProvider(): Generator
    {
        $article = new Article('test1');
        $articleTag1 = new ArticleTag($article, new Tag('tag1'));
        $articleTag2 = new ArticleTag($article, new Tag('tag2'));
        $article->getArticleTags()->add($articleTag1);
        $article->getArticleTags()->add($articleTag2);

        yield 'article with tags' => [
            'article' => $article,
        ];

        $article = new Article('test2');
        yield 'article without tags' => [
            'article' => $article,
        ];
    }

    public static function getArticleNotFoundDataProvider(): Generator
    {
        yield 'article not found uuid' => [
            'articleId' => (new Ulid())->toRfc4122(),
            'status_code' => Response::HTTP_NOT_FOUND,
        ];

        yield 'article not found number' => [
            'articleId' => 1234,
            'status_code' => Response::HTTP_NOT_FOUND,
        ];

        yield 'article not found string' => [
            'articleId' => 'qwerty',
            'status_code' => Response::HTTP_NOT_FOUND,
        ];
    }

    private function mockGetArticlesListValidator(): void
    {
        $getArticlesListValidatorMock = self::createMock(GetArticlesListValidator::class);
        if ($requestBody['tags'] ?? null) {
            $tags = [];
            $tags[] = new Tag('tag1');
            $tags[] = new Tag('tag2');
            $createArticleRequest = new CreateArticleRequest($requestBody['title'], $tags);
        } else {
            $createArticleRequest = new CreateArticleRequest($requestBody['title']);
        }
        $getArticlesListValidatorMock
            ->expects($this->once())
            ->method('validateAndMap')
            ->willReturn(new GetArticlesListRequest());

        self::getContainer()->set(GetArticlesListValidator::class, $getArticlesListValidatorMock);
    }
}
