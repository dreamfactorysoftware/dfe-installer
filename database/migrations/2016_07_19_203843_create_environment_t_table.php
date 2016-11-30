<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnvironmentTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('environment_t')) {

            Schema::create('environment_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('user_id')->nullable();
                $table->string('environment_id_text', 64)->unique('ux_environment_environment_id');
                $table->dateTime('create_date');
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
		Schema::drop('environment_t');
	}

}
