<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSnapshotTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('snapshot_t')) {

            Schema::table('snapshot_t', function (Blueprint $table){
                $table->foreign('user_id', 'fk_snapshot_user_id')
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
		Schema::table('snapshot_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_snapshot_user_id');
		});
	}

}
