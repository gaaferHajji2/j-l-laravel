<?php
namespace App\Services;

use App\Http\Requests\CustomerRequest;
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

    public function createNewCustomer(CustomerRequest $request) {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->customer_code = $request->customer_code;
        $customer->save();
        return $customer;
    }
    
    public function createNewPassport() {}

    public function getAllCustomers() {
        return Customer::all(['name', 'email', 'id']);
    }
    
    public function getCustomerById(int $id) {
        return Customer::where(['id' => $id])->first();
    }

    public function getCustomerByEmailOrCode(string $name, string $code) {
        return Customer::where(['name' => $name])->orWhere(['customer_code' => $code])->first();
    }
}