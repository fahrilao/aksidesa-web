<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'role' => ['sometimes', 'required', Rule::in(User::getRoles())],
            'company_id' => [
                'sometimes',
                'nullable',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($user) {
                    $role = $this->role ?? $user->role;
                    
                    // Administrator should not have company_id
                    if ($role === 'Administrator' && $value !== null) {
                        $fail('Administrator users should not be assigned to a company.');
                    }
                    // Non-Administrator users must have company_id
                    if ($role !== 'Administrator' && $value === null) {
                        $fail('This user role requires a company assignment.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }
}
