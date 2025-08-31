<?php
namespace App\Application\DTOs;

class UserLoginDto
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
}