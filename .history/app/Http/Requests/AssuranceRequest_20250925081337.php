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

     public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'company.required' => 'La compagnie d\'assurance est obligatoire.',
            'policy_number.required' => 'Le numéro de police est obligatoire.',
            'start_date.required' => 'La date de début est obligatoire.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',
            'premium.required' => 'La prime est obligatoire.',
            'premium.numeric' => 'La prime doit être un nombre.',
            'premium.min' => 'La prime ne peut pas être négative.',
            'deductible.required' => 'La franchise est obligatoire.',
            'deductible.numeric' => 'La franchise doit être un nombre.',
            'deductible.min' => 'La franchise ne peut pas être négative.',
        ];
    }
}
