<?php

namespace App\Application\DTOs;

class UserShowDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {}
}