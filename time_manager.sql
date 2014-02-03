CREATE SCHEMA IF NOT EXISTS `time_manager` DEFAULT CHARACTER SET utf8 ;
USE `time_manager` ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
        `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
        `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
        `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
        `user_data` text COLLATE utf8_bin NOT NULL,
        PRIMARY KEY (`session_id`)
        ) 
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

    CREATE TABLE IF NOT EXISTS `login_attempts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip_address` varchar(40) COLLATE utf8_bin NOT NULL,
            `login` varchar(50) COLLATE utf8_bin NOT NULL,
            `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

    -- --------------------------------------------------------

    --
    -- Table structure for table `user_autologin`
    --

    CREATE TABLE IF NOT EXISTS `user_autologin` (
            `key_id` char(32) COLLATE utf8_bin NOT NULL,
            `user_id` int(11) NOT NULL DEFAULT '0',
            `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
            `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
            `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`key_id`,`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

    -- --------------------------------------------------------

    --
    -- Table structure for table `user_profiles`
    --

    CREATE TABLE IF NOT EXISTS `user_profiles` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `country` varchar(20) COLLATE utf8_bin DEFAULT NULL,
            `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

    -- --------------------------------------------------------

    --
    -- Table structure for table `users`
    --

    CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) COLLATE utf8_bin NOT NULL,
            `password` varchar(255) COLLATE utf8_bin NOT NULL,
            `email` varchar(100) COLLATE utf8_bin NOT NULL,
            `activated` tinyint(1) NOT NULL DEFAULT '1',
            `banned` tinyint(1) NOT NULL DEFAULT '0',
            `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
            `new_password_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
            `new_password_requested` datetime DEFAULT NULL,
            `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
            `new_email_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
            `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
            `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

    -- -----------------------------------------------------
    -- Table `time_manager`.`parameters`
    -- -----------------------------------------------------
    CREATE TABLE IF NOT EXISTS `time_manager`.`parameters` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL,
            `stats_period` INT(1) NOT NULL DEFAULT 0 COMMENT ' /* comment truncated */ /*The periods used to calculate overtime :
                                                                                         0 - Calculate on all time
                                                                                         1 - Month
                                                                                         2 - Week
                                                                                         3 - Day*/',
            `working_time` INT(4) NULL COMMENT ' /* comment truncated */ /*The duration of the work day IN MINUTES*/',
            PRIMARY KEY (`id`),
            INDEX `IDX_69348FEA76ED395` (`user_id` ASC),
            CONSTRAINT `FK_69348FEA76ED395`
            FOREIGN KEY (`user_id`)
            REFERENCES `time_manager`.`users` (`id`))
    ENGINE = InnoDB;

    -- -----------------------------------------------------
    -- Table `time_manager`.`overtime`
    -- -----------------------------------------------------
    CREATE  TABLE IF NOT EXISTS `time_manager`.`overtime` (
            `id` INT NOT NULL AUTO_INCREMENT ,
            `amount` INT NULL DEFAULT 0 ,
            `user_id` INT(11) NOT NULL ,
			`date` DATE NOT NULL ,
            PRIMARY KEY (`id`) ,
            UNIQUE KEY (`date`),
            INDEX `fk_overtime_user_idx` (`user_id` ASC) ,
            CONSTRAINT `fk_overtime_users`
            FOREIGN KEY (`user_id` )
            REFERENCES `time_manager`.`users` (`id` )
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
    ENGINE = InnoDB;

    USE `time_manager` ;

    -- -----------------------------------------------------
    -- Table `time_manager`.`checks`
    -- -----------------------------------------------------
    CREATE  TABLE IF NOT EXISTS `time_manager`.`checks` (
            `id` INT(11) NOT NULL AUTO_INCREMENT ,
            `user_id` INT(11) NOT NULL ,
            `check_in` TINYINT(1) NOT NULL ,
            `date` DATETIME  NOT NULL ,
            PRIMARY KEY (`id`) ,
            INDEX `IDX_9F8C0079A76ED395` (`user_id` ASC) ,
            CONSTRAINT `FK_9F8C0079A76ED395`
            FOREIGN KEY (`user_id` )
            REFERENCES `time_manager`.`users` (`id` ))
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;

