<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\CreatePassportRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\GetAllCustomerResource;
use App\Http\Resources\GetAllPassportDataResource;
use App\Http\Resources\GetDetailCustomerResource;
use App\Interfaces\IFirstInterface;

class FirstController extends Controller
{

    public $service;

    public function __construct(IFirstInterface $interface)
    {
        $this->service = $interface;
    }

    public function getAllCustomers() {
        $customers = $this->service->getAllCustomers();
        return GetAllCustomerResource::collection($customers);
    }

    public function createNewCustomer(CustomerRequest $request) {
        $t1 = $this->service->getCustomerByEmailOrCode($request->email, $request->customer_code);

        if($t1 !== null) {
            return response()->json(['msg' => 'duplicate email or code'], 401);
        }

        $customer = $this->service->createNewCustomer($request);
        return (new GetDetailCustomerResource($customer))->response()->setStatusCode(201);
    }

    public function getCustomerById(int $id) {
        $customer = $this->service->getCustomerById($id);

        if($customer == null) {
            return response()->json(['msg'=>'Not Found'], 404);
        }

        return new GetDetailCustomerResource($customer);
    }

    public function createNewPassportData(CreatePassportRequest $request) {

        $t1 = $this->service->getPassportDataByCustomerIdentifier($request->customer_identifier);
        if($t1 != null) {
            return response()->json([
                "msg" => "You have passport"
            ], 403);
        }

        return (new GetAllPassportDataResource($this->service->createNewPassport($request)))->response()->setStatusCode(201);
    }

    public function getPassportWithCustomerById(int $id) {
        return response()->json($this->service->getPassportByIdWithCustomer($id));
    }

    public function createNewAuthor(CreateAuthorRequest $request) {
        return response()->json($this->service->createNewAuthor($request), 201);
    }

    public function getAuthorById(string $id) {
        return response()->json($this->service->getAuthorById($id), 200);
    }
}