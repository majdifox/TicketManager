<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isAgent());
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