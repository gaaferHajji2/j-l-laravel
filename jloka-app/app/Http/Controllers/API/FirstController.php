<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
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

    public function createNewCustomer(CustomerRequest $request) {
        $t1 = $this->service->getCustomerByEmailOrCode($request->email, $request->customer_code);

        if($t1 !== null) {
            return response()->json(['msg' => 'duplicate email or code'], 401);
        }

        $customer = $this->service->createNewCustomer($request);
        return response()->json($customer, 201);
    }
}
