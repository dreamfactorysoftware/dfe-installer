<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('user_t')) {

            Schema::create('user_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->string('email_addr_text', 192)->unique('ux_user_email_addr');
                $table->string('password_text', 192);
                $table->string('remember_token', 128)->nullable();
                $table->string('first_name_text', 64)->nullable();
                $table->string('last_name_text', 64)->nullable();
                $table->string('nickname_text', 128)->nullable();
                $table->string('api_token_text', 128)->nullable();
                $table->string('storage_id_text', 64);
                $table->string('external_id_text', 128)->nullable();
                $table->string('external_password_text', 192)->nullable();
                $table->integer('owner_id')->nullable();
                $table->integer('owner_type_nbr')->nullable();
                $table->string('company_name_text', 128)->nullable();
                $table->string('title_text', 128)->nullable();
                $table->string('city_text', 64)->nullable();
                $table->string('state_province_text', 64)->nullable();
                $table->string('country_text', 2)->nullable();
                $table->string('postal_code_text', 32)->nullable();
                $table->string('phone_text', 32)->nullable();
                $table->boolean('opt_in_ind')->default(1);
                $table->boolean('agree_ind')->default(0);
                $table->dateTime('last_login_date')->nullable();
                $table->string('last_login_ip_text', 64)->nullable();
                $table->boolean('admin_ind')->default(0);
                $table->boolean('activate_ind')->default(0);
                $table->boolean('active_ind')->default(1);
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
		Schema::drop('user_t');
	}

}
