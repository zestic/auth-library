<?php

declare(strict_types=1);

namespace Zestic\Auth\Context;

class RegistrationContext extends AbstractContext
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->data['email'],
            'additionalData' => $this->data['additionalData'] ?? [],
        ];
    }
}
