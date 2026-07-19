<?php

namespace App\Http\Middleware;

use App\Models\Commune;
use App\Models\Region;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCustomerStoreMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $errors = [];

        $required = ['dni', 'id_reg', 'id_com', 'email', 'name', 'last_name', 'password'];

        foreach ($required as $field) {
            if (! $request->filled($field)) {
                $errors[$field] = "El campo {$field} es obligatorio.";
            }
        }

        if ($request->filled('email') && ! filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El campo email debe ser un correo válido.';
        }

        if (! empty($errors)) {
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        $region = Region::where('id_reg', $request->id_reg)->first();

        if (! $region) {
            return response()->json([
                'success' => false,
                'message' => 'La región ingresada no existe.',
            ], 422);
        }

        $commune = Commune::where('id_com', $request->id_com)->first();

        if (! $commune) {
            return response()->json([
                'success' => false,
                'message' => 'La comuna ingresada no existe.',
            ], 422);
        }

        if ($commune->id_reg != $request->id_reg) {
            return response()->json([
                'success' => false,
                'message' => 'La comuna no pertenece a la región ingresada.',
            ], 422);
        }

        return $next($request);
    }
}
