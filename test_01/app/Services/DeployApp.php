<?php

namespace App\Services;

use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Connection;

class DeployApp
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        
        #[DB('mysql')] Connection $connection,
        #[Config('github.token')]
        string $githubToken
    )
    {

        dd($connection);

        dd($githubToken);

    }
}
