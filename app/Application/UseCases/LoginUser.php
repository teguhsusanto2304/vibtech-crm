<?php
namespace App\Application\UseCases;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Application\DTOs\UserLoginDto;

class LoginUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UserLoginDto $dto): ?User
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            return null; // or throw an exception
        }

        return $user;
    }
}