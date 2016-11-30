<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToInstanceServerAsgnTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_server_asgn_t')) {

            Schema::table('instance_server_asgn_t', function (Blueprint $table){
                $table->foreign('instance_id', 'fk_isa_instance_id')
                    ->references('id')
                    ->on('instance_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->foreign('server_id', 'fk_isa_server_id')
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
		Schema::table('instance_server_asgn_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_isa_instance_id');
			$table->dropForeign('fk_isa_server_id');
		});
	}

}
