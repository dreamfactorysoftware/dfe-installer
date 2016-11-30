<?php

use Illuminate\Database\Seeder;

class ServiceUserTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
            \DB::table('service_user_t')->delete();

            \DB::table('service_user_t')->insert(array(
                0 =>
                    array(
                        'id' => 5,
                        'first_name_text' => 'System',
                        'last_name_text' => 'Administrator',
                        'nickname_text' => 'Admin',
                        'email_addr_text' => 'dfadmin@dreamfactory.com',
                        'password_text' => '$2y$10$tNt0by2lhFl/l1XuZUTitOy..JRkKrCQKUZ0R2TQdFFDF4NY8eO52',
                        'owner_id' => NULL,
                        'owner_type_nbr' => NULL,
                        'last_login_date' => NULL,
                        'last_login_ip_text' => NULL,
                        'remember_token' => NULL,
                        'active_ind' => 1,
                        'create_date' => '2016-07-15 18:32:32',
                        'lmod_date' => '2016-07-15 18:32:32',
                    ),

            ));
        }

}
