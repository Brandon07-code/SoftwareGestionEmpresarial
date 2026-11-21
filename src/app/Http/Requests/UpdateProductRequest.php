<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class UpdateProductRequest extends StoreProductRequest
{
    public function rules(): array
    {
        return Arr::except(parent::rules(), ['stock']);
    }
}
