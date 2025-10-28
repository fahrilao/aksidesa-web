<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestLegalLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow administrators to update any request
        // Allow operators and RW users to update requests they created or are assigned to
        $user = auth()->user();
        $request = $this->route('requestLegalLetter');
        
        if ($user->role === 'Administrator') {
            return true;
        }
        
        // For operators and RW users, they can only update if they created it or are assigned to it
        return $request && (
            $request->created_by === $user->id || 
            $request->assigned_to === $user->id
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
        ];
    }
}
