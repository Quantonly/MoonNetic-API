<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('server_ip');
            $table->string('sftp_username');
            $table->string('sftp_password');
            $table->string('sftp_host');
            $table->integer('sftp_port');
            $table->string('php_host');
            $table->string('php_database');
            $table->string('php_username');
            $table->string('php_password');
            $table->string('php_version');
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
        Schema::dropIfExists('servers');
    }
}
