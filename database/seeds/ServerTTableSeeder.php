<?php

use Illuminate\Database\Seeder;

class ServerTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        $db_config = [
            'host'      => env('DB_HOST'),
            'port'      => 3306,
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'database'  => env('DB_DATABASE'),
            'driver'    => 'mysql',
            'default-database-name' => env('DB_DATABASE'),
	        'charset'   => 'utf8',
	        'collation' => 'utf8_unicode_ci',
	        'prefix'    => '',
	        'multi-assign'=> 'on'
        ];


    \DB::table('server_t')->delete();

            \DB::table('server_t')->insert(array(
                0 =>
                    array(
                        'id' => 1,
                        'server_type_id' => 2,
                        'server_id_text' => 'web-dfe',
                        'host_text' => 'dfe.local.com',
                        'mount_id' => 1,
                        'config_text' => '[]',
                        'create_date' => '2016-07-08 14:05:49',
                        'lmod_date' => '2016-07-08 14:05:49',
                    ),
                1 =>
                    array(
                        'id' => 2,
                        'server_type_id' => 3,
                        'server_id_text' => 'app-dfe',
                        'host_text' => 'dfe.local.com',
                        'mount_id' => 1,
                        'config_text' => '[]',
                        'create_date' => '2016-07-08 14:05:49',
                        'lmod_date' => '2016-07-08 14:05:49',
                    ),
                2 =>
                    array(
                        'id' => 3,
                        'server_type_id' => 1,
                        'server_id_text' => 'db-dfe',
                        'host_text' => 'dfe.local.com',
                        'mount_id' => 1,
                        'config_text' => json_encode($db_config),
                        'create_date' => '2016-07-08 14:05:49',
                        'lmod_date' => '2016-07-08 14:05:49',
                    ),
            ));
        }

}
