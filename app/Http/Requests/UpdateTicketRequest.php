<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check admin guard first
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            return $user && $user->role_id === 1; // Admin role check
        }
        
        // Check agent guard
        if (Auth::guard('agent')->check()) {
            $user = Auth::guard('agent')->user();
            return $user && $user->role_id === 2; // Agent role check
        }
        
        // Check default guard (fallback)
        $user = auth()->user();
        return $user && in_array($user->role_id, [1, 2]); // Admin or Agent
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|required|in:' . implode(',', Ticket::getStatuses()),
            'priority' => 'sometimes|required|in:' . implode(',', Ticket::getPriorities()),
            'agent_id' => 'sometimes|nullable|exists:agents,id',
            'category_id' => 'sometimes|required|exists:categories,id,is_active,1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Invalid status selected.',
            'priority.in' => 'Invalid priority level selected.',
            'agent_id.exists' => 'The selected agent does not exist.',
            'category_id.exists' => 'The selected category is invalid or inactive.',
        ];
    }
}