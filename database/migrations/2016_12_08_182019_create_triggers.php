<?php

use Illuminate\Database\Migrations\Migration;

class CreateTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `appKey_beforeDelete`;
            CREATE TRIGGER `appKey_beforeDelete` BEFORE DELETE ON `app_key_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `app_key_arch_t` SELECT * FROM `app_key_t`
                WHERE `app_key_t`.`id` = old.id;
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `csa_beforeDelete`; 
            CREATE TRIGGER `csa_beforeDelete` BEFORE DELETE ON `cluster_server_asgn_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `cluster_server_asgn_arch_t` (cluster_id, server_id, create_date, lmod_date)
                VALUES (old.cluster_id, old.server_id, old.create_date, old.lmod_date);
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `cluster_beforeDelete`;
            CREATE TRIGGER `cluster_beforeDelete` BEFORE DELETE ON `cluster_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `cluster_arch_t` SELECT * FROM `cluster_t`
                WHERE `cluster_t`.`id` = old.id;
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `deactivation_beforeDelete`;
            CREATE TRIGGER `deactivation_beforeDelete` BEFORE DELETE ON `deactivation_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `deactivation_arch_t` SELECT * FROM `deactivation_t`
                WHERE `deactivation_t`.`id` = old.id;
            END ');

        DB::unprepared('DROP TRIGGER IF EXISTS `isa_beforeDelete`;
            CREATE TRIGGER `isa_beforeDelete` BEFORE DELETE ON `instance_server_asgn_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `instance_server_asgn_arch_t` SELECT * FROM `instance_server_asgn_t`
                WHERE
                  `instance_server_asgn_t`.`server_id` = old.server_id AND
                  `instance_server_asgn_t`.`instance_id` = old.instance_id;
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `instance_afterInsert`;
            CREATE TRIGGER `instance_afterInsert` AFTER INSERT ON `instance_t` FOR EACH ROW 
            BEGIN
                DELETE FROM `deactivation_t`
                WHERE user_id = new.user_id AND instance_id = new.id;
        
                INSERT INTO `deactivation_t` (user_id, instance_id, activate_by_date, create_date)
                VALUES (new.user_id, new.id, CURRENT_TIMESTAMP + INTERVAL 7 DAY, current_timestamp);
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `instance_beforeDelete`;
            CREATE TRIGGER `instance_beforeDelete` BEFORE DELETE ON `instance_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `instance_arch_t` SELECT * FROM `instance_t`
                WHERE `instance_t`.`id` = old.id;
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `instance_afterDelete`;
            CREATE TRIGGER `instance_afterDelete` AFTER DELETE ON `instance_t` FOR EACH ROW 
            BEGIN
                DELETE FROM `app_key_t`
                WHERE owner_id = old.id AND owner_type_nbr = 1;
                
                DELETE FROM `deactivation_t`
                WHERE instance_id = old.id;
            END');

        DB::unprepared('DROP TRIGGER IF EXISTS `server_beforeDelete`;
            CREATE TRIGGER `server_beforeDelete` BEFORE DELETE ON `server_t` FOR EACH ROW 
            BEGIN
                INSERT INTO `server_arch_t` SELECT * FROM `server_t`
                WHERE `server_t`.`id` = old.id;
            END');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `appKey_beforeDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `csa_beforeDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `cluster_beforeDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `deactivation_beforeDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `isa_beforeDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `instance_afterInsert`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `instance_beforeDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `instance_afterDelete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `server_beforeDelete`;');
    }
}
