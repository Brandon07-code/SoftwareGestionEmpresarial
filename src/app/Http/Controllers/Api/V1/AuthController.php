<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->sendError('Credenciales de acceso inválidas.', [
                'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.'],
            ], 401);
        }

        if (! $user->activo) {
            return $this->sendError('Usuario inactivo. Comuníquese con el administrador.', [], 403);
        }

        $deviceName = $validated['device_name'] ?? 'turnix_api_token';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ], 'Autenticación exitosa. Token generado correctamente.', 200);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->sendResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ], 'Perfil del usuario autenticado.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'Sesión finalizada y token revocado correctamente.', 200);
    }
}
