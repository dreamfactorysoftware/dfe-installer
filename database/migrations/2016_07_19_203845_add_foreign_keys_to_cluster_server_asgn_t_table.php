<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClusterServerAsgnTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('cluster_server_asgn_t')) {

            Schema::table('cluster_server_asgn_t', function (Blueprint $table){
                $table->foreign('cluster_id', 'fk_csa_cluster_id')
                    ->references('id')
                    ->on('cluster_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->foreign('server_id', 'fk_csa_server_id')
                    ->references('id')
                    ->on('server_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
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
		Schema::table('cluster_server_asgn_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_csa_cluster_id');
			$table->dropForeign('fk_csa_server_id');
		});
	}

}
