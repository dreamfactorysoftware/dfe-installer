<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('config_t')) {

            Schema::create('config_t', function (Blueprint $table){
                $table->increments('id');
                $table->string('name_text', 64)->unique('ux_config_name_text');
                $table->text('value_text', 65535)->nullable();
                $table->timestamp('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('config_t');
	}

}
