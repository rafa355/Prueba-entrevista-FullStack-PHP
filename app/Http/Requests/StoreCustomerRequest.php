<?php

namespace App\Http\Requests;

use App\Models\Commune;
use App\Models\Region;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dni' => 'required|string|max:20',
            'id_reg' => 'required|integer',
            'id_com' => 'required|integer',
            'email' => 'required|email|max:191',
            'password' => 'required|string',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $region = Region::where('id_reg', $this->id_reg)->first();

            if (! $region) {
                $validator->errors()->add('id_reg', 'La región ingresada no existe.');

                return;
            }

            $commune = Commune::where('id_com', $this->id_com)->first();

            if (! $commune) {
                $validator->errors()->add('id_com', 'La comuna ingresada no existe.');

                return;
            }

            if ($commune->id_reg != $this->id_reg) {
                $validator->errors()->add('id_com', 'La comuna no pertenece a la región ingresada.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
