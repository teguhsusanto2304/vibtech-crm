<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UserShowDto;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Collection;

class ListUsersUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $filters
     * @param int $perPage
     * @return array{'data': array<int, UserShowDto>, 'meta': array}
     */
    public function execute(array $filters = [], int $perPage = 15): array
    {
        $paginatedResult = $this->userRepository->findAll($filters, $perPage);

        // Ambil data entitas dari hasil yang dipaginasi
        $userEntities = $paginatedResult['data'];
        $meta = $paginatedResult['meta'];

        // Petakan entitas domain ke DTO
        $usersDto = $userEntities->map(function ($userEntity) {
            return new UserShowDto(
                id: $userEntity->getId(),
                name: $userEntity->getName(),
                email: $userEntity->getEmail()
            );
        })->toArray();

        // Gabungkan data yang sudah dipetakan dengan metadata
        return [
            'data' => $usersDto,
            'meta' => $meta,
        ];
    }
}