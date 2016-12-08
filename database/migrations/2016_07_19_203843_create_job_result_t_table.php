<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobResultTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('job_result_t')) {

            Schema::create('job_result_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->string('result_id_text')->index('ix_job_result_result_id');
                $table->text('result_text', 16777215);
                $table->dateTime('create_date')->nullable();
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('job_result_t');
	}

}
