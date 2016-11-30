<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClusterServerAsgnTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('cluster_server_asgn_t')) {

            Schema::create('cluster_server_asgn_t', function (Blueprint $table){
                $table->integer('cluster_id')->index('ix_csa_cluster_id');
                $table->integer('server_id')->index('ix_csa_server_id');
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->primary(['cluster_id', 'server_id']);
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
		Schema::drop('cluster_server_asgn_t');
	}

}
