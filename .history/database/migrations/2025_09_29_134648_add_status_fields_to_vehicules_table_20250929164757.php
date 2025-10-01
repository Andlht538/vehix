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
        Schema::table('vehicules', function (Blueprint $table) {
            // Modify status column to include new statuses
             $table->enum('status', ['en_attente', 'valide', 'refuse', 'a_corriger', 'doublon'])
                ->default('en_attente')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicules', function (Blueprint $table) {
            // Revert to original status values
            $table->enum('status', ['en_attente', 'valide', 'refuse'])
                ->default('en_attente')
                ->change();
        });
    }
};
