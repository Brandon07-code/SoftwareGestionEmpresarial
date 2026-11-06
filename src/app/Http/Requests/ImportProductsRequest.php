<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Solo Excel o CSV y máximo 2 MB
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ];
    }

    // Mensajes en español
    public function messages(): array
    {
        return [
            'file.required' => 'Seleccione el archivo que desea importar.',
            'file.file' => 'El archivo no se pudo subir. Inténtelo de nuevo.',
            'file.mimes' => 'El archivo debe ser .xlsx, .xls o .csv.',
            'file.max' => 'El archivo no puede pesar más de 2 MB.',
        ];
    }
}
