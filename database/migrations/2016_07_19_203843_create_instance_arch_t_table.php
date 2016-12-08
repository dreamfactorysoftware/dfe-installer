<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstanceArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_arch_t')) {

            Schema::create('instance_arch_t', function (Blueprint $table){
                $table->integer('id')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('guest_location_nbr')->nullable();
                $table->integer('environment_id')->nullable();
                $table->string('instance_id_text', 64)->nullable();
                $table->string('instance_name_text', 128)->nullable();
                $table->text('instance_data_text', 16777215)->nullable();
                $table->integer('cluster_id')->nullable();
                $table->integer('app_server_id')->nullable();
                $table->integer('db_server_id')->nullable();
                $table->integer('web_server_id')->nullable();
                $table->string('storage_id_text', 64)->nullable();
                $table->string('db_host_text', 1024)->nullable();
                $table->integer('db_port_nbr')->nullable();
                $table->string('db_name_text', 64)->nullable();
                $table->string('db_user_text', 64)->nullable();
                $table->string('db_password_text', 64)->nullable();
                $table->string('request_id_text', 128)->nullable();
                $table->timestamp('request_date')->nullable();
                $table->boolean('activate_ind')->nullable()->default(0);
                $table->boolean('trial_instance_ind')->nullable()->default(1);
                $table->boolean('provision_ind')->nullable()->default(0);
                $table->boolean('deprovision_ind')->nullable()->default(0);
                $table->integer('state_nbr')->nullable()->default(0);
                $table->integer('ready_state_nbr')->nullable()->default(0);
                $table->integer('platform_state_nbr')->nullable()->default(0);
                $table->integer('storage_version_nbr')->nullable()->default(0);
                $table->timestamp('last_state_date')->nullable();
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->timestamp('terminate_date')->nullable();
                $table->timestamp('create_date')->nullable();
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
		Schema::drop('instance_arch_t');
	}

}
