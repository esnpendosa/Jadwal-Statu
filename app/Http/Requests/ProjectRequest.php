<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:3000',
            'location'    => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:planning,draft,active,on_hold,completed,cancelled',
            'pic_id'       => 'nullable|exists:users,id',
            'manager_name' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => __('projects.validation.name_required'),
            'location.required'   => __('projects.validation.location_required'),
            'start_date.required' => __('projects.validation.start_date_required'),
            'end_date.required'   => __('projects.validation.end_date_required'),
            'end_date.after_or_equal' => __('projects.validation.end_date_after'),
        ];
    }
}
