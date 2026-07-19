<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Token;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $email, string $password): JsonResponse
    {
        $customer = Customer::where('email', $email)
            ->where('status', 'A')
            ->first();

        if (! $customer || ! Hash::check($password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $token = $this->generateToken($customer);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'data' => [
                'token' => $token->token,
                'expires_in' => $token->ttl.' minutos',
            ],
        ]);
    }

    private function generateToken(Customer $customer): Token
    {
        $now = now()->format('Y-m-d H:i:s');
        $random = random_int(200, 500);
        $plainToken = $customer->email.$now.$random;
        $hashedToken = sha1($plainToken);

        return Token::create([
            'token' => $hashedToken,
            'email' => $customer->email,
            'date_reg' => $now,
            'ttl' => 60,
            'status' => 'A',
        ]);
    }
}
