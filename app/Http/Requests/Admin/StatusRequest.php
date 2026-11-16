<?php

namespace App\Http\Requests\Admin;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $statusId = $this->route('status')?->id;
        $isCreate = $this->isMethod('post');

        return [
            'entity' => [
                $isCreate ? 'required' : 'nullable',
                Rule::in(array_keys(Status::entities())),
            ],
            'code' => [
                $isCreate ? 'required' : 'nullable',
                'alpha_dash',
                'max:50',
                Rule::unique('statuses', 'code')
                    ->where(fn ($query) => $query->where('entity', $this->input('entity')))
                    ->ignore($statusId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', Rule::in(['lime', 'amber', 'red', 'forest', 'neutral'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order') ?: 0,
        ]);
    }
}
