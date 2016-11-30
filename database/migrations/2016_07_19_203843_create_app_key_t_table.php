<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppKeyTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('app_key_t')) {

            Schema::create('app_key_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('owner_id');
                $table->integer('owner_type_nbr');
                $table->string('client_id', 128)->unique('ux_app_key_client_id');
                $table->string('client_secret', 128);
                $table->string('server_secret', 128);
                $table->string('key_class_text', 64);
                $table->timestamps();
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
		Schema::drop('app_key_t');
	}

}
