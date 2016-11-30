<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClusterTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('cluster_t')) {

            Schema::create('cluster_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('owner_id')->nullable();
                $table->integer('owner_type_nbr')->nullable();
                $table->string('cluster_id_text', 128)->unique('ux_cluster_cluster_id_text');
                $table->string('subdomain_text', 128);
                $table->integer('max_instances_nbr')->nullable();
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
		Schema::drop('cluster_t');
	}

}
