<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentTypeRequest extends FormRequest
{
    public const GROUPS = [
        'bank_transfer' => 'Bank Transfer',
        'ewallet' => 'E-Wallet',
        'qris' => 'QRIS',
        'card' => 'Kartu',
        'retail' => 'Gerai Retail',
        'paylater' => 'Paylater',
        'other' => 'Lainnya',
    ];

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paymentTypeId = $this->route('paymentType')?->id;

        return [
            'code' => ['required', 'alpha_dash', 'max:50', Rule::unique('payment_types', 'code')->ignore($paymentTypeId)],
            'name' => ['required', 'string', 'max:100'],
            'group' => ['nullable', Rule::in(array_keys(self::GROUPS))],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => Str::lower($this->input('code'))]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order') ?: 0,
        ]);
    }
}
