<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKardexAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'movement_type' => ['required', Rule::in(['AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'min:10', 'max:255'],
            'document_reference' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'producto',
            'movement_type' => 'tipo de ajuste',
            'quantity' => 'cantidad',
            'reason' => 'motivo del ajuste',
            'document_reference' => 'documento soporte',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Debe seleccionar un producto.',
            'product_id.exists' => 'El producto seleccionado no existe o fue eliminado.',
            'movement_type.in' => 'El tipo de ajuste debe ser AJUSTE_POSITIVO o AJUSTE_NEGATIVO.',
            'quantity.min' => 'La cantidad a ajustar debe ser mínimo 1 unidad.',
            'reason.required' => 'El motivo del ajuste es obligatorio.',
            'reason.min' => 'El motivo del ajuste debe contener al menos 10 caracteres.',
        ];
    }
}
