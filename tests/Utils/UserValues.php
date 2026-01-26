<?php

declare(strict_types=1);

namespace Zestic\Auth\Tests\Utils;

trait UserValues
{
    public const DISPLAY_NAME = 'John Doe';
    public const EMAIL = 'john@example.com';
    public const ID = 123;
    public const IDENTIFIERS = [
        [
            'main' => 'id10T',
        ],
    ];
    public const SYSTEM_ID = 'sys-456';
    public const VERIFIED_AT = '2024-01-01 12:00:00';
    public const ADDITIONAL_DATA = ['foo' => 'bar'];
}
