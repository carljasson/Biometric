<?php

// database/migrations/xxxx_xx_xx_xxxxxx_update_medical_records_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMedicalRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('dose')->nullable()->change();  // Make 'dose' field nullable
            $table->string('reason_medication')->nullable()->change();  // Make 'reason_medication' field nullable
        });
    }

    public function down()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('dose')->nullable(false)->change();  // Revert 'dose' to non-nullable
            $table->string('reason_medication')->nullable(false)->change();  // Revert 'reason_medication' to non-nullable
        });
    }
}
