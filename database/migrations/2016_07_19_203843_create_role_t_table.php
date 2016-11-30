<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoleTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('role_t')) {

            Schema::create('role_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->string('role_name_text', 64)->unique('ux_role_role_name');
                $table->string('description_text', 1024)->nullable();
                $table->boolean('active_ind');
                $table->string('home_view_text', 256);
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
		Schema::drop('role_t');
	}

}
