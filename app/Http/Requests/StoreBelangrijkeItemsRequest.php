<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBelangrijkeItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'stockbeheerder';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'materiaal_ids' => ['nullable', 'array'],
            'materiaal_ids.*' => ['integer', 'exists:materialen,id'],
        ];
    }

    /**
     * @return list<int>
     */
    public function materiaalIds(): array
    {
        return collect($this->input('materiaal_ids', []))
            ->map(fn ($id): int => (int) $id)
            ->all();
    }
}
