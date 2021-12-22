<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPasswordResetCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_password_reset_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false);
            $table->bigInteger('user')->index()->unsigned()->nullable(false);
            $table->foreign('user')->references('id')->on('users')->cascadeOnDelete()->restrictOnUpdate();
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
        Schema::dropIfExists('user_password_reset_codes');
    }
}
