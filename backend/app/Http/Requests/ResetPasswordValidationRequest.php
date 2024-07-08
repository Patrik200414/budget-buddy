<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordValidationRequest extends FormRequest
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
            'password'=>['required', 'min:6', 'max:255', 'confirmed']
        ];
    }

    public function messages(){
        return [
            'password.required'=>'Please fill out the password collumn!',
            'password.min'=>'Password should be at least 6 characters!',
            'password.max'=>'Password should be upmost 255 characters!',
            'password.confirmed'=>'Password and password confirmation are not matching!'
        ];
    }
}
