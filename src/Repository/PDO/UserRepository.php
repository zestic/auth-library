<?php

declare(strict_types=1);

namespace Zestic\Auth\Repository\PDO;

use PDO;
use Zestic\Auth\Context\RegistrationContext;
use Zestic\Auth\Contract\Entity\UserInterface;
use Zestic\Auth\Contract\Repository\UserHydrationInterface;
use Zestic\Auth\Contract\Repository\UserRepositoryInterface;
use Zestic\Auth\Entity\User;
use Zestic\Auth\Entity\Identifier;

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
            'INSERT INTO ' . $this->schema . 'users (id, email, display_name, additional_data, system_id, verified_at)
            VALUES (:id, :email, :display_name, :additional_data, :system_id, :verified_at)'
        );
        $data = $context->toArray();
        $data['id'] = $id;
        try {
            $stmt->execute($data);
            // Persist identifiers if present
            if (!empty($data['identifiers']) && is_array($data['identifiers'])) {
                foreach ($data['identifiers'] as $identifier) {
                    if ($identifier instanceof Identifier) {
                        $this->insertIdentifier($id, $identifier);
                    } elseif (is_array($identifier) && isset($identifier['provider'], $identifier['id'])) {
                        $this->insertIdentifier($id, new Identifier($identifier['provider'], $identifier['id'], $identifier['raw_data'] ?? null));
                    }
                }
            }

            return $id;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to create user', 0, $e);
        }
    }

    private function insertIdentifier(string|int $userId, Identifier $identifier): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . $this->schema . 'user_identifiers (user_id, provider, identifier_id, raw_data)
            VALUES (:user_id, :provider, :identifier_id, :raw_data)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'provider' => $identifier->getProvider(),
            'identifier_id' => $identifier->getId(),
            'raw_data' => $identifier->getRawData(),
        ]);
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
            'SELECT additional_data, email, display_name, id, system_id, verified_at
            FROM ' . $this->schema . 'users
            WHERE ' . $field . ' = :' . $field
        );
        $stmt->execute([$field => $value]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData === false) {
            return null;
        }

        // Load identifiers from user_identifiers table
        $userData['identifiers'] = $this->fetchIdentifiers($userData['id']);

        return $this->hydration->hydrate($userData);
    }

    /**
     * @return array<Identifier>
     */
    private function fetchIdentifiers(string|int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT provider, identifier_id, raw_data FROM ' . $this->schema . 'user_identifiers WHERE user_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $identifiers = [];
        foreach ($rows as $row) {
            $identifiers[$row['provider']] = new Identifier($row['provider'], $row['identifier_id'], $row['raw_data']);
        }

        return $identifiers;
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
            system_id = :system_id,
            verified_at = :verified_at
        WHERE id = :id'
        );

        try {
            $result = $stmt->execute($this->hydration->dehydrate($user));

            // Update identifiers: delete old, insert new
            $this->deleteIdentifiers($user->getId());
            foreach ($user->getIdentifiers() as $identifier) {
                $this->insertIdentifier($user->getId(), $identifier);
            }

            return $result && $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to update user', 0, $e);
        }
    }

    private function deleteIdentifiers(string|int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM ' . $this->schema . 'user_identifiers WHERE user_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
    }
}
