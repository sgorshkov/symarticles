<?php

declare(strict_types=1);

namespace App\Util;

use RuntimeException;

final readonly class ArrayTypeCheckUtil
{
    /**
     * @throws RuntimeException
     */
    public static function check(string $type, array $array): void
    {
        $errorString = sprintf('Array must contain only %s objects', $type);
        array_map(fn($t) => $t instanceof $type ?: throw new RuntimeException($errorString), $array);
    }
}
