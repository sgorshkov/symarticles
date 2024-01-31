<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ArticleDto;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Response\PaginatedResponse;
use App\Service\AppEntityManager;
use App\Util\ArticleTagUtil;
use App\Validator\Article\CreateArticleValidator;
use App\Validator\Article\GetArticlesListValidator;
use App\Validator\Article\UpdateArticleValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ArticleController
{
    #[Route('/articles', name: 'article_create', methods: ['POST'])]
    public function createArticle(
        Request $request,
        CreateArticleValidator $createArticleValidator,
        ArticleRepository $articleRepository,
        AppEntityManager $entityManager
    ): JsonResponse {
        $createArticleRequest = $createArticleValidator->validateAndMap($request->toArray());
        $article = $articleRepository->createArticle($createArticleRequest->title);

        if (null !== $createArticleRequest->tags) {
            ArticleTagUtil::setTagsForArticle($article, $createArticleRequest->tags);
        }

        $entityManager->flush();

        return new JsonResponse(ArticleDto::fromEntity($article), Response::HTTP_CREATED);
    }

    #[Route('/articles/{id}', name: 'article_get', methods: ['GET'])]
    public function getArticle(Article $article): JsonResponse
    {
        return new JsonResponse(ArticleDto::fromEntity($article));
    }

    #[Route('/articles/{id}', name: 'article_update', methods: ['PATCH'])]
    public function updateArticle(
        Request $request,
        Article $article,
        UpdateArticleValidator $updateArticleValidator,
        AppEntityManager $entityManager
    ): JsonResponse {
        $updateArticleRequest = $updateArticleValidator->validateAndMap($request->toArray());
        if ($updateArticleRequest->title) {
            $article->setTitle($updateArticleRequest->title);
        }

        if (null !== $updateArticleRequest->tags) {
            ArticleTagUtil::setTagsForArticle($article, $updateArticleRequest->tags);
        }

        $entityManager->flush();

        return new JsonResponse(ArticleDto::fromEntity($article));
    }

    #[Route('/articles', name: 'articles_list', methods: ['GET'])]
    public function getArticlesList(
        Request $request,
        GetArticlesListValidator $getArticlesListValidator,
        ArticleRepository $articleRepository
    ): JsonResponse {
        $getArticlesListRequest = $getArticlesListValidator->validateAndMap($request->query->all());
        $articles = $articleRepository->findPaginated(
            $getArticlesListRequest->tags ?? [],
            $getArticlesListRequest->page,
            $getArticlesListRequest->perPage
        );

        return new JsonResponse(
            new PaginatedResponse(
                array_map(fn(Article $a) => ArticleDto::fromEntity($a), $articles),
                $getArticlesListRequest->page,
                $getArticlesListRequest->perPage
            )
        );
    }

    #[Route('/articles/{id}', name: 'article_remove', methods: ['DELETE'])]
    public function removeArticle(Article $article, AppEntityManager $entityManager): JsonResponse
    {
        $entityManager->remove($article);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
