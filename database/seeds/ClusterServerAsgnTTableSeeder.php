<?php

use Illuminate\Database\Seeder;

class ClusterServerAsgnTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

            \DB::table('cluster_server_asgn_t')->delete();

            \DB::table('cluster_server_asgn_t')->insert(array(
                0 =>
                    array(
                        'cluster_id' => 1,
                        'server_id' => 1,
                        'create_date' => '2016-07-08 18:05:49',
                        'lmod_date' => '2016-07-08 18:05:49',
                    ),
                1 =>
                    array(
                        'cluster_id' => 1,
                        'server_id' => 2,
                        'create_date' => '2016-07-08 18:05:50',
                        'lmod_date' => '2016-07-08 18:05:50',
                    ),
                2 =>
                    array(
                        'cluster_id' => 1,
                        'server_id' => 3,
                        'create_date' => '2016-07-08 18:05:50',
                        'lmod_date' => '2016-07-08 18:05:50',
                    ),
            ));
        }
        
        
}
