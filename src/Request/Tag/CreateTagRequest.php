<?php

declare(strict_types=1);

namespace App\Request\Tag;

final readonly class CreateTagRequest
{
    public function __construct(public string $name)
    {
    }
}
