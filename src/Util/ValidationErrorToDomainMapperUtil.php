<?php

declare(strict_types=1);

namespace App\Util;

use App\Enum\DomainErrorCode;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Uuid;

final class ValidationErrorToDomainMapperUtil
{
    private const string FIELD_NAME_ARTICLE_TITLE = 'Article.title';
    private const string FIELD_NAME_ARTICLE_TAGS = 'Article.tags';
    private const string FIELD_NAME_ARTICLE_TAG = 'Article.tags.';

    private const array FIELD_TO_ERRORS_MAP = [
        self::FIELD_NAME_ARTICLE_TITLE => self::ARTICLE_TITLE_ERROR_MAP,
        self::FIELD_NAME_ARTICLE_TAGS => self::ARTICLE_TAGS_ERROR_MAP,
        self::FIELD_NAME_ARTICLE_TAG => self::ARTICLE_TAG_ERROR_MAP,
    ];

    private const array ARTICLE_TITLE_ERROR_MAP = [
        Collection::MISSING_FIELD_ERROR => DomainErrorCode::ARTICLE_TITLE_REQUIRED,
        NotBlank::IS_BLANK_ERROR => DomainErrorCode::ARTICLE_TITLE_EMPTY,
        Type::INVALID_TYPE_ERROR => DomainErrorCode::ARTICLE_TITLE_SHOULD_BE_STRING,
        Length::TOO_SHORT_ERROR => DomainErrorCode::ARTICLE_TITLE_TOO_SHORT,
        Length::TOO_LONG_ERROR => DomainErrorCode::ARTICLE_TITLE_TOO_LONG,
        Length::INVALID_CHARACTERS_ERROR => DomainErrorCode::ARTICLE_TITLE_INVALID_CHARACTERS,
    ];

    private const array ARTICLE_TAGS_ERROR_MAP = [
        Type::INVALID_TYPE_ERROR => DomainErrorCode::ARTICLE_TAGS_SHOULD_BE_ARRAY,
        Unique::IS_NOT_UNIQUE => DomainErrorCode::ARTICLE_TAGS_SHOULD_BE_UNIQUE,
    ];

    private const array ARTICLE_TAG_ERROR_MAP = [
        NotBlank::IS_BLANK_ERROR => DomainErrorCode::ARTICLE_TAG_IS_BLANK,
        Type::INVALID_TYPE_ERROR => DomainErrorCode::ARTICLE_TAG_SHOULD_BE_UUID,
        Uuid::TOO_SHORT_ERROR => DomainErrorCode::ARTICLE_TAG_SHOULD_BE_UUID,
        Uuid::TOO_LONG_ERROR => DomainErrorCode::ARTICLE_TAG_SHOULD_BE_UUID,
        Uuid::INVALID_CHARACTERS_ERROR => DomainErrorCode::ARTICLE_TAG_SHOULD_BE_UUID,
    ];

    final public static function map(string $fieldName, string $validationErrorCode): DomainErrorCode
    {
        $errors = self::FIELD_TO_ERRORS_MAP[$fieldName];

        return $errors[$validationErrorCode];
    }
}
