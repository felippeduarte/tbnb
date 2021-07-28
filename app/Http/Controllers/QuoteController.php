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
     * Upsert a quote
     *
     * @param  \App\Http\Requests\QuoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuoteRequest $request)
    {
        $stock_id = Stock::whereSymbol($request->input('symbol'))->first()->id;
        return response(Quote::updateOrCreate(
                ['date' => $request->input('date'), 'stock_id' => $stock_id],            
                $request->all()+ ['stock_id' => $stock_id]
            ),200);
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
