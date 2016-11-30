<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTelemetryTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('telemetry_t')) {

            Schema::create('telemetry_t', function (Blueprint $table){
                $table->bigInteger('id', true)->unsigned();
                $table->string('provider_id_text')->index('ix_telemetry_provider_id');
                $table->dateTime('gather_date')->index('ix_telemetry_gather_date');
                $table->text('data_text', 16777215);
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
		Schema::drop('telemetry_t');
	}

}
