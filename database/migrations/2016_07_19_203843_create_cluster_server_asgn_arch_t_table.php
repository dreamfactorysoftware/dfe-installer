<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClusterServerAsgnArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('cluster_server_asgn_arch_t')) {

            Schema::create('cluster_server_asgn_arch_t', function (Blueprint $table){
                $table->integer('cluster_id')->nullable();
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
		Schema::drop('cluster_server_asgn_arch_t');
	}

}
