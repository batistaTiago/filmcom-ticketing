<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\LoginRequest;
use App\Services\AuthTokenGenerator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Str;

// TODO layerize this code

class AuthController extends Controller
{
    public function login(
        LoginRequest $request,
        AuthTokenGenerator $generator,
        UserRepositoryInterface $userRepository,
        HashManager $hashManager
    )
    {
        try {
            $user = $userRepository->findByEmail($request->email);
        } catch (ResourceNotFoundException $_) {
            throw new AuthenticationException();
        }

        if ((!$user) || (!$hashManager->check($request->password, $user->password))) {
            throw new AuthenticationException();
        }
        $token = $generator->createToken($user, $request->device_id ?? Str::orderedUuid()->toString());

        return response()->json([
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'token' => $request->bearerToken(),
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([], 204);
    }
}
