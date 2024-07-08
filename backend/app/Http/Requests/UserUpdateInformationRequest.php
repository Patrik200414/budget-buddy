<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateInformationRequest extends FormRequest
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
            'firstName'=>['required', 'min:2', 'max:255'],
            'lastName'=>['required', 'min:2', 'max:255'],
            'email'=>['required', 'email'],
            'previousPassword'=>['required', 'min:6', 'max:255'],
            'password'=>['required', 'confirmed', 'min:6', 'max:255'],
        ];
    }

    public function messages(){
        return [
            'firstName.min' => 'First name should be at least 2 characters!',
            'firstName.max' => 'First name shouldn\'t exceed 255 characters!',
            'lastName.min' => 'Last name should be at least 2 characters!',
            'lastName.max' => 'Last name shouldn\'t exceed 255 characters!',
            'email.email' => 'Email should be in email format!',
            'password.confirmed' => 'New password and password confirmation doesn\'t match!',
            'password.min' => 'New password should be at least 6 characters!',
            'password.max' => 'New password shouldn\'t exceed 255 characters!',
            'previousPassword.min' => 'New password should be at least 6 characters!',
            'previousPassword.max' => 'New password shouldn\'t exceed 255 characters!',
        ];
    }
}
