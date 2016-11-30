<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserRoleAsgnTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('user_role_asgn_t')) {

            Schema::table('user_role_asgn_t', function (Blueprint $table){
                $table->foreign('role_id', 'fk_role_role_id')
                    ->references('id')
                    ->on('role_t')
                    ->onUpdate('CASCADE')
                    ->onDelete('CASCADE');
                $table->foreign('user_id', 'fk_role_user_id')
                    ->references('id')
                    ->on('user_t')
                    ->onUpdate('CASCADE')
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
		Schema::table('user_role_asgn_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_role_role_id');
			$table->dropForeign('fk_role_user_id');
		});
	}

}
