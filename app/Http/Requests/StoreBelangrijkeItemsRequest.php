<?php

namespace App\Http\Requests;

use App\Enums\FloodRiskLevel;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBelangrijkeItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role_id === Role::STOCKBEHEERDER;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $validLevels = array_column(FloodRiskLevel::cases(), 'value');

        return [
            'materiaal_ids' => ['nullable', 'array'],
            'materiaal_ids.*' => ['integer', 'exists:materialen,id'],
            'risk_levels' => ['nullable', 'array'],
            'risk_levels.*' => ['string', Rule::in($validLevels)],
        ];
    }

    /**
     * Returns an associative array of materiaal_id => risk_level string,
     * defaulting to 'medium' for any material without an explicit level.
     *
     * @return array<int, string>
     */
    public function materialRiskLevels(): array
    {
        $ids = collect($this->input('materiaal_ids', []))
            ->map(fn ($id): int => (int) $id)
            ->all();

        $riskLevels = $this->input('risk_levels', []);

        $result = [];
        foreach ($ids as $id) {
            $result[$id] = $riskLevels[$id] ?? FloodRiskLevel::Medium->value;
        }

        return $result;
    }
}
