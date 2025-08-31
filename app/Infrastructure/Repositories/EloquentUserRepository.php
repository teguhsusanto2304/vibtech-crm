<?php
namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User as UserEntity;
use App\Domain\Entities\UserList as UserListEntity;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;
use Illuminate\Support\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity
    {
        $userModel = UserModel::find($id);

        if (!$userModel) {
            return null;
        }

        return new UserEntity(
            id: $userModel->id,
            email: $userModel->email,
            password: $userModel->password            
        );
    }

    public function findByEmail($email): ?UserListEntity
    {
        $userModel = UserModel::where('email',$email)->get();

        if (!$userModel) {
            return null;
        }

        return new UserListEntity(
            id: $userModel->id,
            name: $userModel->name,
            email: $userModel->email
        );
    }

    /**
     * @param array $filters
     * @param int $perPage
     * @return array{'data': Collection<int, UserEntity>, 'meta': array}
     */
    public function findAll(array $filters, int $perPage): array
    {
        $query = UserModel::query();

        // Logika filtering
        if (isset($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
        }

        // Jalankan paginasi dan dapatkan hasilnya sebagai array
        $paginatedModels = $query->paginate($perPage);

        // Petakan model Eloquent ke entitas Domain
        $userEntities = collect($paginatedModels->items())->map(function ($userModel) {
            return new UserListEntity(
                id: $userModel->id,
                name: $userModel->name,
                email: $userModel->email
            );
        });

        // Kembalikan data dan metadata paginasi
        return [
            'data' => $userEntities,
            'meta' => [
                'current_page' => $paginatedModels->currentPage(),
                'last_page' => $paginatedModels->lastPage(),
                'total' => $paginatedModels->total(),
                'per_page' => $paginatedModels->perPage(),
            ],
        ];
    }
}