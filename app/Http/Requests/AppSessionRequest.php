<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Change to true to allow validation
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'doctor_id' => 'required|exists:doctors,id',
            'dr_name' => 'required|string',
            'session_date' => 'required|date|unique:appsessions,session_date,NULL,id,doctor_id,' . $this->doctor_id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'no_of_sessions' => 'required|integer|min:1',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'doctor_id.required' => 'Doctor ID is required.',
            'doctor_id.exists' => 'Doctor not found.',
            'session_date.required' => 'Session date is required.',
            'session_date.date' => 'Invalid session date.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Invalid start time format.',
            'end_time.required' => 'End time is required.',
            'end_time.date_format' => 'Invalid end time format.',
            'end_time.after' => 'End time must be after start time.',
            'no_of_sessions.required' => 'Number of sessions is required.',
            'no_of_sessions.integer' => 'Number of sessions must be an integer.',
            'no_of_sessions.min' => 'Number of sessions must be at least 1.',
        ];
    }
}
