<?php

declare(strict_types=1);

namespace Payments\Request;

use Hyperf\Validation\Request\FormRequest;

class PaymentCreateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'order_id' => 'required|integer|min:1',
        ];
    }
}
