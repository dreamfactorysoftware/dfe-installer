<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLimitTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('limit_t')) {

            Schema::create('limit_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->integer('cluster_id')->nullable()->index('ix_limit_cluster_id');
                $table->integer('instance_id')->nullable()->index('ix_limit_instance_id');
                $table->integer('limit_type_nbr')->nullable()->default(0);
                $table->string('limit_key_text', 192)->index('ix_limit_limit_key_text');
                $table->integer('limit_nbr')->nullable();
                $table->integer('period_nbr')->nullable();
                $table->string('label_text', 64)->unique('ix_limit_label');
                $table->boolean('active_ind')->default(1);
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->unique(['cluster_id', 'instance_id', 'limit_key_text'], 'ux_limit_cluster_instance_key');
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
		Schema::drop('limit_t');
	}

}
