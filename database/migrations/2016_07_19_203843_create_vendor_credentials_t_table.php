<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVendorCredentialsTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('vendor_credentials_t')) {
            Schema::create('vendor_credentials_t', function (Blueprint $table){
                $table->integer('id', true);
                $table->integer('user_id')->nullable()->index('ix_vendor_creds_user_id');
                $table->integer('vendor_id')->index('ix_vendor_creds_vendor_id');
                $table->integer('environment_id')->default(0);
                $table->text('keys_text', 16777215)->nullable();
                $table->string('label_text', 64)->nullable();
                $table->dateTime('create_date');
                $table->timestamp('lmod_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->unique(['user_id', 'vendor_id', 'label_text'], 'ux_vendor_creds_user_vendor_label');
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
		Schema::drop('vendor_credentials_t');
	}

}
