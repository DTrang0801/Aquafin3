<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreNeerslagRequest extends FormRequest
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
            'jaar' => ['required', 'integer', 'min:2004', 'max:'.date('Y')],
            'maand' => ['required', 'integer', 'min:1', 'max:12'],
            'mm' => ['required', 'integer', 'min:0', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jaar.required' => 'Het jaar is verplicht.',
            'jaar.integer' => 'Het jaar moet een getal zijn.',
            'jaar.min' => 'Het jaar moet minimaal 2004 zijn.',
            'jaar.max' => 'Het jaar kan niet in de toekomst liggen.',
            'maand.required' => 'De maand is verplicht.',
            'maand.integer' => 'De maand moet een getal zijn.',
            'maand.min' => 'De maand moet minimaal 1 zijn.',
            'maand.max' => 'De maand kan maximaal 12 zijn.',
            'mm.required' => 'De neerslagmeting (mm) is verplicht.',
            'mm.integer' => 'De neerslagmeting moet een getal zijn.',
            'mm.min' => 'De neerslagmeting kan niet negatief zijn.',
            'mm.max' => 'De neerslagmeting lijkt onrealistisch hoog.',
        ];
    }
}
