<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledTasksLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_tasks_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('task')->index()->unsigned()->nullable(false);
            $table->foreign('task')->references('id')->on('scheduled_tasks')->cascadeOnDelete()->restrictOnUpdate();
            $table->bigInteger('run')->index()->unsigned()->nullable(false);
            $table->foreign('run')->references('id')->on('scheduled_tasks_runs')->cascadeOnDelete()->restrictOnUpdate();
            $table->string('path')->nullable(false)->unique();
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
        Schema::dropIfExists('scheduled_tasks_logs');
    }
}
