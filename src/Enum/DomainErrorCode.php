<?php

namespace App\Enum;

enum DomainErrorCode: string
{
    case ARTICLE_TITLE_REQUIRED = '7dfb1d19-ffc1-4611-8960-9be7f927f00e';
    case ARTICLE_TITLE_EMPTY = 'c8db0ad3-3e4c-4811-9be7-b0eccccd4f9f';
    case ARTICLE_TITLE_SHOULD_BE_STRING = 'ddc9a9cc-04a5-45e1-848d-c1cdde348a1b';
    case ARTICLE_TITLE_TOO_SHORT = '724412da-a85a-4814-9098-d614b58a5c58';
    case ARTICLE_TITLE_TOO_LONG = '6d509fd6-402f-461b-8096-df928662a131';
    case ARTICLE_TITLE_INVALID_CHARACTERS = 'b5c5729a-f595-4ccf-b975-e749b2e4a27c';

    case ARTICLE_TAGS_SHOULD_BE_ARRAY = '681b5fce-d4e8-469a-bcba-e6a865e11633';
    case ARTICLE_TAGS_SHOULD_BE_UNIQUE = '302148fc-83c9-4212-a20e-5aad8bcf442a';
    case ARTICLE_TAGS_NOT_FOUND = 'fa97f9db-57f2-4842-a8d4-e589fa58fe1b';
    case ARTICLE_TAG_IS_BLANK = '246e19ae-c879-4f5c-a0f5-94c9f303b620';
    case ARTICLE_TAG_SHOULD_BE_UUID = '2d2b0c92-c4d4-49d5-b855-f473c8c76267';
}
