<?php

namespace App\Http\Controllers;

use App\Services\RegionService;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    public function __construct(
        private readonly RegionService $regionService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->regionService->index();
    }
}
