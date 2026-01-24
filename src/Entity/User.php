<?php

declare(strict_types=1);

namespace Zestic\Auth\Entity;

use Carbon\CarbonInterface;
use Zestic\Auth\Contract\Entity\UserInterface;

class User implements UserInterface
{
    /** @var array<string, mixed> */
    private array $additionalData = [];
    private ?string $displayName = null;
    private string $email;
    private string|int $id;
    private ?string $identifier = null;
    private string|int|null $systemId = null;
    private ?CarbonInterface $verifiedAt = null;

    /**
     * @return array<string, mixed>|null
     */
    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    /**
     * @param array<string, mixed>|null $additionalData
     */
    public function setAdditionalData(?array $additionalData): self
    {
        $this->additionalData = $additionalData ?? [];

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function setId(string|int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getSystemId(): string|int|null
    {
        return $this->systemId;
    }

    public function setSystemId(string|int|null $systemId): self
    {
        $this->systemId = $systemId;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->verifiedAt !== null;
    }

    public function getVerifiedAt(): ?CarbonInterface
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(CarbonInterface $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }
}
