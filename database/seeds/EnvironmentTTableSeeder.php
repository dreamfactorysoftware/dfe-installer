<?php

use Illuminate\Database\Seeder;

class EnvironmentTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

            \DB::table('environment_t')->delete();

            \DB::table('environment_t')->insert(array(
                0 =>
                    array(
                        'id' => 1,
                        'user_id' => NULL,
                        'environment_id_text' => 'Development',
                        'create_date' => '2016-07-07 18:30:27',
                        'lmod_date' => '2016-07-07 18:30:27',
                    ),
                1 =>
                    array(
                        'id' => 2,
                        'user_id' => NULL,
                        'environment_id_text' => 'Production',
                        'create_date' => '2016-07-07 18:30:27',
                        'lmod_date' => '2016-07-07 18:30:27',
                    ),
            ));
        }

}
