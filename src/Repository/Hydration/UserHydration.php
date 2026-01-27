<?php

declare(strict_types=1);

namespace Zestic\Auth\Repository\Hydration;

use Carbon\Carbon;
use Zestic\Auth\Contract\Entity\UserInterface;
use Zestic\Auth\Contract\Repository\UserHydrationInterface;
use Zestic\Auth\Entity\User;
use Zestic\Auth\Entity\Identifier;

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
        if (isset($data['identifiers']) && is_array($data['identifiers'])) {
            $identifiers = [];
            foreach ($data['identifiers'] as $provider => $identifierData) {
                if ($identifierData instanceof Identifier) {
                    $identifiers[$provider] = $identifierData;
                } elseif (is_array($identifierData) && isset($identifierData['provider'], $identifierData['id'])) {
                    $identifiers[$identifierData['provider']] = new Identifier(
                        $identifierData['provider'],
                        $identifierData['id'],
                        $identifierData['raw_data'] ?? null
                    );
                }
            }
            $user->setIdentifiers($identifiers);
        }
        if (isset($data['system_id'])) {
            $user->setSystemId($data['system_id']);
        }
        if (isset($data['verified_at'])) {
            $user->setVerifiedAt(new Carbon($data['verified_at']));
        }
    }
}
