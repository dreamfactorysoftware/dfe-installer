<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerTypeTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('server_type_t')) {

            Schema::create('server_type_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->string('type_name_text', 64)->unique('ux_server_type_type_name');
                $table->binary('schema_text', 16777215);
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
		Schema::drop('server_type_t');
	}

}
