<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimulatedPriceService;

class SimulatedPriceController extends Controller
{
    public function __construct(SimulatedPriceService $simulatedPriceService)
    {
        $this->simulatedPriceService = $simulatedPriceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->simulatedPriceService->getSimulatedPrice();
    }
}
