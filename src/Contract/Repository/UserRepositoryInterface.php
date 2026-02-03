<?php

declare(strict_types=1);

namespace Zestic\Auth\Contract\Repository;

use Zestic\Auth\Contract\Entity\UserInterface;

interface UserRepositoryInterface
{
    public function beginTransaction(): void;

    public function commit(): bool;

    public function emailExists(string $email): bool;

    public function findUserById(string $id): ?UserInterface;

    public function findUserByEmail(string $email): ?UserInterface;

    public function rollback(): void;

    public function save(UserInterface $user): bool;

    public function update(UserInterface $user): bool;
}
