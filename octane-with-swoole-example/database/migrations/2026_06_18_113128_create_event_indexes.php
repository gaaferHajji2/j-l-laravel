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
        Schema::table('events', function (Blueprint $table) {
            $table->index('type', 'event_type_index');
            $table->index(
                'description',
                'event_description_index'
            );
            $table->index('date', 'event_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('event_type_index');
            $table->dropIndex('event_description_index');
            $table->dropIndex('event_date_index');
        });
    }
};
