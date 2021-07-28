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
            'stocks.*.quote' => 'required|numeric',
            'stocks.*.symbol' => [
                'required',
                Rule::in(\App\Models\Stock::select('symbol')->get()->pluck('symbol')),
            ],
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                Rule::unique('quotes', 'stock_id')->ignore($this->id),
            ],
        ];
    }
}
