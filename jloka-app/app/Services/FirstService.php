<?php

namespace App\Services;

use App\Interfaces\IFirstInterface;

class FirstService implements IFirstInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createNewCustomer() {}
    public function createNewPassport() {}
    public function getAllCustomers() {}
    public function getCustomerById(int $id) {}
}
