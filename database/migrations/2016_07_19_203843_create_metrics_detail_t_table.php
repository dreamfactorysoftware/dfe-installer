<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMetricsDetailTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('metrics_detail_t')) {

            Schema::create('metrics_detail_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->integer('user_id')->index('ix_metrics_detail_user_id');
                $table->integer('instance_id')->index('ix_metrics_detail_instance_id');
                $table->date('gather_date');
                $table->text('data_text', 16777215);
                $table->timestamps();
                $table->unique(['user_id', 'instance_id', 'gather_date'], 'ux_metrics_detail_user_instance_date');
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
		Schema::drop('metrics_detail_t');
	}

}
