<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class GetUser {

    public function __construct (
        #[CurrentUser] User $user
    ) {
        dd($user->toArray());
    }

}

?>