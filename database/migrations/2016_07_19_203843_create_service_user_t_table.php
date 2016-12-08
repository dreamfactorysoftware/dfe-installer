<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceUserTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('service_user_t')) {

            Schema::create('service_user_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->string('first_name_text', 64);
                $table->string('last_name_text', 64);
                $table->string('nickname_text', 128)->nullable();
                $table->string('email_addr_text', 192)->unique('ux_service_user_email_addr');
                $table->string('password_text', 192);
                $table->integer('owner_id')->nullable()->index('ix_service_user_owner_id');
                $table->integer('owner_type_nbr')->nullable();
                $table->timestamp('last_login_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->string('last_login_ip_text', 64)->nullable();
                $table->string('remember_token', 128)->nullable();
                $table->boolean('active_ind')->default(0);
                $table->timestamp('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('service_user_t');
	}

}
