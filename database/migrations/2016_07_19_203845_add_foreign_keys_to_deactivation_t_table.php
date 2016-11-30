<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDeactivationTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('deactivation_t')) {

            Schema::table('deactivation_t', function (Blueprint $table){
                $table->foreign('instance_id', 'fk_deactivation_instance_id')
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
		Schema::table('deactivation_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_deactivation_instance_id');
		});
	}

}
