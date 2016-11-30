<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('server_t')) {

            Schema::create('server_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('server_type_id')->index('ix_server_server_type_id');
                $table->string('server_id_text', 64)->unique('ux_server_server_id');
                $table->string('host_text', 1024);
                $table->integer('mount_id')->nullable()->index('ix_server_mount_id');
                $table->text('config_text', 16777215)->nullable();
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
		Schema::drop('server_t');
	}

}
