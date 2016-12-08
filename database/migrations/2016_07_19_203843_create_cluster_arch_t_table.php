<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClusterArchTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('cluster_arch_t')) {

            Schema::create('cluster_arch_t', function (Blueprint $table){
                $table->integer('id')->nullable();
                $table->integer('owner_id')->nullable();
                $table->integer('owner_type_nbr')->nullable();
                $table->string('cluster_id_text', 128)->nullable();
                $table->string('subdomain_text', 128)->nullable();
                $table->integer('max_instances_nbr')->nullable();
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
		Schema::drop('cluster_arch_t');
	}

}
