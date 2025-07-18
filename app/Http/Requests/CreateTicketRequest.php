<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated with client guard and has client role
        return Auth::guard('client')->check() && 
               Auth::guard('client')->user()->role_id === 3;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id,is_active,1',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Please provide a subject for your ticket.',
            'description.required' => 'Please describe your issue in detail.',
            'description.min' => 'Please provide at least 10 characters in the description.',
            'category_id.required' => 'Please select a category for your ticket.',
            'category_id.exists' => 'The selected category is invalid.',
            'priority.required' => 'Please select a priority level.',
            'priority.in' => 'Invalid priority level selected.',
            'attachment.mimes' => 'Only PDF, images, and document files are allowed.',
            'attachment.max' => 'File size should not exceed 10MB.',
        ];
    }
}