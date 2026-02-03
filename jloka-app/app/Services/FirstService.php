<?php
namespace App\Services;

use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\CreatePassportRequest;
use App\Http\Requests\CreateStudentRequest;
use App\Http\Requests\CustomerRequest;
use App\Interfaces\IFirstInterface;
use App\Models\Author;
use App\Models\Book;
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

    public function getPassportByIdWithCustomer(int $id) {
        return Passport::where(['passport_uid' => $id])->with(['customer' => function($query) {
            $query->select('customer_code', 'name', 'email');
        }])->first();
    }

    public function getAuthorById(string $id){
        return Author::with('books')->where([ 'author_slug' => $id ])->first();
    }
    
    public function createNewAuthor(CreateAuthorRequest $request){
        $author = new Author();
        $author->author_slug = $request->author_slug;
        $author->name = $request->name;
        $author->email = $request->email;
        $author->bio = $request->bio;
        $author->save();
        return $author;
    }
    public function getBookById(int $id) {
        return Book::with('author')->find($id);
    }
    public function createNewBook(CreateBookRequest $request){
        $book = new Book();
        $book->title = $request->title;
        $book->isbn = $request->isbn;
        $book->published_date = $request->published_date;
        $book->writer_id = $request->writer_id;
        $book->save();
        return $book;
    }

    public function getStudentById(string $id) {

    }
    public function createNewStudent(CreateStudentRequest $request) {
        
    }

}