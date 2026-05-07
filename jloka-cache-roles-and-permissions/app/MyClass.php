<?php

namespace App;

class MyClass
{
    public static $numberStatic = 0;
    public $number = 0;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        echo "Construct\n";
    }

     public function __destruct()
    {
        echo "Deconstruct\n";
    }

    public function add()
    {
        self::$numberStatic++;
        $this->number++;
    }
    public function get()
    {
        return self::$numberStatic . " - " . $this->number;
    }
}
