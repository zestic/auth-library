<?php

declare(strict_types=1);

namespace Zestic\Auth\Repository\PDO;

use PDO;
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

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM ' . $this->schema . 'users WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findUserBy('email', $email);
    }

    public function findUserById(string $id): ?UserInterface
    {
        return $this->findUserBy('id', $id);
    }

    public function findUserByIdentity(string $identifier, string|int $id): ?UserInterface
    {
        $sql = $this->selectFromSQL()
        . 'LEFT JOIN ' . $this->schema . 'user_identifiers AS ui ON u.id = ui.user_id '
        . 'WHERE ui.provider = :provider AND ui.identifier_id = :identifier_id '
        . 'AND ui.deleted_at IS NULL';

        $params = [
            'provider' => $identifier,
            'identifier_id' => $id,
        ];

        return $this->findUser($sql, $params);
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * @param User $user
     */
    public function save(UserInterface|User $user): bool
    {
        $id = $this->generateUniqueIdentifier();
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . $this->schema . 'users (id, email, display_name)
            VALUES (:id, :email, :display_name)'
        );
        $stmt->execute([
            'id' => $id,
            'email' => $user->getEmail(),
            'display_name' => $user->getDisplayName(),
        ]);
        $user->setId($id);

        return $this->update($user);
    }

    /**
     * @param User $user
     */
    public function update(UserInterface|User $user): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ' . $this->schema . ' users AS u
        SET email = :email,
            display_name = :display_name,
            additional_data = :additional_data,
            system_id = :system_id,
            verified_at = :verified_at
        WHERE u.id = :id'
        );

        try {
            $result = $stmt->execute($this->hydration->dehydrate($user));

            // Update identifiers: only add/remove as needed
            $userId = $user->getId();
            $existing = $this->fetchAllIdentifiersRaw($userId); // all, including soft-deleted
            $new = $user->getIdentifiers();

            // Index by provider+id for comparison
            $existingMap = [];
            foreach ($existing as $row) {
                $key = $row['provider'] . '|' . $row['identifier_id'];
                $existingMap[$key] = $row;
            }
            $newMap = [];
            foreach ($new as $identifier) {
                $key = $identifier->getProvider() . '|' . $identifier->getId();
                $newMap[$key] = $identifier;
            }

            // Soft delete removed
            foreach ($existingMap as $key => $row) {
                if (!isset($newMap[$key]) && $row['deleted_at'] === null) {
                    $softDelete = $this->pdo->prepare(
                        'UPDATE ' . $this->schema . 'user_identifiers SET deleted_at = ' . $this->dbNow() . ' WHERE user_id = :user_id AND provider = :provider AND identifier_id = :identifier_id AND deleted_at IS NULL'
                    );
                    $softDelete->execute([
                        'user_id' => $userId,
                        'provider' => $row['provider'],
                        'identifier_id' => $row['identifier_id'],
                    ]);
                }
            }

            // Add or restore new
            foreach ($newMap as $key => $identifier) {
                $this->insertIdentifier($userId, $identifier);
            }

            return $result && $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to update user', 0, $e);
        }
    }

    /**
     * @return array<Identifier>
     */
    private function fetchIdentifiers(string|int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT provider, identifier_id, raw_data FROM ' . $this->schema . 'user_identifiers WHERE user_id = :user_id AND deleted_at IS NULL'
        );
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $identifiers = [];
        foreach ($rows as $row) {
            $identifiers[$row['provider']] = new Identifier(
                $row['provider'],
                $row['identifier_id'],
                json_decode($row['raw_data'], true)
            );
        }

        return $identifiers;
    }

    private function insertIdentifier(string|int $userId, Identifier $identifier): void
    {
        // Check if identifier exists (even soft-deleted)
        $stmt = $this->pdo->prepare(
            'SELECT deleted_at FROM ' . $this->schema . 'user_identifiers WHERE user_id = :user_id AND provider = :provider AND identifier_id = :identifier_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'provider' => $identifier->getProvider(),
            'identifier_id' => $identifier->getId(),
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            // If soft-deleted, restore it
            if ($row['deleted_at'] !== null) {
                $update = $this->pdo->prepare(
                    'UPDATE ' . $this->schema . 'user_identifiers SET deleted_at = NULL, raw_data = :raw_data WHERE user_id = :user_id AND provider = :provider AND identifier_id = :identifier_id'
                );
                $update->execute([
                    'raw_data' => $identifier->getJsonData(),
                    'user_id' => $userId,
                    'provider' => $identifier->getProvider(),
                    'identifier_id' => $identifier->getId(),
                ]);
            } else {
                // Already exists and not deleted, update raw_data if needed
                $update = $this->pdo->prepare(
                    'UPDATE ' . $this->schema . 'user_identifiers SET raw_data = :raw_data WHERE user_id = :user_id AND provider = :provider AND identifier_id = :identifier_id'
                );
                $update->execute([
                    'raw_data' => $identifier->getJsonData(),
                    'user_id' => $userId,
                    'provider' => $identifier->getProvider(),
                    'identifier_id' => $identifier->getId(),
                ]);
            }
        } else {
            // Insert new
            $insert = $this->pdo->prepare(
                'INSERT INTO ' . $this->schema . 'user_identifiers (user_id, provider, identifier_id, raw_data) VALUES (:user_id, :provider, :identifier_id, :raw_data)'
            );
            $insert->execute([
                'user_id' => $userId,
                'provider' => $identifier->getProvider(),
                'identifier_id' => $identifier->getId(),
                'raw_data' => $identifier->getJsonData(),
            ]);
        }
    }

    /**
     * Fetch all identifiers for a user, including soft-deleted.
     *
     * @return array<int, array{provider:string, identifier_id:string, raw_data:mixed, deleted_at:mixed}>
     */
    private function fetchAllIdentifiersRaw(string|int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT provider, identifier_id, raw_data, deleted_at FROM ' . $this->schema . 'user_identifiers WHERE user_id = :user_id '
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function findUserBy(string $field, string $value): ?UserInterface
    {
        $sql = $this->selectFromSQL() . 'WHERE u.' . $field . '= :' . $value;

        return $this->findUser($sql, [$field => $value]);
    }

    /**
     * @param string               $sql
     * @param array<string, mixed> $params
     *
     * @return null|UserInterface
     */
    private function findUser(string $sql, array $params): ?UserInterface
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData === false) {
            return null;
        }

        // Load identifiers from user_identifiers table
        $userData['identifiers'] = $this->fetchIdentifiers($userData['id']);

        return $this->hydration->hydrate($userData);
    }

    private function selectFromSQL(): string
    {
        return 'SELECT u.additional_data, u.email, u.display_name, u.id, u.system_id, u.verified_at
            FROM ' . $this->schema . 'users AS u ';
    }
}
