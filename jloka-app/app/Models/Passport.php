<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passport extends Model
{
    protected $primaryKey = 'passport_uid';
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_identifier', 'customer_code');
    }

}
