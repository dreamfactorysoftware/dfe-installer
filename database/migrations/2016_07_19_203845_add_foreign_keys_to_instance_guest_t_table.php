<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToInstanceGuestTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_guest_t')) {

            Schema::table('instance_guest_t', function (Blueprint $table){
                $table->foreign('instance_id', 'fk_instance_guest_instance_id')
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
		Schema::table('instance_guest_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_instance_guest_instance_id');
		});
	}

}
