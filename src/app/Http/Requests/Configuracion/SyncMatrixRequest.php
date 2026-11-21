<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncMatrixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matrix' => ['nullable', 'array'],
            'matrix.*' => ['array'],
            'matrix.*.*' => ['string', Rule::exists('permissions', 'name')],
        ];
    }
}
