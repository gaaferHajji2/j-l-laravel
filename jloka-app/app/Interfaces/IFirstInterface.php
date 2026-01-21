<?php

namespace App\Interfaces;

use App\Http\Requests\CustomerRequest;

interface IFirstInterface
{
    public function createNewCustomer(CustomerRequest $request);
    
    public function createNewPassport();

    public function getAllCustomers();
    
    public function getCustomerById(int $id);

    public function getCustomerByEmailOrCode(string $name, string $code);
}
