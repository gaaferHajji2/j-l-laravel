<?php

namespace App\Interfaces;

interface IFirstInterface
{
    public function createNewCustomer();
    
    public function createNewPassport();

    public function getAllCustomers();
    
    public function getCustomerById(int $id);
}
