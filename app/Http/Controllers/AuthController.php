<?php

namespace App\Http\Controllers;

use App\Application\UseCases\LoginUser;
use App\Application\UseCases\ShowUserUseCase;
use App\Application\UseCases\ListUsersUseCase;
use App\Application\DTOs\UserLoginDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private ShowUserUseCase $showUserUseCase;
    private ListUsersUseCase $listUsersUseCase;

    public function __construct(
        private LoginUser $loginUser,
        ShowUserUseCase $showUserUseCase, 
        ListUsersUseCase $listUsersUseCase
    ) {
        $this->showUserUseCase = $showUserUseCase;
        $this->listUsersUseCase = $listUsersUseCase;
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        }

        $dto = new UserLoginDto(
            email: $request->input('email'),
            password: $request->input('password'),
        );

        $user = $this->loginUser->execute($dto);

    if ($user) {
        // Here, we explicitly log in the user to the session.
        // This is good practice if your API and web routes share a session.
        Auth::loginUsingId($user->id);

        // Revoke all existing tokens for the user to ensure a fresh one is used
        // for a more secure approach
        auth()->user()->tokens()->delete();

        // Create a new Sanctum token for the user
        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token, // This is the token you send to the frontend
            'token_type' => 'Bearer',
        ]);
    }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function show(int $id): JsonResponse
    {
        $userDto = $this->showUserUseCase->execute($id);

        if (!$userDto) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $userDto->id,
            'name' => $userDto->name,
            'email' => $userDto->email,
        ]);
    }
    
    public function list(Request $request): JsonResponse
    {
        // Call the new ListUsersUseCase
        $perPage = $request->input('per_page', 15);
        $filters = [
            'search' => $request->input('search'),
        ];
        
        // Panggil Use Case dengan parameter yang diterima
        $paginatedUsers = $this->listUsersUseCase->execute($filters, $perPage);

        return response()->json($paginatedUsers);
    }
}