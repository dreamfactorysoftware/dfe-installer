<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToInstanceTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_t')) {

            Schema::table('instance_t', function (Blueprint $table){
                $table->foreign('app_server_id', 'fk_instance_app_server_id')
                    ->references('id')
                    ->on('server_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
                $table->foreign('cluster_id', 'fk_instance_cluster_id')
                    ->references('id')
                    ->on('cluster_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
                $table->foreign('db_server_id', 'fk_instance_db_server_id')
                    ->references('id')
                    ->on('server_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
                $table->foreign('user_id', 'fk_instance_user_id')
                    ->references('id')
                    ->on('user_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
                $table->foreign('web_server_id', 'fk_instance_web_server')
                    ->references('id')
                    ->on('server_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
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
		Schema::table('instance_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_instance_app_server_id');
			$table->dropForeign('fk_instance_cluster_id');
			$table->dropForeign('fk_instance_db_server_id');
			$table->dropForeign('fk_instance_user_id');
			$table->dropForeign('fk_instance_web_server');
		});
	}

}
