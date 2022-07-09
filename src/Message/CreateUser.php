<?php

declare(strict_types=1);

namespace App\Message;

class CreateUser
{
    public function __construct(private readonly string $email, private readonly string $password)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
