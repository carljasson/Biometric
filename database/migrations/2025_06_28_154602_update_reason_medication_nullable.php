<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReasonMedicationNullable extends Migration
{
    public function up()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('reason_medication')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('reason_medication')->nullable(false)->change();
        });
    }
}
