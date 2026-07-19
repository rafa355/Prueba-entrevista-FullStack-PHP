<?php

namespace App\Services;

use App\Models\Region;
use Illuminate\Http\JsonResponse;

class RegionService
{
    public function index(): JsonResponse
    {
        $regions = Region::where('status', 'A')
            ->with([
                'communes:id_com,id_reg,description,status',
            ])
            ->get()
            ->map(function ($region) {
                return [
                    'id_reg' => $region->id_reg,
                    'description' => $region->description,
                    'communes' => $region->communes
                        ->where('status', 'A')
                        ->map(function ($commune) {
                            return [
                                'id_com' => $commune->id_com,
                                'description' => $commune->description,
                            ];
                        })
                        ->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $regions,
        ]);
    }
}
