<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMetricsTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('metrics_t')) {

            Schema::create('metrics_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->text('metrics_data_text', 16777215);
                $table->boolean('sent_ind')->default(0);
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
		Schema::drop('metrics_t');
	}

}
