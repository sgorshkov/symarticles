<?php

declare(strict_types=1);

namespace App\Request\Tag;

readonly class CreateTagRequest
{
    public function __construct(public string $name)
    {
    }
}
