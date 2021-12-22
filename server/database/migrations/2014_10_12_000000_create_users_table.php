<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('firstname')->nullable(false);
            $table->string('lastname')->nullable(false);
            $table->string('auth')->nullable(false)->default('manual');
            $table->string('calendartype')->nullable();
            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->bigInteger('firstaccess')->nullable();
            $table->bigInteger('lastaccess')->nullable();
            $table->bigInteger('lastlogin')->nullable();
            $table->ipAddress('lastip')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
