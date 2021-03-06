<?php
/**
 * @package PB voting
 * @version 0.1.1
 */
/*
* Creates tables when the plugin is activated
*/
function pb_voting_create_tables( )
{
    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name_reg = $wpdb->prefix . PB_VOTE_TABLE_NAMES['register'];
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name_reg'") != $table_name_reg) {
         //table not in database. Create new table
        $charset_collate = $wpdb->get_charset_collate();

        $command = "CREATE TABLE IF NOT EXISTS $table_name_reg (
            `id` INT NOT NULL AUTO_INCREMENT,
            `voting_id` INT NOT NULL,
            `voter_id` VARCHAR(50) NOT NULL,
            `registration_code` VARCHAR(50) NOT NULL,
            `issued_time` DATETIME NOT NULL,
            `expiration_time` DATETIME NOT NULL,
            `message_id` VARCHAR(50),
            `status` VARCHAR(15) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `id_UNIQUE` (`id` ASC),
            INDEX `REG_CODE` (`registration_code` ASC),
            INDEX `USER` (`voter_id` ASC, `registration_code` ASC),
            INDEX `VOTING_AND_USER` (`voting_id` ASC, `voter_id` ASC))
            $charset_collate;";

        $result = dbDelta( $command );
    }

    $table_name = $wpdb->prefix . PB_VOTE_TABLE_NAMES['votes'];
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
         //table not in database. Create new table
        $charset_collate = $wpdb->get_charset_collate();

        $command = "CREATE TABLE IF NOT EXISTS $table_name (
            `id` INT NOT NULL AUTO_INCREMENT,
            `voting_id`   INT NOT NULL,
            `register_id` INT NOT NULL,
            `project_id`   INT NOT NULL,
            `vote_time`   DATETIME NOT NULL,
            `vote_points` INT(1) NOT NULL,
            `vote_plus_minus` INT(1) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `id_UNIQUE` (`id` ASC),
            INDEX `VOTING` (`voting_id` ASC, `project_id` ASC, `vote_time` ASC),
            INDEX `REG_ID` (`register_id` ASC ),
            CONSTRAINT `REGISTER_ID`
              FOREIGN KEY (`register_id`)
              REFERENCES $table_name_reg (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
            $charset_collate;";

        $result = dbDelta( $command );
    }

    $table_name_reg_log = $wpdb->prefix . PB_VOTE_TABLE_NAMES['register_log'];
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name_reg_log'") != $table_name_reg_log) {
         //table not in database. Create new table
        $charset_collate = $wpdb->get_charset_collate();

        $command = "CREATE TABLE IF NOT EXISTS $table_name_reg_log (
          	`id` INT(11) NOT NULL AUTO_INCREMENT,
          	`reference_id` VARCHAR(30) NULL DEFAULT NULL,
          	`new_status` CHAR(50) NOT NULL,
          	`log_timestamp` DATETIME NOT NULL DEFAULT current_timestamp(),
          	`step` VARCHAR(50) NOT NULL,
          	`description` VARCHAR(200) NULL DEFAULT NULL,
          	`register_id` INT(11) NOT NULL,
          	PRIMARY KEY (`id`),
          	INDEX `FK_wp_pb_register_log_wp_pb_register` (`register_id`),
          	INDEX `IDX_ID_TIMESTAMPT` (`reference_id`, `log_timestamp`),
          	CONSTRAINT `FK_wp_pb_register_log_wp_pb_register`
              FOREIGN KEY (`register_id`)
              REFERENCES `wp_pb_register` (`id`)
              ON UPDATE NO ACTION ON DELETE NO ACTION)
        $charset_collate;";

        $result = dbDelta( $command );
        }
}

function pb_voting_drop_tables( )
{
    global $wpdb, $pb_vote_table_name;

    $table_name = $wpdb->prefix . PB_VOTE_TABLE_NAMES['register'];
    $command = "DROP TABLE IF EXISTS $table_name;";
    $result = $wpdb->query($command);

    $table_name = $wpdb->prefix . PB_VOTE_TABLE_NAMES['register_log'];
    $command = "DROP TABLE IF EXISTS $table_name;";
    $result = $wpdb->query($command);

    $table_name = $wpdb->prefix . PB_VOTE_TABLE_NAMES['votes'];
    $command = "DROP TABLE IF EXISTS $table_name;";
    $result = $wpdb->query($command);

}
