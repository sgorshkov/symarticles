<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Request\Article\CreateArticleRequest;
use App\Validator\Article\CreateArticleValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use Symfony\Component\DependencyInjection\Container;

trait ArticleControllerTestsTrait
{
    abstract public static function once(): InvokedCountMatcher;

    abstract protected function createMock(string $originalClassName): MockObject;

    abstract protected static function getContainer(): Container;

    private function mockCreateArticleValidator(array $requestBody): void
    {
        $createArticleValidatorMock = static::createMock(CreateArticleValidator::class);
        $createArticleValidatorMock
            ->expects($this->once())
            ->method('validateAndMap')
            ->willReturn(new CreateArticleRequest($requestBody['title']));

        static::getContainer()->set(CreateArticleValidator::class, $createArticleValidatorMock);
    }

    private function mockArticleRepository(string $method, array $params): void
    {
        $articleRepositoryMock = static::createMock(ArticleRepository::class);
        switch ($method) {
            case 'create':
                $articleRepositoryMock
                    ->expects($this->once())
                    ->method('createArticle')
                    ->with($params['title'])
                    ->willReturn(new Article($params['title']));
                break;
            case 'getArticle':
            case 'update':
                $articleRepositoryMock
                    ->expects($this->once())
                    ->method('find')
                    ->with($params['id'])
                    ->willReturn($params['article']);
                break;
            case 'getArticleNotFound':
                $articleRepositoryMock
                    ->expects($this->once())
                    ->method('find')
                    ->with($params['id'])
                    ->willReturn(null);
                break;
        }

        static::getContainer()->set(ArticleRepository::class, $articleRepositoryMock);
    }

    private function assertArticleStructure(array $articleArray): void
    {
        $this->assertEquals(['id', 'title', 'tags'], array_keys($articleArray));
    }
}
