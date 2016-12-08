<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRouteHashTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('route_hash_t')) {

            Schema::create('route_hash_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('type_nbr')->default(0);
                $table->string('hash_text', 128)->unique('ix_route_hash_hash');
                $table->string('actual_path_text', 1024);
                $table->dateTime('expire_date')->nullable();
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
		Schema::drop('route_hash_t');
	}

}
