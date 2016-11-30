<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstanceServerAsgnArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_server_asgn_arch_t')) {

            Schema::create('instance_server_asgn_arch_t', function (Blueprint $table){
                $table->integer('instance_id')->nullable();
                $table->integer('server_id')->nullable();
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
		Schema::drop('instance_server_asgn_arch_t');
	}

}
