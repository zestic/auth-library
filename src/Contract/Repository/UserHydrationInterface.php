<?php

declare(strict_types=1);

namespace Zestic\Auth\Contract\Repository;

use Zestic\Auth\Contract\Entity\UserInterface;

interface UserHydrationInterface
{
    /**
     * @return array<string, mixed>
     */
    public function dehydrate(UserInterface $user): array;

    /**
     * @param array<string, mixed> $data
     */
    public function hydrate(array $data): UserInterface;

    /**
     * @param array<string, mixed> $data
     */
    public function update(UserInterface $user, array $data): void;
}
