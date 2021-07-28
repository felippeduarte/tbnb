<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\QuoteRequest;
use App\Models\Stock;
use App\Models\Quote;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Upsert quotes for a day
     *
     * @param  \App\Http\Requests\QuoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuoteRequest $request)
    {
        $data = $request->all();

        $symbols = array_column($data['stocks'], 'symbol');
        $stocks = Stock::whereIn('symbol',$symbols)->get()->pluck('id','symbol');

        $quotes = collect($data['stocks'])->map(function($v, $k) use ($stocks, $data) {
            return [
                'stock_id' => $stocks[$v['symbol']],
                'quote' => $v['quote'],
                'date' => $data['date'],
            ];
        })->toArray();

        try {
            $upsert = Quote::upsert(
                $quotes,
                ['stock_id','date'],
                ['quote']
            );
            return response($data, 200);
        } catch(\Exception $e) {
            abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
}
