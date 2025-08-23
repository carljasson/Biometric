<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('admins', function (Blueprint $table) {
        $table->string('theme')->default('light');
        $table->integer('auto_logout')->default(15);
        $table->string('notifications')->default('email');
    });
}

};
