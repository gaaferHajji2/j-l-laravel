<?php

namespace App\Interfaces;

use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\CreatePassportRequest;
use App\Http\Requests\CustomerRequest;

interface IFirstInterface
{
    public function createNewCustomer(CustomerRequest $request);
    public function getAllCustomers();
    public function getCustomerById(int $id);
    public function getCustomerByEmailOrCode(string $name, string $code);

    public function createNewPassport(CreatePassportRequest $request);
    public function getPassportDataByCustomerIdentifier(string $customerIdentifier);
    public function getPassportByIdWithCustomer(int $id);

    public function getAuthorById(int $id);
    public function createNewAuthor(CreateAuthorRequest $request);
}
