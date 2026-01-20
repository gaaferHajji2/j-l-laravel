<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('passports', function (Blueprint $table) {
            $table->id('passport_uid'); // Custom primary key name
            $table->string('passport_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('country');
            $table->string('customer_identifier'); // Custom foreign key
            
            // Custom foreign key constraint
            $table->foreign('customer_identifier')
                  ->references('customer_code')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passports');
    }
};
