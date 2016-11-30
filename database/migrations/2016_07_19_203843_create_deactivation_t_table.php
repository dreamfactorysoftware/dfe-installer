<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeactivationTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('deactivation_t')) {

            Schema::create('deactivation_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('user_id');
                $table->integer('instance_id')->index('ix_deactivation_instance_id');
                $table->dateTime('activate_by_date');
                $table->integer('extend_count_nbr')->default(0);
                $table->integer('user_notified_nbr')->default(0);
                $table->integer('action_reason_nbr')->default(0);
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->unique(['user_id', 'instance_id'], 'ux_deactivation_user_instance');
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
		Schema::drop('deactivation_t');
	}

}
