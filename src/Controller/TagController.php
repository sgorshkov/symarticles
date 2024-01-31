<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TagDto;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Response\PaginatedResponse;
use App\Service\AppEntityManager;
use App\Validator\Tag\CreateTagValidator;
use App\Validator\Tag\GetTagsListValidator;
use App\Validator\Tag\UpdateTagValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TagController
{
    #[Route('/tags', name: 'tags_create', methods: ['POST'])]
    public function createTag(
        Request $request,
        CreateTagValidator $createTagValidator,
        TagRepository $tagRepository,
        AppEntityManager $entityManager
    ): JsonResponse {
        $createTagRequest = $createTagValidator->validateAndMap($request->toArray());
        $tag = $tagRepository->createTag($createTagRequest->name);

        $entityManager->flush();

        return new JsonResponse(TagDto::fromEntity($tag), Response::HTTP_CREATED);
    }

    #[Route('/tags/{id}', name: 'tags_update', methods: ['PUT'])]
    public function updateTag(
        Request $request,
        UpdateTagValidator $updateTagValidator,
        TagRepository $tagRepository,
        AppEntityManager $entityManager,
        Tag $id
    ): JsonResponse {
        $updateTagRequest = $updateTagValidator->validateAndMap($request->toArray());
        $tag = $tagRepository->find($id);
        $tag->setName($updateTagRequest->name);

        $entityManager->flush();

        return new JsonResponse(TagDto::fromEntity($tag));
    }

    #[Route('/tags', name: 'tags_list', methods: ['GET'])]
    public function listTags(
        Request $request,
        TagRepository $tagRepository,
        GetTagsListValidator $getTagsListValidator
    ): JsonResponse {
        $getTagsListRequest = $getTagsListValidator->validateAndMap($request->query->all());
        $tags = $tagRepository->findPaginated(
            $getTagsListRequest->page,
            $getTagsListRequest->perPage
        );

        return new JsonResponse(
            new PaginatedResponse(
                array_map(fn(Tag $t) => TagDto::fromEntity($t), $tags),
                $getTagsListRequest->page,
                $getTagsListRequest->perPage
            )
        );
    }
}
