<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\VehiculeStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('make');
            $table->string('model');
            $table->enum('vehicle_type', ['voiture', 'moto', 'utilitaire', 'camion', 'bus', 'scooter', 'camionnette']);
            $table->year('year');
            $table->string('license_plate')->unique();
            $table->string('vin')->unique();
            $table->string('color')->nullable();
            $table->enum('fuel_type', ['essence', 'diesel', 'hybride', 'electrique', 'gpl'])->nullable();
            $table->unsignedBigInteger('mileage')->default(0);
            $table->enum('status', array_column(VehiculeStatus::cases(), 'value'))
                  ->default(VehiculeStatus::PENDING->value);
            $table->text('validation_notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
