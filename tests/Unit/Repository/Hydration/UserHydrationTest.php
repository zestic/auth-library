<?php

declare(strict_types=1);

namespace Zestic\Auth\Tests\Unit\Repository\Hydration;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Zestic\Auth\Entity\User;
use Zestic\Auth\Repository\Hydration\UserHydration;

class UserHydrationTest extends TestCase
{
    public function testDehydrateReturnsCorrectArray(): void
    {
        $user = (new User())
            ->setAdditionalData(['foo' => 'bar'])
            ->setDisplayName('John Doe')
            ->setEmail('john@example.com')
            ->setId(123)
            ->setSystemId('sys-456')
            ->setVerifiedAt(new Carbon('2024-01-01 12:00:00'));

        $hydrator = new UserHydration();
        $result = $hydrator->dehydrate($user);

        $this->assertEquals([
            'additional_data' => ['foo' => 'bar'],
            'display_name' => 'John Doe',
            'email' => 'john@example.com',
            'id' => 123,
            'system_id' => 'sys-456',
            'verified_at' => '2024-01-01 12:00:00',
        ], $result);
    }

    public function testHydrateCreatesUserWithData(): void
    {
        $data = [
            'additional_data' => ['foo' => 'bar'],
            'display_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'id' => 456,
            'system_id' => 'sys-789',
            'verified_at' => '2025-02-02 13:30:00',
        ];

        $hydrator = new UserHydration();
        $user = $hydrator->hydrate($data);

        $this->assertEquals(['foo' => 'bar'], $user->getAdditionalData());
        $this->assertEquals('Jane Doe', $user->getDisplayName());
        $this->assertEquals('jane@example.com', $user->getEmail());
        $this->assertEquals(456, $user->getId());
        $this->assertEquals('sys-789', $user->getSystemId());
        $this->assertEquals('2025-02-02 13:30:00', $user->getVerifiedAt()?->toDateTimeString());
    }

    public function testUpdateUpdatesUserProperties(): void
    {
        $user = new User();
        $hydrator = new UserHydration();
        $data = [
            'additional_data' => ['baz' => 'qux'],
            'display_name' => 'Alice',
            'email' => 'alice@example.com',
            'id' => 789,
            'system_id' => 'sys-101',
            'verified_at' => '2026-03-03 14:45:00',
        ];
        $hydrator->update($user, $data);

        $this->assertEquals(['baz' => 'qux'], $user->getAdditionalData());
        $this->assertEquals('Alice', $user->getDisplayName());
        $this->assertEquals('alice@example.com', $user->getEmail());
        $this->assertEquals(789, $user->getId());
        $this->assertEquals('sys-101', $user->getSystemId());
        $this->assertEquals('2026-03-03 14:45:00', $user->getVerifiedAt()?->toDateTimeString());
    }
}
