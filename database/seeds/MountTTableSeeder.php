<?php

use Illuminate\Database\Seeder;

class MountTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
            \DB::table('mount_t')->delete();

            \DB::table('mount_t')->insert(array(
                0 =>
                    array(
                        'id' => 1,
                        'mount_type_nbr' => 0,
                        'mount_id_text' => 'mount-local-1',
                        'owner_id' => NULL,
                        'owner_type_nbr' => NULL,
                        'root_path_text' => '/data/storage/',
                        'config_text' => '{"disk":"local"}',
                        'last_mount_date' => NULL,
                        'create_date' => '2016-07-07 18:30:27',
                        'lmod_date' => '2016-07-07 18:30:27',
                    ),
            ));
        }
        
        
}
