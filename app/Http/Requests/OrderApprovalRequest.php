<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Order Approval Request
 * 
 * Handles validation for order approval processing.
 * Ensures all required approval information is present and valid.
 *
 * @author Fahed
 * @package App\Http\Requests
 */
class OrderApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @author Fahed
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @author Fahed
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'approval_level' => 'required|in:first,second',
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @author Fahed
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'approval_level.required' => 'Approval level is required.',
            'approval_level.in' => 'Invalid approval level. Must be either "first" or "second".',
            'status.required' => 'Approval status is required.',
            'status.in' => 'Invalid approval status. Must be either "approved" or "rejected".',
            'notes.max' => 'Approval notes cannot exceed 1000 characters.'
        ];
    }
} 