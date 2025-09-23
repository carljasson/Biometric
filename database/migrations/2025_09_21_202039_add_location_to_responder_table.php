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
        Schema::table('responders', function (Blueprint $table) {
            // Add a nullable string column for town/area
            $table->string('location')->nullable()->after('email');
            // → Example values: 'Santa Fe', 'Madridejos', 'Bantayan'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responders', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
