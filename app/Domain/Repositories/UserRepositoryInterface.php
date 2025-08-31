<?php
namespace App\Domain\Repositories;

use App\Domain\Entities\User;
use App\Domain\Entities\UserList;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?UserList;

    /**
     * @return Collection<int, User>
     */
    public function findAll(array $filters, int $perPage): array;
}