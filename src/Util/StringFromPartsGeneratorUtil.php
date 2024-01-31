<?php

declare(strict_types=1);

namespace App\Util;

final readonly class StringFromPartsGeneratorUtil
{
    /**
     * @param string[] $parts
     */
    public static function generate(int $number, array $parts): string
    {
        $nameParts = [];
        $namePartsNum = [];
        preg_match_all("/\d/", (string)$number, $namePartsNum);
        foreach ($namePartsNum[0] as $partNum) {
            $nameParts[] = $parts[(int)$partNum];
        }

        return implode(' ', $nameParts);
    }
}
