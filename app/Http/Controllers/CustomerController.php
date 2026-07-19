<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        return $this->customerService->store($request);
    }

    public function show(ShowCustomerRequest $request): JsonResponse
    {
        return $this->customerService->show($request);
    }

    public function destroy(string $dni): JsonResponse
    {
        return $this->customerService->destroy($dni);
    }
}
