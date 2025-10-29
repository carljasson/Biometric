<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method')->nullable();   // fingerprint, face, password, etc.
            $table->string('device')->nullable();   // user-agent or device name
            $table->string('ip', 45)->nullable();
            $table->string('location')->nullable(); // optional
            $table->string('session_id')->nullable();
            $table->timestamp('logged_in_at')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
            $table->index('logged_in_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_histories');
    }
}
