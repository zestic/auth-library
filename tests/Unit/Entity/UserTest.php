<?php

// tests/Unit/Entity/UserTest.php

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Zestic\Auth\Entity\User;

class UserTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $additionalData = ['role' => 'admin'];
        $displayName = 'Test User';
        $email = 'test@example.com';
        $id = 123;
        $verifiedAt = new CarbonImmutable('2024-01-01T00:00:00Z');
        $identifier = 'user-identifier';

        $user = (new User())
            ->setAdditionalData($additionalData)
            ->setDisplayName($displayName)
            ->setEmail($email)
            ->setId($id)
            ->setIdentifier($identifier)
            ->setSystemId('sys-42')
            ->setVerifiedAt($verifiedAt);

        $this->assertSame($additionalData, $user->getAdditionalData());
        $this->assertSame($displayName, $user->getDisplayName());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($id, $user->getId());
        $this->assertSame($identifier, $user->getIdentifier());
        $this->assertSame('sys-42', $user->getSystemId());
        $this->assertSame($verifiedAt, $user->getVerifiedAt());
        $this->assertTrue($user->isVerified());
    }
}
