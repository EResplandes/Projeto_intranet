<?php

namespace App\Services;

use App\Http\Resources\UsuarioResource;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\PermissoesUsers;

class AutenticacaoService
{
    public function login($request)
    {
        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->senha,
            ];

            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'token' => null,
                    'usuario' => null,
                    'http_code' => 401,
                ];
            }

            $permissoes = PermissoesUsers::where('usuario_id', Auth::user()->id)->first();

            $usuario = UsuarioResource::collection(User::where('email', $request->email)->get());

            return [
                'token' => $token,
                'usuario' => $usuario,
                'http_code' => 200,
                'status' => true,
                'permissoes' => $permissoes
            ];
        } catch (\Throwable $th) {
            return [
                'token' => null,
                'usuario' => null,
                'http_code' => 500,
                'status' => false
            ];
        }
    }
}
