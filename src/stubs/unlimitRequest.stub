<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Unlimit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'take' => 'nullable|integer|max:100',
            'page' => 'nullable|integer',
            'include' => 'nullable|string|max:300',
            'sort' => 'nullable|string|max:120',
            'filter' => 'nullable|array|max:30',
            'filter.*' => 'nullable|string|max:255',
            'find' => 'nullable|array|max:30',
            'find.*' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
            'strict' => 'nullable|boolean',
            'trash' => 'nullable|string|in:current,all,trashed,|max:8',
            'select' => 'nullable|string|max:500',
            'except' => 'nullable|string|max:500',
        ];
    }
}
