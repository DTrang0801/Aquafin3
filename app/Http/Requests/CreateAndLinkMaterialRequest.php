<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class CreateAndLinkMaterialRequest extends FormRequest
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
        return [
            'naam' => ['required', 'string', 'max:255'],
            'beschrijving' => ['nullable', 'string', 'max:1000'],
            'materiaal_subcategorie_id' => ['required', 'integer', 'exists:materiaal_subcategorieen,id'],
            'link_as_critical' => ['boolean'],
        ];
    }
}
