<?php

declare(strict_types=1);

namespace Zestic\Auth\Repository\Hydration;

use Carbon\Carbon;
use Zestic\Auth\Contract\Entity\UserInterface;
use Zestic\Auth\Contract\Repository\UserHydrationInterface;
use Zestic\Auth\Entity\User;

/**
 * @implements UserHydrationInterface<User>
 */
class UserHydration implements UserHydrationInterface
{
    /**
     * @param User $user
     */
    public function dehydrate(UserInterface|User $user): array
    {
        return [
            'additional_data' => $user->getAdditionalData(),
            'display_name' => $user->getDisplayName(),
            'email' => $user->getEmail(),
            'id' => $user->getId(),
            'identifiers' => $user->getIdentifiers(),
            'system_id' => $user->getSystemId(),
            'verified_at' => $user->getVerifiedAt()?->toDateTimeString(),
        ];
    }

    public function hydrate(array $data): UserInterface
    {
        $user = new User();
        $this->update($user, $data);

        return $user;
    }

    /**
     * @param User $user
     */
    public function update(UserInterface|User $user, array $data): void
    {
        if (isset($data['additional_data'])) {
            $user->setAdditionalData($data['additional_data']);
        }
        if (isset($data['display_name'])) {
            $user->setDisplayName($data['display_name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['id'])) {
            $user->setId($data['id']);
        }
        if (isset($data['identifiers'])) {
            $user->setIdentifiers($data['identifiers']);
        }
        if (isset($data['system_id'])) {
            $user->setSystemId($data['system_id']);
        }
        if (isset($data['verified_at'])) {
            $user->setVerifiedAt(new Carbon($data['verified_at']));
        }
    }
}
