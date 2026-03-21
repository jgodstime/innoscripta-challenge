<?php

namespace App\Services\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    public function __construct(
        private User $user
    ) {}

    public function login(array $data): array
    {
        $user = $this->user->where('email', $data['email'])->first();

        if (! $user) {
            return $this->error('Invalid login credential.', [], 401);
        }

        if (! $user) {
            return $this->error('Invalid login credential', [], 401);
        }

        if (! Hash::check($data['password'], $user->password)) {
            return $this->error('Invalid login credential', [], 401);
        }

        // Revoke all other tokens
        // $user->tokens()->delete();

        $token = $user->createToken($user->email)->plainTextToken;
        $user->token = $token;

        return $this->success('Your are logged in', new UserResource($user));
    }
}
