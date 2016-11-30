<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstanceGuestArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_guest_arch_t')) {

            Schema::create('instance_guest_arch_t', function (Blueprint $table){
                $table->integer('id')->nullable();
                $table->integer('instance_id')->nullable();
                $table->integer('vendor_id')->nullable();
                $table->integer('vendor_image_id')->nullable();
                $table->integer('vendor_credentials_id')->nullable();
                $table->integer('flavor_nbr')->nullable();
                $table->string('base_image_text', 32)->nullable();
                $table->string('region_text', 32)->nullable();
                $table->string('availability_zone_text', 32)->nullable();
                $table->string('security_group_text', 1024)->nullable();
                $table->string('ssh_key_text', 64)->nullable();
                $table->integer('root_device_type_nbr')->nullable();
                $table->string('public_host_text', 256)->nullable();
                $table->string('public_ip_text', 20)->nullable();
                $table->string('private_host_text', 256)->nullable();
                $table->string('private_ip_text', 20)->nullable();
                $table->integer('state_nbr')->nullable();
                $table->string('state_text', 64)->nullable();
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
		Schema::drop('instance_guest_arch_t');
	}

}
