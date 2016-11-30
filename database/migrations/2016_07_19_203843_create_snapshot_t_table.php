<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSnapshotTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('snapshot_t')) {

            Schema::create('snapshot_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->integer('user_id')->index('ix_snapshot_user_id');
                $table->integer('instance_id')->index('ix_snapshot_instance_id');
                $table->integer('route_hash_id');
                $table->string('snapshot_id_text', 128)->nullable();
                $table->boolean('public_ind')->default(1);
                $table->string('public_url_text', 1024);
                $table->dateTime('expire_date');
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->unique(['user_id', 'snapshot_id_text'], 'ux_snapshot_user_id_snapshot_id');
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
		Schema::drop('snapshot_t');
	}

}
