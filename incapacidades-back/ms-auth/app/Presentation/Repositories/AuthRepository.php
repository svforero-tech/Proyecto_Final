<?php

namespace App\Presentation\Repositories;

use App\Models\Usuario;

class AuthRepository
{
    public function login(string $identificador, string $contrasena): ?array
    {
        $usuario = Usuario::where(function ($query) use ($identificador) {
            $query->where('usuario', $identificador)
                  ->orWhere('correo', $identificador);
        })->first();

        if (!$usuario || $usuario->contrasena !== $contrasena) {
            return null;
        }

        if ($usuario->estado !== 'activo') {
            return ['error' => 'El usuario se encuentra inactivo'];
        }

        $token = bin2hex(random_bytes(32));
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return [
            'usuario' => [
                'id'      => $usuario->id,
                'nombre'  => $usuario->nombre,
                'correo'  => $usuario->correo,
                'usuario' => $usuario->usuario,
                'rol'     => $usuario->rol
            ],
            'token' => $token
        ];
    }

    public function logout(string $token): bool
    {
        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            return false;
        }

        $usuario->token = null;
        $usuario->sesion_activa = false;
        return $usuario->save();
    }

    public function validarToken(string $token): ?Usuario
    {
        if (empty($token)) {
            return null;
        }

        return Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->where('estado', 'activo')
            ->first();
    }
}