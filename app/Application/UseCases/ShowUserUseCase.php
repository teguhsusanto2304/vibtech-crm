<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UserShowDto;
use App\Domain\Repositories\UserRepositoryInterface;

class ShowUserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id): ?UserShowDto
    {
        $userEntity = $this->userRepository->findById($id);

        if (!$userEntity) {
            return null;
        }

        return new UserShowDto(
            id: $userEntity->getId(),
            name: $userEntity->getName(),
            email: $userEntity->getEmail()
        );
    }
}