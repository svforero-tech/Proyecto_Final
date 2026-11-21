<?php

namespace App\Controllers;

use App\Presentation\Repositories\AuthRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    private AuthRepository $repository;

    public function __construct()
    {
        $this->repository = new AuthRepository();
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    private function extraerToken(Request $request): string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }
        return $authHeader;
    }

    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? [];
        $identificador = trim($body['usuario'] ?? $body['correo'] ?? '');
        $contrasena = $body['contrasena'] ?? '';

        if ($identificador === '' || $contrasena === '') {
            return $this->jsonResponse($response, [
                'ok' => false,
                'mensaje' => 'Debe ingresar usuario/correo y contraseña'
            ], 400);
        }

        $resultado = $this->repository->login($identificador, $contrasena);

        if (!$resultado) {
            return $this->jsonResponse($response, [
                'ok' => false,
                'mensaje' => 'Credenciales incorrectas'
            ], 401);
        }

        if (isset($resultado['error'])) {
            return $this->jsonResponse($response, [
                'ok' => false,
                'mensaje' => $resultado['error']
            ], 403);
        }

        return $this->jsonResponse($response, [
            'ok' => true,
            'mensaje' => 'Inicio de sesión exitoso',
            'data' => $resultado
        ], 200);
    }

    public function logout(Request $request, Response $response): Response
    {
        $token = $this->extraerToken($request);

        if ($token === '') {
            return $this->jsonResponse($response, [
                'ok' => false,
                'mensaje' => 'Token no proporcionado'
            ], 400);
        }

        $cerrado = $this->repository->logout($token);

        if (!$cerrado) {
            return $this->jsonResponse($response, [
                'ok' => false,
                'mensaje' => 'Token inválido o sesión ya cerrada'
            ], 401);
        }

        return $this->jsonResponse($response, [
            'ok' => true,
            'mensaje' => 'Sesión cerrada correctamente'
        ], 200);
    }

    public function validar(Request $request, Response $response): Response
    {
        $token = $this->extraerToken($request);
        $usuario = $this->repository->validarToken($token);

        if (!$usuario) {
            return $this->jsonResponse($response, [
                'ok' => false,
                'mensaje' => 'Sesión inválida o expirada'
            ], 401);
        }

        return $this->jsonResponse($response, [
            'ok' => true,
            'mensaje' => 'Sesión válida',
            'usuario' => [
                'id'      => $usuario->id,
                'nombre'  => $usuario->nombre,
                'usuario' => $usuario->usuario,
                'rol'     => $usuario->rol
            ]
        ], 200);
    }
}