<?php

declare(strict_types=1);

namespace Zestic\Auth\Tests\Unit\Entity;

use Carbon\CarbonImmutable;
use Zestic\Auth\Tests\Utils\UserValues;
use PHPUnit\Framework\TestCase;
use Zestic\Auth\Entity\User;

class UserTest extends TestCase
{
    use UserValues;

    public function testSettersAndGetters(): void
    {
        $verifiedAt = new CarbonImmutable(self::VERIFIED_AT);

        $user = (new User())
            ->setAdditionalData(self::ADDITIONAL_DATA)
            ->setDisplayName(self::DISPLAY_NAME)
            ->setEmail(self::EMAIL)
            ->setId(self::ID)
            ->setIdentifiers(self::IDENTIFIERS)
            ->setSystemId(self::SYSTEM_ID)
            ->setVerifiedAt($verifiedAt);

        $this->assertSame(self::ADDITIONAL_DATA, $user->getAdditionalData());
        $this->assertSame(self::DISPLAY_NAME, $user->getDisplayName());
        $this->assertSame(self::EMAIL, $user->getEmail());
        $this->assertSame(self::ID, $user->getId());
        $this->assertSame(self::IDENTIFIERS, $user->getIdentifiers());
        $this->assertSame(self::SYSTEM_ID, $user->getSystemId());
        $this->assertEquals($verifiedAt, $user->getVerifiedAt());
        $this->assertTrue($user->isVerified());
    }
}
