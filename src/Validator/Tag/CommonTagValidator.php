<?php

declare(strict_types=1);

namespace App\Validator\Tag;

use App\Exception\ValidationException;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;

class CommonTagValidator
{
    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    public function validateTagNotExist(string $name): void
    {
        $existTag = $this->tagRepository->findOneBy(['name' => $name]);
        if ($existTag) {
            throw ValidationException::fromArray([
                'name' => sprintf('tag with name: <%s> already exists', $name),
            ]);
        }
    }

    public function getTagNameValidatorConstraint(): Constraint
    {
        return new Required(
            new Sequentially(
                [
                    new NotBlank(),
                    new Type('string'),
                ]
            )
        );
    }
}
