<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdministrator();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(User::getRoles())],
        ];

        // Add company_id validation based on role
        if ($this->input('role') === 'Administrator') {
            $rules['company_id'] = [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        $fail('Administrator users should not be assigned to a company.');
                    }
                }
            ];
        } else {
            $rules['company_id'] = 'required|exists:companies,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_id.required_unless' => 'Company is required for all users except Administrator.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }
}
