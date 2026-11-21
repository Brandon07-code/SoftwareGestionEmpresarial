<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_-]+$/', Rule::unique('roles', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.regex' => 'El rol solo puede contener minúsculas, números, guiones o guión bajo.',
            'name.unique' => 'Ya existe un rol con ese nombre.',
        ];
    }
}
