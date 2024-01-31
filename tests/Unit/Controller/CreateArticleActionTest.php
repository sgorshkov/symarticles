<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ArticleController;
use App\Entity\Tag;
use App\Request\Article\CreateArticleRequest;
use App\Service\AppEntityManager;
use App\Tests\Unit\Constant\CommonConstants;
use App\Tests\Unit\Traits\WebClientTrait;
use App\Validator\Article\CreateArticleValidator;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\ArticleController::createArticle
 */
final class CreateArticleActionTest extends WebTestCase
{
    use WebClientTrait;
    use ArticleControllerTestsTrait;

    /**
     * @dataProvider createArticleDataProvider
     */
    public function testCreateArticle(array $requestBody): void
    {
        $webClient = $this->getWebClient();

        $this->mockAppEntityManager();
        $this->mockArticleRepository('create', $requestBody);
        $this->mockCreateArticleValidator($requestBody);

        $webClient->request(
            Request::METHOD_POST,
            sprintf('%s/articles', self::API_BASE_PREFIX),
            [],
            [],
            [],
            json_encode($requestBody)
        );

        $response = $webClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseContent = json_decode($response->getContent(), true);
        $this->assertArticleStructure($responseContent);
    }

    public static function createArticleDataProvider(): Generator
    {
        yield 'with tags' => [
            [
                'title' => 'test title string',
                'tags' => [CommonConstants::TAG_ID_1, CommonConstants::TAG_ID_2],
            ],
        ];

        yield 'without tags' => [
            [
                'title' => 'test title string 2',
            ],
        ];
    }

    private function mockCreateArticleValidator(array $requestBody): void
    {
        $createArticleValidatorMock = self::createMock(CreateArticleValidator::class);
        if ($requestBody['tags'] ?? null) {
            $tags = [];
            $tags[] = new Tag('tag1');
            $tags[] = new Tag('tag2');
            $createArticleRequest = new CreateArticleRequest($requestBody['title'], $tags);
        } else {
            $createArticleRequest = new CreateArticleRequest($requestBody['title']);
        }
        $createArticleValidatorMock
            ->expects($this->once())
            ->method('validateAndMap')
            ->willReturn($createArticleRequest);

        self::getContainer()->set(CreateArticleValidator::class, $createArticleValidatorMock);
    }

    private function mockAppEntityManager(): void
    {
        $appEntityManagerMock = $this->createMock(AppEntityManager::class);
        $appEntityManagerMock
            ->expects($this->once())
            ->method('flush');

        self::getContainer()->set(AppEntityManager::class, $appEntityManagerMock);
    }
}
