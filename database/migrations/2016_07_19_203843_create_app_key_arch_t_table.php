<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppKeyArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('app_key_arch_t')) {

            Schema::create('app_key_arch_t', function(Blueprint $table)
            {
                $table->integer('id')->nullable();
                $table->string('key_class_text', 64)->nullable();
                $table->string('client_id', 128)->nullable();
                $table->string('client_secret', 128)->nullable();
                $table->string('server_secret', 128)->nullable();
                $table->integer('owner_id')->nullable();
                $table->integer('owner_type_nbr')->nullable();
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
		Schema::drop('app_key_arch_t');
	}

}
