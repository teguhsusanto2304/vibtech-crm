<?php
namespace App\Domain\Entities;

class UserList
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}