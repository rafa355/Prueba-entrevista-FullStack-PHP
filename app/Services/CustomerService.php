<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerService
{
    public function index(): JsonResponse
    {
        $customers = Customer::where('status', 'A')
            ->with([
                'region:id_reg,description',
                'commune:id_com,description',
            ])
            ->get()
            ->map(function ($customer) {
                return [
                    'dni' => $customer->dni,
                    'email' => $customer->email,
                    'name' => $customer->name,
                    'last_name' => $customer->last_name,
                    'address' => $customer->address,
                    'region' => $customer->region->description,
                    'commune' => $customer->commune->description,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $customer = Customer::create([
            'dni' => $request->dni,
            'id_reg' => $request->id_reg,
            'id_com' => $request->id_com,
            'email' => $request->email,
            'password' => $request->password,
            'name' => $request->name,
            'last_name' => $request->last_name,
            'address' => $request->address ?? null,
            'date_reg' => now()->format('Y-m-d H:i:s'),
            'status' => 'A',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente registrado exitosamente.',
            'data' => $customer->only(['dni', 'email', 'name', 'last_name']),
        ], 201);
    }

    public function show(Request $request): JsonResponse
    {
        $query = Customer::where('status', 'A')
            ->with([
                'region:id_reg,description',
                'commune:id_com,description',
            ]);

        if ($request->filled('dni')) {
            $query->where('dni', $request->dni);
        }

        if ($request->filled('email')) {
            $query->where('email', $request->email);
        }

        $customer = $query->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $customer->name,
                'last_name' => $customer->last_name,
                'address' => $customer->address,
                'region' => $customer->region->description,
                'commune' => $customer->commune->description,
            ],
        ]);
    }

    public function destroy(string $dni): JsonResponse
    {
        $customer = Customer::where('dni', $dni)->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no existe',
            ], 404);
        }

        if ($customer->status === 'trash') {
            return response()->json([
                'success' => false,
                'message' => 'Registro no existe',
            ], 404);
        }

        Customer::where('dni', $dni)
            ->where('id_reg', $customer->id_reg)
            ->where('id_com', $customer->id_com)
            ->update(['status' => 'trash']);

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente.',
        ]);
    }
}
