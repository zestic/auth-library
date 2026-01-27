<?php

declare(strict_types=1);

namespace Zestic\Auth\Contract\Entity;

use Carbon\CarbonInterface;
use Zestic\Auth\Entity\Identifier;

interface UserInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getAdditionalData(): ?array;

    public function getDisplayName(): ?string;

    public function getEmail(): string;

    public function getId(): string|int;

    /**
     * @return array<string, Identifier>
     */
    public function getIdentifiers(): array;

    public function getIdentifierByProvider(string $provider): ?Identifier;

    public function getSystemId(): string|int|null;

    public function isVerified(): bool;

    public function getVerifiedAt(): ?CarbonInterface;
}
