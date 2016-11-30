<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMountTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('mount_t')) {

            Schema::create('mount_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('mount_type_nbr')->default(0);
                $table->string('mount_id_text', 64)->unique('ux_mount_mount_id');
                $table->integer('owner_id')->nullable();
                $table->integer('owner_type_nbr')->nullable();
                $table->string('root_path_text', 128)->nullable();
                $table->text('config_text', 16777215)->nullable();
                $table->dateTime('last_mount_date')->nullable();
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
		Schema::drop('mount_t');
	}

}
