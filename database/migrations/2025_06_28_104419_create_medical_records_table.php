<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    // Check if the medical_records table exists before creating it
    if (!Schema::hasTable('medical_records')) {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id(); // shorthand for bigIncrements
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('blood_type')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('surgeries')->nullable(); // Optional if you plan to add
            $table->text('complications')->nullable(); // Optional if needed
            $table->timestamps(); // includes created_at and updated_at
        });
    }
}


    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
