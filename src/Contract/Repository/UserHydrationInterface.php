<?php

declare(strict_types=1);

namespace Zestic\Auth\Contract\Repository;

use Zestic\Auth\Contract\Entity\UserInterface;

/**
 * @template T of UserInterface
 */
interface UserHydrationInterface
{
    /**
     * @return array<string, mixed>
     *
     * @param T $user
     */
    public function dehydrate(UserInterface $user): array;

    /**
     * @param array<string, mixed> $data
     *
     * @return T
     */
    public function hydrate(array $data): UserInterface;

    /**
     * @param array<string, mixed> $data
     * @param T                    $user
     */
    public function update(UserInterface $user, array $data): void;
}
