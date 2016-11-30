<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToServiceUserTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('service_user_t')) {

            Schema::table('service_user_t', function (Blueprint $table){
                $table->foreign('owner_id', 'fk_service_user_owner_id')
                    ->references('id')
                    ->on('service_user_t')
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
		Schema::table('service_user_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_service_user_owner_id');
		});
	}

}
