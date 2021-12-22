<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledTasksRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_tasks_runs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('started_at')->nullable(false)->default(0);
            $table->bigInteger('finished_at')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_tasks_runs');
    }
}
