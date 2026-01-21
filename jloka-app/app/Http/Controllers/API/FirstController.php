<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\IFirstInterface;
use Illuminate\Http\Request;

class FirstController extends Controller
{

    public $service;

    public function __construct(IFirstInterface $interface)
    {
        $this->service = $interface;
    }

    public function getAllCustomers() {
        $customers = $this->service->getAllCustomers();
        return response()->json($customers);
    }

}
