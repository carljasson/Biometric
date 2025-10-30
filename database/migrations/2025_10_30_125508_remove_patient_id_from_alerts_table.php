<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('alerts', function (Blueprint $table) {
            if (Schema::hasColumn('alerts', 'patient_id')) {
                $table->dropColumn('patient_id');
            }
        });
    }

    public function down()
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();
        });
    }
};
