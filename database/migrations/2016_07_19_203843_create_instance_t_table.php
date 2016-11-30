<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstanceTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_t')) {

            Schema::create('instance_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('user_id')->index('ix_instance_user_id');
                $table->integer('guest_location_nbr')->default(0);
                $table->integer('environment_id')->default(1)->index('ix_instance_environment_id');
                $table->string('instance_id_text', 64)->nullable();
                $table->string('instance_name_text', 128)->nullable()->unique('ux_instance_instance_name');
                $table->text('instance_data_text', 16777215)->nullable();
                $table->integer('cluster_id')->default(1)->index('ix_instance_cluster_id');
                $table->integer('app_server_id')->default(6)->index('ix_instance_app_server_id');
                $table->integer('db_server_id')->default(4)->index('ix_instance_db_server_id');
                $table->integer('web_server_id')->default(5)->index('ix_instance_web_server_id');
                $table->string('storage_id_text', 64)->nullable();
                $table->string('db_host_text', 1024)->default('localhost');
                $table->integer('db_port_nbr')->default(3306);
                $table->string('db_name_text', 64)->default('dreamfactory');
                $table->string('db_user_text', 64)->default('df_user');
                $table->string('db_password_text', 64)->default('df_user');
                $table->string('request_id_text', 128)->nullable();
                $table->dateTime('request_date')->nullable();
                $table->boolean('activate_ind')->default(0);
                $table->boolean('trial_instance_ind')->default(1);
                $table->boolean('provision_ind')->default(0);
                $table->boolean('deprovision_ind')->default(0);
                $table->integer('state_nbr')->default(0);
                $table->integer('ready_state_nbr')->default(0);
                $table->integer('platform_state_nbr')->default(0);
                $table->integer('storage_version_nbr')->default(0);
                $table->timestamp('last_state_date')
                    ->default(DB::raw('CURRENT_TIMESTAMP'))
                    ->index('ix_instance_state_date');
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->dateTime('terminate_date')->nullable();
                $table->dateTime('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('instance_t');
	}

}
