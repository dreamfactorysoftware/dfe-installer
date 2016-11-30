<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOwnerHashTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('owner_hash_t')) {

            Schema::create('owner_hash_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('owner_id');
                $table->integer('owner_type_nbr');
                $table->string('hash_text', 128);
                $table->dateTime('create_date');
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
		Schema::drop('owner_hash_t');
	}

}
