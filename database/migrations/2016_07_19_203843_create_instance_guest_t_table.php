<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstanceGuestTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('instance_guest_t')) {

            Schema::create('instance_guest_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('instance_id')->index('fk_instance_guest_instance_id');
                $table->integer('vendor_id');
                $table->integer('vendor_image_id')->default(0);
                $table->integer('vendor_credentials_id')->nullable();
                $table->integer('flavor_nbr')->default(0);
                $table->string('base_image_text', 32)->default('t1.micro');
                $table->string('region_text', 32)->nullable();
                $table->string('availability_zone_text', 32)->nullable();
                $table->string('security_group_text', 1024)->nullable();
                $table->string('ssh_key_text', 64)->nullable();
                $table->integer('root_device_type_nbr')->default(0);
                $table->string('public_host_text', 256)->nullable();
                $table->string('public_ip_text', 20)->nullable();
                $table->string('private_host_text', 256)->nullable();
                $table->string('private_ip_text', 20)->nullable();
                $table->integer('state_nbr')->default(0);
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
		Schema::drop('instance_guest_t');
	}

}
