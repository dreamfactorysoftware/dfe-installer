<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('job_t')) {

            Schema::create('job_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->string('queue', 256);
                $table->text('payload', 65535);
                $table->boolean('attempts');
                $table->boolean('reserved');
                $table->integer('reserved_at')->unsigned()->nullable();
                $table->integer('available_at')->unsigned();
                $table->integer('created_at')->unsigned();
            });
        }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('job_t');
	}

}
