<?php

declare(strict_types=1);

namespace Zestic\Auth\Tests\Unit\Repository\Hydration;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Zestic\Auth\Entity\User;
use Zestic\Auth\Repository\Hydration\UserHydration;
use Zestic\Auth\Tests\Utils\UserValues;

class UserHydrationTest extends TestCase
{
    use UserValues;

    public function testDehydrateReturnsCorrectArray(): void
    {
        $user = (new User())
            ->setAdditionalData(self::ADDITIONAL_DATA)
            ->setDisplayName(self::DISPLAY_NAME)
            ->setEmail(self::EMAIL)
            ->setId(self::ID)
            ->setIdentifiers(self::IDENTIFIERS)
            ->setSystemId(self::SYSTEM_ID)
            ->setVerifiedAt(new Carbon(self::VERIFIED_AT));

        $hydrator = new UserHydration();
        $result = $hydrator->dehydrate($user);

        $this->assertEquals([
            'additional_data' => self::ADDITIONAL_DATA,
            'display_name' => self::DISPLAY_NAME,
            'email' => self::EMAIL,
            'id' => self::ID,
            'identifiers' => self::IDENTIFIERS,
            'system_id' => self::SYSTEM_ID,
            'verified_at' => self::VERIFIED_AT,
        ], $result);
    }

    public function testHydrateCreatesUserWithData(): void
    {
        $data = [
            'additional_data' => self::ADDITIONAL_DATA,
            'display_name' => self::DISPLAY_NAME,
            'email' => self::EMAIL,
            'id' => self::ID,
            'identifiers' => self::IDENTIFIERS,
            'system_id' => self::SYSTEM_ID,
            'verified_at' => self::VERIFIED_AT,
        ];

        $hydrator = new UserHydration();
        $user = $hydrator->hydrate($data);

        $this->assertEquals(self::ADDITIONAL_DATA, $user->getAdditionalData());
        $this->assertEquals(self::DISPLAY_NAME, $user->getDisplayName());
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertEquals(self::ID, $user->getId());
        $this->assertEquals(self::IDENTIFIERS, $user->getIdentifiers());
        $this->assertEquals(self::SYSTEM_ID, $user->getSystemId());
        $this->assertEquals(self::VERIFIED_AT, $user->getVerifiedAt()?->toDateTimeString());
    }

    public function testUpdateUpdatesUserProperties(): void
    {
        $user = new User();
        $hydrator = new UserHydration();
        $data = [
            'additional_data' => self::ADDITIONAL_DATA,
            'display_name' => self::DISPLAY_NAME,
            'email' => self::EMAIL,
            'id' => self::ID,
            'identifiers' => self::IDENTIFIERS,
            'system_id' => self::SYSTEM_ID,
            'verified_at' => self::VERIFIED_AT,
        ];
        $hydrator->update($user, $data);

        $this->assertEquals(self::ADDITIONAL_DATA, $user->getAdditionalData());
        $this->assertEquals(self::DISPLAY_NAME, $user->getDisplayName());
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertEquals(self::ID, $user->getId());
        $this->assertEquals(self::IDENTIFIERS, $user->getIdentifiers());
        $this->assertEquals(self::SYSTEM_ID, $user->getSystemId());
        $this->assertEquals(self::VERIFIED_AT, $user->getVerifiedAt()?->toDateTimeString());
    }
}
