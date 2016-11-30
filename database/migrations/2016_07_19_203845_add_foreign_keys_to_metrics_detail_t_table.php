<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMetricsDetailTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('metrics_detail_t')) {

            Schema::table('metrics_detail_t', function (Blueprint $table){
                $table->foreign('instance_id', 'fk_metrics_detail_instance_id')
                    ->references('id')
                    ->on('instance_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->foreign('user_id', 'fk_metrics_detail_user_id')
                    ->references('id')
                    ->on('user_t')
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
		Schema::table('metrics_detail_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_metrics_detail_instance_id');
			$table->dropForeign('fk_metrics_detail_user_id');
		});
	}

}
