<?php
namespace App\Services;

use App\Http\Requests\CreatePassportRequest;
use App\Http\Requests\CustomerRequest;
use App\Interfaces\IFirstInterface;
use App\Models\Customer;
use App\Models\Passport;

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
    
    public function createNewPassport(CreatePassportRequest $request) {
        $passport = new Passport();
        $passport->passport_number = $request->passport_number;
        $passport->issue_date = $request->issue_date;
        $passport->expiry_date = $request->expiry_date;
        $passport->country = $request->country;
        $passport->customer_identifier = $request->customer_identifier;
        $passport->save();
        return $passport;
    }

    public function getAllCustomers() {
        return Customer::all(['name', 'email', 'id']);
    }
    
    public function getCustomerById(int $id) {
        return Customer::where(['id' => $id])->first();
    }

    public function getCustomerByEmailOrCode(string $name, string $code) {
        return Customer::where(['name' => $name])->orWhere(['customer_code' => $code])->first();
    }

    public function getPassportDataByCustomerIdentifier(string $customerIdentifier) {
        return Passport::select('passport_uid', 'customer_identifier')
            ->where(['customer_identifier' => $customerIdentifier])
            ->first();
    }
}