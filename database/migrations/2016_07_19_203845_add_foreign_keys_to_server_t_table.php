<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToServerTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('server_t')) {

            Schema::table('server_t', function (Blueprint $table){
                $table->foreign('mount_id', 'fk_server_mount_id')
                    ->references('id')
                    ->on('mount_t')
                    ->onUpdate('CASCADE')
                    ->onDelete('RESTRICT');
                $table->foreign('server_type_id', 'fk_server_server_type_id')
                    ->references('id')
                    ->on('server_type_t')
                    ->onUpdate('CASCADE')
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
		Schema::table('server_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_server_mount_id');
			$table->dropForeign('fk_server_server_type_id');
		});
	}

}
