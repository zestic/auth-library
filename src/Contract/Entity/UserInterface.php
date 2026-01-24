<?php

declare(strict_types=1);

namespace Zestic\Auth\Contract\Entity;

use Carbon\CarbonInterface;

interface UserInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getAdditionalData(): ?array;

    public function getDisplayName(): ?string;

    public function getEmail(): string;

    public function getId(): string|int;

    public function getSystemId(): string|int|null;

    public function isVerified(): bool;

    public function getVerifiedAt(): ?CarbonInterface;
}
