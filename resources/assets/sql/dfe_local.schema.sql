/********************************************************************************
* DreamFactory Enterprise(tm) Console/Dashboard Schema
* Copyright (c) 2012-infinity DreamFactory Software, Inc. All Rights Reserved
******************************************************************************* 

/********************************************************************************
*
* Grab current variable settings and turn off index checks for reload
*
******************************************************************************* 

SET NAMES utf8;
SET SQL_MODE = '';
SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0;

/** Create the database and use it * 
CREATE DATABASE IF NOT EXISTS `dfe_local`
  DEFAULT CHARACTER SET utf8;

USE `dfe_local`;

/********************************************************************************
*
* Create the core entity and their associated archive tables
*
******************************************************************************* 

/********************************************************************************
* Entity keys: app_key_t and app_key_arch_t
******************************************************************************* 

DROP TABLE IF EXISTS `app_key_t`;

CREATE TABLE `app_key_t` (
  `id`             INT(11)                 NOT NULL AUTO_INCREMENT,
  `owner_id`       INT(11)                 NOT NULL,
  `owner_type_nbr` INT(11)                 NOT NULL,
  `client_id`      VARCHAR(128)
                   COLLATE utf8_unicode_ci NOT NULL,
  `client_secret`  VARCHAR(128)
                   COLLATE utf8_unicode_ci NOT NULL,
  `server_secret`  VARCHAR(128)
                   COLLATE utf8_unicode_ci NOT NULL,
  `key_class_text` VARCHAR(64)
                   COLLATE utf8_unicode_ci NOT NULL,
  `created_at`     TIMESTAMP               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at`     TIMESTAMP               NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_app_key_client_id` (`client_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `app_key_arch_t`;

CREATE TABLE `app_key_arch_t` (
  `id`             INT(11),
  `key_class_text` VARCHAR(64),
  `client_id`      VARCHAR(128),
  `client_secret`  VARCHAR(128),
  `server_secret`  VARCHAR(128),
  `owner_id`       INT(11),
  `owner_type_nbr` INT(11),
  `created_at`     DATETIME,
  `updated_at`     TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Password reset: auth_reset_t
******************************************************************************* 

DROP TABLE IF EXISTS `auth_reset_t`;

CREATE TABLE `auth_reset_t` (
  `email`      VARCHAR(255)
               COLLATE utf8_unicode_ci NOT NULL,
  `token`      VARCHAR(255)
               COLLATE utf8_unicode_ci NOT NULL,
  `created_at` TIMESTAMP               NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `ix_auth_reset_email` (`email`),
  KEY `ix_auth_reset_token` (`token`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Deployment Environments: environment_t
******************************************************************************* 

DROP TABLE IF EXISTS `environment_t`;

CREATE TABLE `environment_t` (
  `id`                  INT(11)     NOT NULL AUTO_INCREMENT,
  `user_id`             INT(11)              DEFAULT NULL,
  `environment_id_text` VARCHAR(64) NOT NULL,
  `create_date`         DATETIME    NOT NULL,
  `lmod_date`           TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_environment_environment_id` (`environment_id_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

/********************************************************************************
* Configuration Settings: config_t
******************************************************************************* 

DROP TABLE IF EXISTS `config_t`;

CREATE TABLE `config_t` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_text`   VARCHAR(64)  NOT NULL,
  `value_text`  TEXT,
  `create_date` DATETIME     NOT NULL,
  `lmod_date`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_config_name_text` (`name_text`)
)
  ENGINE = INNODB
  CHARSET = utf8
  COLLATE = utf8_general_ci;

/********************************************************************************
* Metrics & Telemetry: metrics_t / telemetry_t
******************************************************************************* 

DROP TABLE IF EXISTS `metrics_t`;

CREATE TABLE `metrics_t` (
  `id`                BIGINT(20) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `metrics_data_text` MEDIUMTEXT
                      COLLATE utf8_unicode_ci NOT NULL,
  `sent_ind`          TINYINT(1)              NOT NULL DEFAULT 0,
  `create_date`       DATETIME                NOT NULL,
  `lmod_date`         TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `metrics_detail_t`;

CREATE TABLE `metrics_detail_t` (
  `id`          BIGINT(20) UNSIGNED                NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11)                            NOT NULL,
  `instance_id` INT(11)                            NOT NULL,
  `gather_date` DATE                               NOT NULL,
  `data_text`   MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
  `created_at`  TIMESTAMP                          NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at`  TIMESTAMP                          NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_metrics_detail_user_instance_date` (`user_id`, `instance_id`, `gather_date`),
  KEY `ix_metrics_detail_user_id` (`user_id`),
  KEY `ix_metrics_detail_instance_id` (`instance_id`),
  CONSTRAINT `fk_metrics_detail_instance_id` FOREIGN KEY (`instance_id`) REFERENCES `instance_t` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_metrics_detail_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `telemetry_t`;

CREATE TABLE `telemetry_t` (
  `id`               BIGINT(20) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `provider_id_text` VARCHAR(255)
                     COLLATE utf8_unicode_ci NOT NULL,
  `gather_date`      DATETIME                NOT NULL,
  `data_text`        MEDIUMTEXT
                     COLLATE utf8_unicode_ci NOT NULL,
  `create_date`      DATETIME                NOT NULL,
  `lmod_date`        TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_telemetry_provider_id` (`provider_id_text`),
  KEY `ix_telemetry_gather_date` (`gather_date`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Server Mounts: mount_t
******************************************************************************* 

DROP TABLE IF EXISTS `mount_t`;

CREATE TABLE `mount_t` (
  `id`              INT(11)                 NOT NULL AUTO_INCREMENT,
  `mount_type_nbr`  INT(11)                 NOT NULL DEFAULT '0',
  `mount_id_text`   VARCHAR(64)
                    COLLATE utf8_unicode_ci NOT NULL,
  `owner_id`        INT(11)                          DEFAULT NULL,
  `owner_type_nbr`  INT(11)                          DEFAULT NULL,
  `root_path_text`  VARCHAR(128)
                    COLLATE utf8_unicode_ci          DEFAULT NULL,
  `config_text`     MEDIUMTEXT
                    COLLATE utf8_unicode_ci,
  `last_mount_date` DATETIME                         DEFAULT NULL,
  `create_date`     DATETIME                NOT NULL,
  `lmod_date`       TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_mount_mount_id` (`mount_id_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Artisan Migrations: migration_t
******************************************************************************* 

DROP TABLE IF EXISTS `migration_t`;

CREATE TABLE `migration_t` (
  `migration` VARCHAR(256)
              COLLATE utf8_unicode_ci NOT NULL,
  `batch`     INT(11)                 NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Console User Roles: role_t
******************************************************************************* 

DROP TABLE IF EXISTS `role_t`;

CREATE TABLE `role_t` (
  `id`               INT(11)                 NOT NULL AUTO_INCREMENT,
  `role_name_text`   VARCHAR(64)
                     COLLATE utf8_unicode_ci NOT NULL,
  `description_text` VARCHAR(1024),
  `active_ind`       TINYINT(1)              NOT NULL,
  `home_view_text`   VARCHAR(256)
                     COLLATE utf8_unicode_ci NOT NULL,
  `create_date`      DATETIME                NOT NULL,
  `lmod_date`        TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_role_role_name` (`role_name_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Export Download Hashing: owner_hash_t and route_hash_t
******************************************************************************* 

/** @todo I believe this table is no longer used. Will verify and remove if so. * 
DROP TABLE IF EXISTS `owner_hash_t`;

CREATE TABLE `owner_hash_t` (
  `id`             INT(11)      NOT NULL AUTO_INCREMENT,
  `owner_id`       INT(11)      NOT NULL,
  `owner_type_nbr` INT(11)      NOT NULL,
  `hash_text`      VARCHAR(128) NOT NULL,
  `create_date`    DATETIME     NOT NULL,
  `lmod_date`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `route_hash_t`;

CREATE TABLE `route_hash_t` (
  `id`               INT(11)                 NOT NULL AUTO_INCREMENT,
  `type_nbr`         INT(11)                 NOT NULL DEFAULT '0',
  `hash_text`        VARCHAR(128)
                     COLLATE utf8_unicode_ci NOT NULL,
  `actual_path_text` VARCHAR(1024)
                     COLLATE utf8_unicode_ci NOT NULL,
  `expire_date`      DATETIME                         DEFAULT NULL,
  `create_date`      DATETIME                NOT NULL,
  `lmod_date`        TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_route_hash_hash` (`hash_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Dashboard Users: user_t
******************************************************************************* 

DROP TABLE IF EXISTS `user_t`;

CREATE TABLE `user_t` (
  `id`                     INT(11)      NOT NULL AUTO_INCREMENT,
  `email_addr_text`        VARCHAR(192) NOT NULL,
  `password_text`          VARCHAR(192) NOT NULL,
  `remember_token`         VARCHAR(128)          DEFAULT NULL,
  `first_name_text`        VARCHAR(64)           DEFAULT NULL,
  `last_name_text`         VARCHAR(64)           DEFAULT NULL,
  `nickname_text`          VARCHAR(128)          DEFAULT NULL,
  `api_token_text`         VARCHAR(128)          DEFAULT NULL,
  `storage_id_text`        VARCHAR(64)  NOT NULL,
  `external_id_text`       VARCHAR(128)          DEFAULT NULL,
  `external_password_text` VARCHAR(192)          DEFAULT NULL,
  `owner_id`               INT(11)               DEFAULT NULL,
  `owner_type_nbr`         INT(11)               DEFAULT NULL,
  `company_name_text`      VARCHAR(128)          DEFAULT NULL,
  `title_text`             VARCHAR(128)          DEFAULT NULL,
  `city_text`              VARCHAR(64)           DEFAULT NULL,
  `state_province_text`    VARCHAR(64)           DEFAULT NULL,
  `country_text`           VARCHAR(2)            DEFAULT NULL,
  `postal_code_text`       VARCHAR(32)           DEFAULT NULL,
  `phone_text`             VARCHAR(32)           DEFAULT NULL,
  `opt_in_ind`             TINYINT(1)   NOT NULL DEFAULT '1',
  `agree_ind`              TINYINT(1)   NOT NULL DEFAULT '0',
  `last_login_date`        DATETIME              DEFAULT NULL,
  `last_login_ip_text`     VARCHAR(64)           DEFAULT NULL,
  `admin_ind`              TINYINT(1)   NOT NULL DEFAULT '0',
  `activate_ind`           TINYINT(1)   NOT NULL DEFAULT '0',
  `active_ind`             TINYINT(1)   NOT NULL DEFAULT '1',
  `create_date`            DATETIME     NOT NULL,
  `lmod_date`              TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_user_email_addr` (`email_addr_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Provisioners: vendor_t, vendor_image_t, and vendor_credentials_t
******************************************************************************* 

DROP TABLE IF EXISTS `vendor_t`;

CREATE TABLE `vendor_t` (
  `id`               INT(11)     NOT NULL AUTO_INCREMENT,
  `vendor_name_text` VARCHAR(64) NOT NULL,
  `create_date`      DATETIME    NOT NULL,
  `lmod_date`        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_vendor_vendor_name` (`vendor_name_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `vendor_image_t`;

CREATE TABLE `vendor_image_t` (
  `id`                     INT(11)     NOT NULL AUTO_INCREMENT,
  `vendor_id`              INT(11)     NOT NULL,
  `os_text`                VARCHAR(64) NOT NULL DEFAULT 'Linux',
  `license_text`           VARCHAR(64)          DEFAULT 'Public',
  `image_id_text`          VARCHAR(64) NOT NULL,
  `image_name_text`        VARCHAR(256)         DEFAULT NULL,
  `image_description_text` TEXT,
  `architecture_nbr`       INT(11)     NOT NULL DEFAULT '0',
  `region_text`            VARCHAR(64)          DEFAULT NULL,
  `availability_zone_text` VARCHAR(64)          DEFAULT NULL,
  `root_storage_text`      VARCHAR(32)          DEFAULT NULL,
  `create_date`            DATETIME    NOT NULL,
  `lmod_date`              TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_vendor_image_vendor_image` (`vendor_id`, `image_id_text`),
  CONSTRAINT `fk_vendor_image_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `vendor_credentials_t`;

CREATE TABLE `vendor_credentials_t` (
  `id`             INT(11)   NOT NULL AUTO_INCREMENT,
  `user_id`        INT(11)            DEFAULT NULL,
  `vendor_id`      INT(11)   NOT NULL,
  `environment_id` INT(11)   NOT NULL DEFAULT '0',
  `keys_text`      MEDIUMTEXT,
  `label_text`     VARCHAR(64)        DEFAULT NULL,
  `create_date`    DATETIME  NOT NULL,
  `lmod_date`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_vendor_creds_user_vendor_label` (`user_id`, `vendor_id`, `label_text`),
  KEY `ix_vendor_creds_user_id` (`user_id`),
  KEY `ix_vendor_creds_vendor_id` (`vendor_id`),
  CONSTRAINT `fk_vendor_creds_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Console Users: service_user_t
******************************************************************************* 

DROP TABLE IF EXISTS `service_user_t`;

CREATE TABLE `service_user_t` (
  `id`                 INT(11)      NOT NULL AUTO_INCREMENT,
  `first_name_text`    VARCHAR(64)  NOT NULL,
  `last_name_text`     VARCHAR(64)  NOT NULL,
  `nickname_text`      VARCHAR(128)          DEFAULT NULL,
  `email_addr_text`    VARCHAR(192) NOT NULL,
  `password_text`      VARCHAR(192) NOT NULL,
  `owner_id`           INT(11)               DEFAULT NULL,
  `owner_type_nbr`     INT(11)               DEFAULT NULL,
  `last_login_date`    DATETIME              DEFAULT NULL,
  `last_login_ip_text` VARCHAR(64)           DEFAULT NULL,
  `remember_token`     VARCHAR(128)          DEFAULT NULL,
  `active_ind`         TINYINT(1)   NOT NULL DEFAULT '0',
  `create_date`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lmod_date`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_service_user_email_addr` (`email_addr_text`),
  KEY `ix_service_user_owner_id` (`owner_id`),
  CONSTRAINT `fk_service_user_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `service_user_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Clusters: cluster_t and cluster_arch_t
******************************************************************************* 

DROP TABLE IF EXISTS `cluster_t`;

CREATE TABLE `cluster_t` (
  `id`                INT(11)      NOT NULL AUTO_INCREMENT,
  `owner_id`          INT(11)               DEFAULT NULL,
  `owner_type_nbr`    INT(11)               DEFAULT NULL,
  `cluster_id_text`   VARCHAR(128) NOT NULL,
  `subdomain_text`    VARCHAR(128) NOT NULL,
  `max_instances_nbr` INT(11)               DEFAULT NULL,
  `create_date`       DATETIME     NOT NULL,
  `lmod_date`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_cluster_cluster_id_text` (`cluster_id_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `cluster_arch_t`;

CREATE TABLE `cluster_arch_t` (
  `id`                INT(11)      DEFAULT NULL,
  `owner_id`          INT(11)      DEFAULT NULL,
  `owner_type_nbr`    INT(11)      DEFAULT NULL,
  `cluster_id_text`   VARCHAR(128) DEFAULT NULL,
  `subdomain_text`    VARCHAR(128) DEFAULT NULL,
  `max_instances_nbr` INT(11)      DEFAULT NULL,
  `create_date`       DATETIME     DEFAULT NULL,
  `lmod_date`         TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Servers: server_t, server_arch_t, and server_type_t
******************************************************************************* 

DROP TABLE IF EXISTS `server_type_t`;

CREATE TABLE `server_type_t` (
  `id`             INT(11)     NOT NULL AUTO_INCREMENT,
  `type_name_text` VARCHAR(64) NOT NULL,
  `schema_text`    MEDIUMBLOB  NOT NULL,
  `create_date`    DATETIME    NOT NULL,
  `lmod_date`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_server_type_type_name` (`type_name_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `server_t`;

CREATE TABLE `server_t` (
  `id`             INT(11)       NOT NULL AUTO_INCREMENT,
  `server_type_id` INT(11)       NOT NULL,
  `server_id_text` VARCHAR(64)   NOT NULL,
  `host_text`      VARCHAR(1024) NOT NULL,
  `mount_id`       INT(11)                DEFAULT NULL,
  `config_text`    MEDIUMTEXT,
  `create_date`    DATETIME      NOT NULL,
  `lmod_date`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_server_server_id` (`server_id_text`),
  KEY `ix_server_server_type_id` (`server_type_id`),
  KEY `ix_server_mount_id` (`mount_id`),
  CONSTRAINT `fk_server_server_type_id` FOREIGN KEY (`server_type_id`) REFERENCES `server_type_t` (`id`)
    ON UPDATE CASCADE,
  CONSTRAINT `fk_server_mount_id` FOREIGN KEY (`mount_id`) REFERENCES `mount_t` (`id`)
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `server_arch_t`;

CREATE TABLE `server_arch_t` (
  `id`             INT(11),
  `server_type_id` INT(11),
  `server_id_text` VARCHAR(128),
  `host_text`      VARCHAR(1024),
  `mount_id`       INT(11),
  `config_text`    MEDIUMTEXT,
  `create_date`    DATETIME,
  `lmod_date`      TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Instances: instance_t, instance_arch_t
******************************************************************************* 

DROP TABLE IF EXISTS `instance_t`;

CREATE TABLE `instance_t` (
  `id`                  INT(11)                 NOT NULL   AUTO_INCREMENT,
  `user_id`             INT(11)                 NOT NULL,
  `guest_location_nbr`  INT(11)                 NOT NULL   DEFAULT '0',
  `environment_id`      INT(11)                 NOT NULL   DEFAULT '1',
  `instance_id_text`    VARCHAR(64)
                        CHARACTER SET utf8                 DEFAULT NULL,
  `instance_name_text`  VARCHAR(128)
                        CHARACTER SET utf8                 DEFAULT NULL,
  `instance_data_text`  MEDIUMTEXT
                        COLLATE utf8_unicode_ci,
  `cluster_id`          INT(11)                 NOT NULL   DEFAULT '1',
  `app_server_id`       INT(11)                 NOT NULL   DEFAULT '6',
  `db_server_id`        INT(11)                 NOT NULL   DEFAULT '4',
  `web_server_id`       INT(11)                 NOT NULL   DEFAULT '5',
  `storage_id_text`     VARCHAR(64)
                        CHARACTER SET utf8                 DEFAULT NULL,
  `db_host_text`        VARCHAR(1024)
                        COLLATE utf8_unicode_ci NOT NULL   DEFAULT 'localhost',
  `db_port_nbr`         INT(11)                 NOT NULL   DEFAULT '3306',
  `db_name_text`        VARCHAR(64)
                        CHARACTER SET utf8      NOT NULL   DEFAULT 'dreamfactory',
  `db_user_text`        VARCHAR(64)
                        CHARACTER SET utf8      NOT NULL   DEFAULT 'df_user',
  `db_password_text`    VARCHAR(64)
                        CHARACTER SET utf8      NOT NULL   DEFAULT 'df_user',
  `request_id_text`     VARCHAR(128)
                        CHARACTER SET utf8                 DEFAULT NULL,
  `request_date`        DATETIME                           DEFAULT NULL,
  `activate_ind`        TINYINT(1)              NOT NULL   DEFAULT '0',
  `trial_instance_ind`  TINYINT(1)              NOT NULL   DEFAULT '1',
  `provision_ind`       TINYINT(1)              NOT NULL   DEFAULT '0',
  `deprovision_ind`     TINYINT(1)              NOT NULL   DEFAULT '0',
  `state_nbr`           INT(11)                 NOT NULL   DEFAULT '0',
  `ready_state_nbr`     INT(11)                 NOT NULL   DEFAULT '0',
  `platform_state_nbr`  INT(11)                 NOT NULL   DEFAULT '0',
  `storage_version_nbr` INT(11)                 NOT NULL   DEFAULT '0',
  `last_state_date`     DATETIME                NOT NULL   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `start_date`          DATETIME                           DEFAULT NULL,
  `end_date`            DATETIME                           DEFAULT NULL,
  `terminate_date`      DATETIME                           DEFAULT NULL,
  `create_date`         DATETIME                NOT NULL   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lmod_date`           TIMESTAMP               NOT NULL   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_instance_instance_name` (`instance_name_text`),
  KEY `ix_instance_environment_id` (`environment_id`),
  KEY `ix_instance_user_id` (`user_id`),
  KEY `ix_instance_cluster_id` (`cluster_id`),
  KEY `ix_instance_app_server_id` (`app_server_id`),
  KEY `ix_instance_db_server_id` (`db_server_id`),
  KEY `ix_instance_web_server_id` (`web_server_id`),
  KEY `ix_instance_state_date` (`last_state_date`),
  CONSTRAINT `fk_instance_app_server_id` FOREIGN KEY (`app_server_id`) REFERENCES `server_t` (`id`),
  CONSTRAINT `fk_instance_cluster_id` FOREIGN KEY (`cluster_id`) REFERENCES `cluster_t` (`id`),
  CONSTRAINT `fk_instance_db_server_id` FOREIGN KEY (`db_server_id`) REFERENCES `server_t` (`id`),
  CONSTRAINT `fk_instance_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_t` (`id`),
  CONSTRAINT `fk_instance_web_server` FOREIGN KEY (`web_server_id`) REFERENCES `server_t` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `instance_arch_t`;

CREATE TABLE `instance_arch_t` (
  `id`                  INT(11)                 DEFAULT NULL,
  `user_id`             INT(11)                 DEFAULT NULL,
  `guest_location_nbr`  INT(11)                 DEFAULT NULL,
  `environment_id`      INT(11)                 DEFAULT NULL,
  `instance_id_text`    VARCHAR(64)
                        CHARACTER SET utf8      DEFAULT NULL,
  `instance_name_text`  VARCHAR(128)
                        CHARACTER SET utf8      DEFAULT NULL,
  `instance_data_text`  MEDIUMTEXT
                        COLLATE utf8_unicode_ci,
  `cluster_id`          INT(11)                 DEFAULT NULL,
  `app_server_id`       INT(11)                 DEFAULT NULL,
  `db_server_id`        INT(11)                 DEFAULT NULL,
  `web_server_id`       INT(11)                 DEFAULT NULL,
  `storage_id_text`     VARCHAR(64)
                        CHARACTER SET utf8      DEFAULT NULL,
  `db_host_text`        VARCHAR(1024)
                        COLLATE utf8_unicode_ci DEFAULT NULL,
  `db_port_nbr`         INT(11)                 DEFAULT NULL,
  `db_name_text`        VARCHAR(64)
                        CHARACTER SET utf8      DEFAULT NULL,
  `db_user_text`        VARCHAR(64)
                        CHARACTER SET utf8      DEFAULT NULL,
  `db_password_text`    VARCHAR(64)
                        CHARACTER SET utf8      DEFAULT NULL,
  `request_id_text`     VARCHAR(128)
                        CHARACTER SET utf8      DEFAULT NULL,
  `request_date`        DATETIME                DEFAULT NULL,
  `activate_ind`        TINYINT(1)              DEFAULT '0',
  `trial_instance_ind`  TINYINT(1)              DEFAULT '1',
  `provision_ind`       TINYINT(1)              DEFAULT '0',
  `deprovision_ind`     TINYINT(1)              DEFAULT '0',
  `state_nbr`           INT(11)                 DEFAULT '0',
  `ready_state_nbr`     INT(11)                 DEFAULT '0',
  `platform_state_nbr`  INT(11)                 DEFAULT '0',
  `storage_version_nbr` INT(11)                 DEFAULT '0',
  `last_state_date`     DATETIME                DEFAULT NULL,
  `start_date`          DATETIME                DEFAULT NULL,
  `end_date`            DATETIME                DEFAULT NULL,
  `terminate_date`      DATETIME                DEFAULT NULL,
  `create_date`         DATETIME                DEFAULT NULL,
  `lmod_date`           TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* instance_guest_t and instance_guest_arch_t
******************************************************************************* 

DROP TABLE IF EXISTS `instance_guest_t`;

CREATE TABLE `instance_guest_t` (
  `id`                     INT(11)            NOT NULL AUTO_INCREMENT,
  `instance_id`            INT(11)            NOT NULL,
  `vendor_id`              INT(11)            NOT NULL,
  `vendor_image_id`        INT(11)            NOT NULL DEFAULT '0',
  `vendor_credentials_id`  INT(11)                     DEFAULT NULL,
  `flavor_nbr`             INT(11)            NOT NULL DEFAULT '0',
  `base_image_text`        VARCHAR(32)
                           CHARACTER SET utf8 NOT NULL DEFAULT 't1.micro',
  `region_text`            VARCHAR(32)
                           CHARACTER SET utf8          DEFAULT NULL,
  `availability_zone_text` VARCHAR(32)
                           CHARACTER SET utf8          DEFAULT NULL,
  `security_group_text`    VARCHAR(1024)
                           CHARACTER SET utf8          DEFAULT NULL,
  `ssh_key_text`           VARCHAR(64)
                           CHARACTER SET utf8          DEFAULT NULL,
  `root_device_type_nbr`   INT(11)            NOT NULL DEFAULT '0',
  `public_host_text`       VARCHAR(256)
                           CHARACTER SET utf8          DEFAULT NULL,
  `public_ip_text`         VARCHAR(20)
                           CHARACTER SET utf8          DEFAULT NULL,
  `private_host_text`      VARCHAR(256)
                           CHARACTER SET utf8          DEFAULT NULL,
  `private_ip_text`        VARCHAR(20)
                           CHARACTER SET utf8          DEFAULT NULL,
  `state_nbr`              INT(11)            NOT NULL DEFAULT '0',
  `state_text`             VARCHAR(64)
                           CHARACTER SET utf8          DEFAULT NULL,
  `create_date`            DATETIME           NOT NULL,
  `lmod_date`              TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_instance_guest_instance_id` (`instance_id`),
  CONSTRAINT `fk_instance_guest_instance_id` FOREIGN KEY (`instance_id`) REFERENCES `instance_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `instance_guest_arch_t`;

CREATE TABLE `instance_guest_arch_t` (
  `id`                     INT(11)                 DEFAULT NULL,
  `instance_id`            INT(11)                 DEFAULT NULL,
  `vendor_id`              INT(11)                 DEFAULT NULL,
  `vendor_image_id`        INT(11)                 DEFAULT NULL,
  `vendor_credentials_id`  INT(11)                 DEFAULT NULL,
  `flavor_nbr`             INT(11)                 DEFAULT NULL,
  `base_image_text`        VARCHAR(32)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `region_text`            VARCHAR(32)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `availability_zone_text` VARCHAR(32)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `security_group_text`    VARCHAR(1024)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `ssh_key_text`           VARCHAR(64)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `root_device_type_nbr`   INT(11)                 DEFAULT NULL,
  `public_host_text`       VARCHAR(256)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `public_ip_text`         VARCHAR(20)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_host_text`      VARCHAR(256)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_ip_text`        VARCHAR(20)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_nbr`              INT(11)                 DEFAULT NULL,
  `state_text`             VARCHAR(64)
                           COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_date`            DATETIME                DEFAULT NULL,
  `lmod_date`              TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* deactivation_t and deactivation_arch_t
*
* These tables are used to automatically deprovision instances that are
* never activated after a set period of time.
******************************************************************************* 

DROP TABLE IF EXISTS `deactivation_t`;

CREATE TABLE `deactivation_t` (
  `id`                INT(11)   NOT NULL AUTO_INCREMENT,
  `user_id`           INT(11)   NOT NULL,
  `instance_id`       INT(11)   NOT NULL,
  `activate_by_date`  DATETIME  NOT NULL,
  `extend_count_nbr`  INT(1)    NOT NULL DEFAULT '0',
  `user_notified_nbr` INT(1)    NOT NULL DEFAULT '0',
  `action_reason_nbr` INT(11)   NOT NULL DEFAULT '0',
  `create_date`       DATETIME  NOT NULL,
  `lmod_date`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_deactivation_user_instance` (`user_id`, `instance_id`),
  KEY `ix_deactivation_instance_id` (`instance_id`),
  CONSTRAINT `fk_deactivation_instance_id` FOREIGN KEY (`instance_id`) REFERENCES `instance_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `deactivation_arch_t`;

CREATE TABLE `deactivation_arch_t` (
  `id`                INT(11)  DEFAULT NULL,
  `user_id`           INT(11)  DEFAULT NULL,
  `instance_id`       INT(11)  DEFAULT NULL,
  `activate_by_date`  DATETIME DEFAULT NULL,
  `extend_count_nbr`  INT(1)   DEFAULT '0',
  `user_notified_nbr` INT(1)   DEFAULT '0',
  `action_reason_nbr` INT(11)  DEFAULT '0',
  `create_date`       DATETIME DEFAULT NULL,
  `lmod_date`         TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Job Queue: job_t, job_result_t, and job_fail_t
******************************************************************************* 

DROP TABLE IF EXISTS `job_t`;

CREATE TABLE `job_t` (
  `id`           BIGINT(20) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(256)
                 COLLATE utf8_unicode_ci NOT NULL,
  `payload`      TEXT
                 COLLATE utf8_unicode_ci NOT NULL,
  `attempts`     TINYINT(3) UNSIGNED     NOT NULL,
  `reserved`     TINYINT(3) UNSIGNED     NOT NULL,
  `reserved_at`  INT(10) UNSIGNED                 DEFAULT NULL,
  `available_at` INT(10) UNSIGNED        NOT NULL,
  `created_at`   INT(10) UNSIGNED        NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `job_fail_t`;

CREATE TABLE `job_fail_t` (
  `id`         BIGINT(20) UNSIGNED,
  `connection` TEXT
               COLLATE utf8_unicode_ci,
  `queue`      TEXT
               COLLATE utf8_unicode_ci,
  `payload`    TEXT
               COLLATE utf8_unicode_ci,
  `failed_at`  TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `job_result_t`;

CREATE TABLE `job_result_t` (
  `id`             BIGINT(20) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `result_id_text` VARCHAR(255)
                   COLLATE utf8_unicode_ci NOT NULL,
  `result_text`    MEDIUMTEXT
                   COLLATE utf8_unicode_ci NOT NULL,
  `create_date`    DATETIME                NOT NULL,
  `lmod_date`      TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_job_result_result_id` (`result_id_text`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Limits: limit_t
******************************************************************************* 

DROP TABLE IF EXISTS `limit_t`;

CREATE TABLE `limit_t` (
  `id`             BIGINT(20) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `cluster_id`     INT(11)                          DEFAULT NULL,
  `instance_id`    INT(11)                          DEFAULT NULL,
  `limit_type_nbr` INT(11)                          DEFAULT 0,
  `limit_key_text` VARCHAR(192)
                   COLLATE utf8_unicode_ci NOT NULL,
  `limit_nbr`      INT(11)                          DEFAULT NULL,
  `period_nbr`     INT(11)                          DEFAULT NULL,
  `label_text`     VARCHAR(64)
                   COLLATE utf8_unicode_ci NOT NULL,
  `active_ind`     TINYINT(1)              NOT NULL DEFAULT '1',
  `create_date`    DATETIME                NOT NULL,
  `lmod_date`      TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_limit_label` (`label_text`),
  UNIQUE KEY `ux_limit_cluster_instance_key` (`cluster_id`, `instance_id`, `limit_key_text`),
  KEY `ix_limit_limit_key_text` (`limit_key_text`),
  KEY `ix_limit_cluster_id` (`cluster_id`),
  KEY `ix_limit_instance_id` (`instance_id`),
  CONSTRAINT `fk_limit_cluster_id` FOREIGN KEY (`cluster_id`) REFERENCES `cluster_t` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_limit_instance_id` FOREIGN KEY (`instance_id`) REFERENCES `instance_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
* Instance Exports: snapshot_t
******************************************************************************* 

DROP TABLE IF EXISTS `snapshot_t`;

CREATE TABLE `snapshot_t` (
  `id`               BIGINT(20) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `user_id`          INT(11)                 NOT NULL,
  `instance_id`      INT(11)                 NOT NULL,
  `route_hash_id`    INT(11)                 NOT NULL,
  `snapshot_id_text` VARCHAR(128)
                     COLLATE utf8_unicode_ci          DEFAULT NULL,
  `public_ind`       TINYINT(1)              NOT NULL DEFAULT '1',
  `public_url_text`  VARCHAR(1024)
                     COLLATE utf8_unicode_ci NOT NULL,
  `expire_date`      DATETIME                NOT NULL,
  `create_date`      DATETIME                NOT NULL,
  `lmod_date`        TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_snapshot_user_id_snapshot_id` (`user_id`, `snapshot_id_text`),
  KEY `ix_snapshot_user_id` (`user_id`),
  KEY `ix_snapshot_instance_id` (`instance_id`),
  CONSTRAINT `fk_snapshot_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
*
* Associative Entity Assignment Tables
*
******************************************************************************* 

/********************************************************************************
* Roles -> Users: user_role_asgn_t
******************************************************************************* 

DROP TABLE IF EXISTS `user_role_asgn_t`;

CREATE TABLE `user_role_asgn_t` (
  `user_id`     INT(11)   NOT NULL,
  `role_id`     INT(11)   NOT NULL,
  `create_date` DATETIME  NOT NULL,
  `lmod_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`),
  KEY `fk_role_role_id` (`role_id`),
  CONSTRAINT `fk_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `role_t` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_role_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_t` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Servers -> Clusters: cluster_server_asgn_t and cluster_server_asgn_arch_t
******************************************************************************* 

DROP TABLE IF EXISTS `cluster_server_asgn_t`;

CREATE TABLE `cluster_server_asgn_t` (
  `cluster_id`  INT(11)   NOT NULL,
  `server_id`   INT(11)   NOT NULL,
  `create_date` DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lmod_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cluster_id`, `server_id`),
  KEY `ix_csa_server_id` (`server_id`),
  KEY `ix_csa_cluster_id` (`cluster_id`),
  CONSTRAINT `fk_csa_cluster_id` FOREIGN KEY (`cluster_id`) REFERENCES `cluster_t` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_csa_server_id` FOREIGN KEY (`server_id`) REFERENCES `server_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `cluster_server_asgn_arch_t`;

CREATE TABLE `cluster_server_asgn_arch_t` (
  `cluster_id`  INT(11),
  `server_id`   INT(11),
  `create_date` DATETIME,
  `lmod_date`   TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/********************************************************************************
* Instances -> Servers: instance_server_asgn_t and instance_server_asgn_arch_t
******************************************************************************* 

DROP TABLE IF EXISTS `instance_server_asgn_t`;

CREATE TABLE `instance_server_asgn_t` (
  `instance_id` INT(11)   NOT NULL,
  `server_id`   INT(11)   NOT NULL,
  `create_date` DATETIME  NOT NULL,
  `lmod_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`instance_id`, `server_id`),
  KEY `ix_isa_server_id` (`server_id`),
  KEY `ix_isa_instance_id` (`instance_id`),
  CONSTRAINT `fk_isa_instance_id` FOREIGN KEY (`instance_id`) REFERENCES `instance_t` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_isa_server_id` FOREIGN KEY (`server_id`) REFERENCES `server_t` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

DROP TABLE IF EXISTS `instance_server_asgn_arch_t`;

CREATE TABLE `instance_server_asgn_arch_t` (
  `instance_id` INT(11),
  `server_id`   INT(11),
  `create_date` DATETIME,
  `lmod_date`   TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/********************************************************************************
*
* Triggers!
*
******************************************************************************* 

DELIMITER $$

 DROP TRIGGER   IF EXISTS     `appKey_beforeDelete` 
    CREATE     TRIGGER `appKey_beforeDelete` BEFORE DELETE ON `app_key_t`
FOR EACH ROW BEGIN
  INSERT INTO `app_key_arch_t` SELECT *
                               FROM `app_key_t`
                               WHERE `app_key_t`.`id` = old.id;
END 

  DROP TRIGGER   IF EXISTS     `csa_beforeDelete` 
    CREATE     TRIGGER `csa_beforeDelete` BEFORE DELETE ON `cluster_server_asgn_t`
FOR EACH ROW BEGIN
  INSERT INTO `cluster_server_asgn_arch_t` (cluster_id, server_id, create_date, lmod_date)
  VALUES (old.cluster_id, old.server_id, old.create_date, old.lmod_date);
END 

  DROP TRIGGER   IF EXISTS     `cluster_beforeDelete` 
    CREATE     TRIGGER `cluster_beforeDelete` BEFORE DELETE ON `cluster_t`
FOR EACH ROW BEGIN
  INSERT INTO `cluster_arch_t` SELECT *
                               FROM `cluster_t`
                               WHERE `cluster_t`.`id` = old.id;
END 

  DROP TRIGGER   IF EXISTS     `deactivation_beforeDelete` 
    CREATE     TRIGGER `deactivation_beforeDelete` BEFORE DELETE ON `deactivation_t`
FOR EACH ROW BEGIN
  INSERT INTO `deactivation_arch_t` SELECT *
                                    FROM `deactivation_t`
                                    WHERE `deactivation_t`.`id` = old.id;
END 

  DROP TRIGGER   IF EXISTS     `isa_beforeDelete` 
    CREATE     TRIGGER `isa_beforeDelete` BEFORE DELETE ON `instance_server_asgn_t`
FOR EACH ROW BEGIN
  INSERT INTO `instance_server_asgn_arch_t` SELECT *
                                            FROM `instance_server_asgn_t`
                                            WHERE
                                              `instance_server_asgn_t`.`server_id` = old.server_id AND
                                              `instance_server_asgn_t`.`instance_id` = old.instance_id;
END 

  DROP TRIGGER   IF EXISTS     `instance_afterInsert` 
    CREATE     TRIGGER `instance_afterInsert` AFTER INSERT ON `instance_t`
FOR EACH ROW BEGIN
  DELETE FROM `deactivation_t`
  WHERE user_id = new.user_id AND instance_id = new.id;

  INSERT INTO `deactivation_t` (user_id, instance_id, activate_by_date, create_date)
  VALUES (new.user_id, new.id, CURRENT_TIMESTAMP + INTERVAL 7 DAY, current_timestamp);
END 

  DROP TRIGGER   IF EXISTS     `instance_beforeDelete` 
    CREATE     TRIGGER `instance_beforeDelete` BEFORE DELETE ON `instance_t`
FOR EACH ROW BEGIN
  INSERT INTO `instance_arch_t` SELECT *
                                FROM `instance_t`
                                WHERE `instance_t`.`id` = old.id;
END 

  DROP TRIGGER   IF EXISTS     `instance_afterDelete` 
    CREATE     TRIGGER `instance_afterDelete` AFTER DELETE ON `instance_t`
FOR EACH ROW BEGIN
  DELETE FROM `app_key_t`
  WHERE owner_id = old.id AND owner_type_nbr = 1;

  DELETE FROM `deactivation_t`
  WHERE instance_id = old.id;
END 

  DROP TRIGGER   IF EXISTS     `server_beforeDelete` 
    CREATE     TRIGGER `server_beforeDelete` BEFORE DELETE ON `server_t`
FOR EACH ROW BEGIN
  INSERT INTO `server_arch_t` SELECT *
                              FROM `server_t`
                              WHERE `server_t`.`id` = old.id;
END 

DELIMITER ;

/********************************************************************************
* DreamFactory Enterprise(tm) Console/Dashboard Install/Setup Data
* Copyright (c) 2012-infinity DreamFactory Software, Inc. All Rights Reserved
******************************************************************************* 

/********************************************************************************
* The supported environments
******************************************************************************* 

INSERT INTO `environment_t` (`user_id`, `environment_id_text`, `create_date`, `lmod_date`)
VALUES (NULL, 'Development', NOW(), NOW()), (NULL, 'Production', NOW(), NOW());

/********************************************************************************
* The default local mount
******************************************************************************* 

INSERT INTO `mount_t` (`mount_type_nbr`, `mount_id_text`, `root_path_text`, `owner_id`, `owner_type_nbr`, `config_text`, `create_date`)
VALUES (0, 'mount-local-1', '/data/storage/', NULL, NULL, '{"disk":"local"}', NOW());

/********************************************************************************
* The types of servers allowed in clusters
******************************************************************************* 

INSERT INTO `server_type_t` (`id`, `type_name_text`, `schema_text`, `create_date`, `lmod_date`)
VALUES (1, 'db', '', NOW(), NOW()), (2, 'web', '', NOW(), NOW()), (3, 'app', '', NOW(), NOW());

/********************************************************************************
* Vendors supported with this version
******************************************************************************* 

INSERT INTO `vendor_t` (`id`, `vendor_name_text`, `create_date`, `lmod_date`)
VALUES (1, 'Amazon EC2', NOW(), NOW()), (2, 'DreamFactory', NOW(), NOW()), (3, 'Windows Azure', NOW(), NOW()), (4, 'Rackspace', NOW(), NOW()),
  (5, 'OpenStack', NOW(), NOW());

/********************************************************************************
* Pre-loaded vendor images for instances (this is way old)
******************************************************************************* 

INSERT INTO `vendor_image_t` (`id`, `vendor_id`, `os_text`, `license_text`, `image_id_text`, `image_name_text`, `image_description_text`, `architecture_nbr`, `region_text`, `availability_zone_text`, `root_storage_text`, `create_date`, `lmod_date`)
VALUES (34, 1, 'Linux', 'Public', 'ami-013f9768', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20120728', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW()),
  (169, 1, 'Linux', 'Public', 'ami-057bcf6c', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20120822', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (430, 1, 'Linux', 'Public', 'ami-0d3f9764', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20120728', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (624, 1, 'Linux', 'Public', 'ami-137bcf7a', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20120822', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW()),
  (1865, 1, 'Linux', 'Public', 'ami-3b4ff252', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20121001', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (1920, 1, 'Linux', 'Public', 'ami-3d4ff254', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20121001', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW()),
  (3981, 1, 'Linux', 'Public', 'ami-82fa58eb', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20120616', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW()),
  (4275, 1, 'Linux', 'Public', 'ami-8cfa58e5', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20120616', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (4647, 1, 'Linux', 'Public', 'ami-9878c0f1', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20121026.1', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (4764, 1, 'Linux', 'Public', 'ami-9c78c0f5', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20121026.1', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW()),
  (4946, 1, 'Linux', 'Public', 'ami-a29943cb', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20120424', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW()),
  (5246, 1, 'Linux', 'Public', 'ami-ac9943c5', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20120424', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (12601, 1, 'Linux', 'Public', 'ami-e720ad8e', 'ubuntu/images/ebs/ubuntu-precise-12.04-i386-server-20121218', NULL, 0, NULL, NULL, 'ebs', NOW(), NOW()),
  (13287, 1, 'Linux', 'Public', 'ami-fd20ad94', 'ubuntu/images/ebs/ubuntu-precise-12.04-amd64-server-20121218', NULL, 1, NULL, NULL, 'ebs', NOW(), NOW());

