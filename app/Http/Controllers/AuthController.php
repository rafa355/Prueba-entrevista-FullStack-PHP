<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Token;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:191',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)
            ->where('status', 'A')
            ->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $now = now()->format('Y-m-d H:i:s');
        $random = random_int(200, 500);
        $plainToken = $customer->email . $now . $random;
        $hashedToken = sha1($plainToken);

        $token = Token::create([
            'token' => $hashedToken,
            'email' => $customer->email,
            'date_reg' => $now,
            'ttl' => 60,
            'status' => 'A',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'data' => [
                'token' => $token->token,
                'expires_in' => $token->ttl . ' minutos',
            ],
        ]);
    }
}
