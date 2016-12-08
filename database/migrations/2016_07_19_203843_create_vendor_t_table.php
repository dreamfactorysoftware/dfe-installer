<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVendorTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('vendor_t')) {

            Schema::create('vendor_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->string('vendor_name_text', 64)->unique('ux_vendor_vendor_name');
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
		Schema::drop('vendor_t');
	}

}
