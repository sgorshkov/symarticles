<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ArticleController;
use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Entity\Tag;
use App\Tests\Unit\Constant\CommonConstants;
use App\Tests\Unit\Traits\WebClientTrait;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Ulid;

/**
 * @covers \App\Controller\ArticleController::getArticle
 */
final class GetArticleActionTest extends WebTestCase
{
    use WebClientTrait;
    use ArticleControllerTestsTrait;

    /**
     * @dataProvider getArticleSuccessDataProvider
     */
    public function testGetArticleSuccess(Article $article): void
    {
        $webClient = $this->getWebClient();

        $this->mockArticleRepository(
            'getArticle',
            ['id' => CommonConstants::DEFAULT_ARTICLE_ID, 'article' => $article]
        );

        $webClient->request(
            Request::METHOD_GET,
            sprintf('%s/articles/%s', self::API_BASE_PREFIX, CommonConstants::DEFAULT_ARTICLE_ID),
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
    public function testGetArticleNotFound(string|int $articleId): void
    {
        $webClient = $this->getWebClient();
        $this->mockArticleRepository('getArticleNotFound', ['id' => $articleId]);

        $webClient->request(
            Request::METHOD_GET,
            sprintf('%s/articles/%s', self::API_BASE_PREFIX, $articleId),
        );

        $response = $webClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public static function getArticleSuccessDataProvider(): Generator
    {
        $article = new Article('test1');
        $articleTag1 = new ArticleTag($article, new Tag('tag1'));
        $articleTag2 = new ArticleTag($article, new Tag('tag2'));
        $article->setArticleTags([$articleTag1, $articleTag2]);

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
        ];

        yield 'article not found number' => [
            'articleId' => 1234,
        ];

        yield 'article not found string' => [
            'articleId' => 'qwerty',
        ];
    }
}
