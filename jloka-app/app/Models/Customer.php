<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    public function passport()
    {
        return $this->hasOne(Passport::class, 'customer_identifier', 'customer_code');
    }
}