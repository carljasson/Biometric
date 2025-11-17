<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('login_histories', function (Blueprint $table) {
        // Drop foreign key first
        $table->dropForeign(['user_id']); // <-- important

        // Drop the user_id column
        $table->dropColumn('user_id');

        // Add polymorphic columns
        $table->unsignedBigInteger('loggable_id')->after('id');
        $table->string('loggable_type')->after('loggable_id');
    });
}


public function down()
{
    Schema::table('login_histories', function (Blueprint $table) {
        $table->dropColumn(['loggable_id', 'loggable_type']);
        $table->unsignedBigInteger('user_id')->nullable();
    });
}

};
