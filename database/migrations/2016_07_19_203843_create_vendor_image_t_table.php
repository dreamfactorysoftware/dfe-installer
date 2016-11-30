<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVendorImageTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('vendor_image_t')) {

            Schema::create('vendor_image_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('vendor_id');
                $table->string('os_text', 64)->default('Linux');
                $table->string('license_text', 64)->nullable()->default('Public');
                $table->string('image_id_text', 64);
                $table->string('image_name_text', 256)->nullable();
                $table->text('image_description_text', 65535)->nullable();
                $table->integer('architecture_nbr')->default(0);
                $table->string('region_text', 64)->nullable();
                $table->string('availability_zone_text', 64)->nullable();
                $table->string('root_storage_text', 32)->nullable();
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->unique(['vendor_id', 'image_id_text'], 'ux_vendor_image_vendor_image');
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
		Schema::drop('vendor_image_t');
	}

}
