<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequest extends FormRequest
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
            'quote' => 'required|numeric',
            'symbol' => 'required|exists:App\Models\Stock,symbol',
            'date' => [
                'required',
                'date',
                Rule::unique('quotes', 'stock_id')->ignore($this->id),
            ],
        ];
    }
}
