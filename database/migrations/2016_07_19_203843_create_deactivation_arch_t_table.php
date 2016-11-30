<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeactivationArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('deactivation_arch_t')) {

            Schema::create('deactivation_arch_t', function (Blueprint $table){
                $table->integer('id')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('instance_id')->nullable();
                $table->dateTime('activate_by_date')->nullable();
                $table->integer('extend_count_nbr')->nullable()->default(0);
                $table->integer('user_notified_nbr')->nullable()->default(0);
                $table->integer('action_reason_nbr')->nullable()->default(0);
                $table->dateTime('create_date')->nullable();
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('deactivation_arch_t');
	}

}
