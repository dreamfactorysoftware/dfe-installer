<?php

use Illuminate\Database\Seeder;

class ServerTypeTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
            \DB::table('server_type_t')->delete();

            \DB::table('server_type_t')->insert(array(
                0 =>
                    array(
                        'id' => 1,
                        'type_name_text' => 'db',
                        'schema_text' => '',
                        'create_date' => '2016-07-07 18:30:27',
                        'lmod_date' => '2016-07-07 18:30:27',
                    ),
                1 =>
                    array(
                        'id' => 2,
                        'type_name_text' => 'web',
                        'schema_text' => '',
                        'create_date' => '2016-07-07 18:30:27',
                        'lmod_date' => '2016-07-07 18:30:27',
                    ),
                2 =>
                    array(
                        'id' => 3,
                        'type_name_text' => 'app',
                        'schema_text' => '',
                        'create_date' => '2016-07-07 18:30:27',
                        'lmod_date' => '2016-07-07 18:30:27',
                    ),
            ));
        }
}
