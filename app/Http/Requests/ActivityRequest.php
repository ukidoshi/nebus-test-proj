<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'parent_id' => 'nullable|integer'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
