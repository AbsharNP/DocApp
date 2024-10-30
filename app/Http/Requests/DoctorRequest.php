<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Set to true or implement your own authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'phone_number' => 'required|string|unique:doctors,phone_number|digits:10', 
            'department_id' =>'|exists:departments,id',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The doctor\'s name is required.',
            'email.required' => 'The email address is required.',
            'email.unique' => 'The email address has already been taken.',
            'doc_id.required' => 'The doctor ID is required.',
            'doc_id.unique' => 'The doctor ID has already been taken.',
            'phone_number.required' => 'The phone number is required.',
            'department_id.required' => 'The department ID is required.',
            'department_id.exists' => 'The selected department does not exist.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters long.',
        ];
    }
}
