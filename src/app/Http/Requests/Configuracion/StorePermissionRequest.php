<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9-]+$/', Rule::unique('permissions', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.regex' => 'El permiso debe usar formato kebab-case (minúsculas, números y guiones, ej: aprobar-descuentos).',
            'name.unique' => 'Ya existe un permiso con ese nombre.',
        ];
    }
}
