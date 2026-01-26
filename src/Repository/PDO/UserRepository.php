<?php

declare(strict_types=1);

namespace Zestic\Auth\Repository\PDO;

use PDO;
use Zestic\Auth\Context\RegistrationContext;
use Zestic\Auth\Contract\Entity\UserInterface;
use Zestic\Auth\Contract\Repository\UserHydrationInterface;
use Zestic\Auth\Contract\Repository\UserRepositoryInterface;
use Zestic\Auth\Entity\User;

class UserRepository extends AbstractPDORepository implements UserRepositoryInterface
{
    /**
     * @param PDO                          $pdo
     * @param UserHydrationInterface<User> $hydration
     */
    public function __construct(
        PDO $pdo,
        private UserHydrationInterface $hydration,
    ) {
        parent::__construct($pdo);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function create(RegistrationContext $context): string|int
    {
        $id = $this->generateUniqueIdentifier();
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . $this->schema . 'users (id, email, display_name, additional_data, identifiers, verified_at)
            VALUES (:id, :email, :display_name, :additional_data, :identifiers, :verified_at)'
        );
        $additionalData = $context->get('additionalData');

        try {
            $data = $context->toArray();
            $data['id'] = $id;
            $stmt->execute($data);

            return $id;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to create user', 0, $e);
        }
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findUserBy('email', $email);
    }

    public function findUserById(string $id): ?UserInterface
    {
        return $this->findUserBy('id', $id);
    }

    private function findUserBy(string $field, string $value): ?UserInterface
    {
        $stmt = $this->pdo->prepare(
            'SELECT additional_data, identifiers, email, display_name, id, system_id, verified_at
            FROM ' . $this->schema . 'users
            WHERE ' . $field . ' = :' . $field
        );
        $stmt->execute([$field => $value]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData === false) {
            return null;
        }

        return $this->hydration->hydrate($userData);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM ' . $this->schema . 'users WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * @param User $user
     */
    public function update(UserInterface|User $user): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ' . $this->schema . 'users
        SET email = :email,
            display_name = :display_name,
            additional_data = :additional_data,
            identifiers = :identifiers,
            system_id = :system_id,
            verified_at = :verified_at
        WHERE id = :id'
        );

        try {
            $result = $stmt->execute($this->hydration->dehydrate($user));

            return $result && $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to update user', 0, $e);
        }
    }
}
