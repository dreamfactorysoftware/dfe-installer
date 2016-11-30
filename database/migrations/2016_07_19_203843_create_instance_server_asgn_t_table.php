<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstanceServerAsgnTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_server_asgn_t')) {

            Schema::create('instance_server_asgn_t', function (Blueprint $table){
                $table->integer('instance_id')->index('ix_isa_instance_id');
                $table->integer('server_id')->index('ix_isa_server_id');
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->primary(['instance_id', 'server_id']);
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
		Schema::drop('instance_server_asgn_t');
	}

}
