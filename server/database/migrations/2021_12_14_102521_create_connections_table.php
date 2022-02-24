<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('description')->nullable();
            $table->string('host')->nullable(false);
            $table->string('base_dn')->nullable(false);
            $table->string('bind_dn')->nullable(false)->default('');
            $table->string('bind_pw')->nullable(false)->default('');
            $table->integer('port')->nullable(false)->default(389);
            $table->tinyInteger('tls')->nullable(false)->default('0');
            $table->integer('version')->nullable(false)->default(LDAP_DEFAULT_VERSION);
            $table->string('encoding')->nullable(false)->default(LDAP_DEFAULT_ENCODING);
            $table->integer('pagesize')->nullable(false)->default(LDAP_DEFAULT_PAGESIZE);
            $table->string('pagedresultscontrol')->nullable(false)->default(LDAP_DEFAULT_PAGED_RESULTS_CONTROL);
            $table->tinyInteger('rootdse')->nullable(false)->default(LDAP_DEFAULT_ROOTDSE);
            $table->tinyInteger('private')->nullable(false)->default('0');
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
        Schema::dropIfExists('connections');
    }
}
