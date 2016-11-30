<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVendorCredentialsTTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if ( ! Schema::hasTable('vendor_credentials_t')) {

            Schema::table('vendor_credentials_t', function (Blueprint $table){
                $table->foreign('vendor_id', 'fk_vendor_creds_vendor_id')
                    ->references('id')
                    ->on('vendor_t')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
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
		Schema::table('vendor_credentials_t', function(Blueprint $table)
		{
			$table->dropForeign('fk_vendor_creds_vendor_id');
		});
	}

}
