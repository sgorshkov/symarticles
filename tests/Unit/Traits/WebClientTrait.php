<?php

declare(strict_types=1);

namespace App\Tests\Unit\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait WebClientTrait
{
    private const string API_BASE_PREFIX = '/api/v1';
    private const array DEFAULT_WEB_CLIENT_HEADERS = [
        'HTTP_Accepted' => 'application/json',
        'HTTP_Content-Type' => 'application/json',
    ];
    abstract protected static function createClient(array $options = [], array $server = []): KernelBrowser;

    private function getWebClient(): KernelBrowser
    {
        return static::createClient([], static::DEFAULT_WEB_CLIENT_HEADERS);
    }
}
