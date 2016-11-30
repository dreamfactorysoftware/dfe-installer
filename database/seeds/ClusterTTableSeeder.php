<?php

use Illuminate\Database\Seeder;

class ClusterTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

            \DB::table('cluster_t')->delete();

            \DB::table('cluster_t')->insert(array(
                0 =>
                    array(
                        'id' => 1,
                        'owner_id' => NULL,
                        'owner_type_nbr' => NULL,
                        'cluster_id_text' => 'cluster-dfe',
                        'subdomain_text' => 'dfe.local.com',
                        'max_instances_nbr' => NULL,
                        'create_date' => '2016-07-08 14:05:49',
                        'lmod_date' => '2016-07-08 14:05:49',
                    ),
            ));
        }
        
        
}
