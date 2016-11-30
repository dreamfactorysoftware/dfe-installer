<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthResetTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('auth_reset_t')) {

            Schema::create('auth_reset_t', function (Blueprint $table){
                $table->string('email')->index('ix_auth_reset_email');
                $table->string('token')->index('ix_auth_reset_token');
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('auth_reset_t');
	}

}
