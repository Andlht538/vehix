<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
            'vehicule_id' => ['required', 'exists:vehicules,id'],
            'company' => ['required', 'string', 'max:200'],
            'policy_number' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'premium' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'deductible' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'coverage_details' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
