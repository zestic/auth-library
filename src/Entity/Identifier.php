<?php

declare(strict_types=1);

namespace Zestic\Auth\Entity;

class Identifier
{
    /**
     * @param array<mixed, mixed> $rawData
     */
    public function __construct(
        private string $provider,
        private string $id,
        private array $rawData = [],
    ) {}

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getJsonData(): string
    {
        return json_encode($this->rawData);
    }

    /**
     * @return array<mixed, mixed> $rawData
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}
