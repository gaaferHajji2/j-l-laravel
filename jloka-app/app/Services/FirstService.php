<?php

namespace App\Services;

use App\Interfaces\IFirstInterface;
use App\Models\Customer;

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
    public function getAllCustomers() {
        return Customer::all(['name', 'email', 'id']);
    }
    public function getCustomerById(int $id) {}
}
