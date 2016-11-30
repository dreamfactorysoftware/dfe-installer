<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLimitTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('limit_t')) {

            Schema::table('limit_t', function (Blueprint $table){
                $table->foreign('cluster_id', 'fk_limit_cluster_id')
                    ->references('id')
                    ->on('cluster_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->foreign('instance_id', 'fk_limit_instance_id')
                    ->references('id')
                    ->on('instance_t')
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
		Schema::table('limit_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_limit_cluster_id');
			$table->dropForeign('fk_limit_instance_id');
		});
	}

}
