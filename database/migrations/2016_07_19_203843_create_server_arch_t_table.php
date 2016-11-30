<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('server_arch_t')) {

            Schema::create('server_arch_t', function (Blueprint $table){
                $table->integer('id')->default(0)->primary();
                $table->integer('server_type_id')->nullable();
                $table->string('server_id_text', 128)->nullable();
                $table->string('host_text', 1024)->nullable();
                $table->integer('mount_id')->nullable();
                $table->text('config_text', 16777215)->nullable();
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
		Schema::drop('server_arch_t');
	}

}
