<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobFailTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('job_fail_t')) {

            Schema::create('job_fail_t', function (Blueprint $table){
                $table->bigInteger('id')->unsigned()->nullable();
                $table->text('connection', 65535)->nullable();
                $table->text('queue', 65535)->nullable();
                $table->text('payload', 65535)->nullable();
                $table->timestamp('failed_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('job_fail_t');
	}

}
