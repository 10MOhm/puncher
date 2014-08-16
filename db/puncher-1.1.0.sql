SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `time_manager` DEFAULT CHARACTER SET utf8 ;
CREATE SCHEMA IF NOT EXISTS `time_manager` DEFAULT CHARACTER SET utf8 ;
USE `time_manager` ;

-- -----------------------------------------------------
-- Table `time_manager`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `password` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `email` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `activated` TINYINT(1) NOT NULL DEFAULT '1',
  `banned` TINYINT(1) NOT NULL DEFAULT '0',
  `ban_reason` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `new_password_key` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `new_password_requested`  NULL DEFAULT NULL,
  `new_email` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `new_email_key` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `last_ip` VARCHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `last_login`  NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created`  NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `time_manager`.`overtime`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`overtime` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `amount` INT NULL DEFAULT 0,
  `users_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_overtime_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `time_manager`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_overtime_users_idx` ON `time_manager`.`overtime` (`users_id` ASC);


-- -----------------------------------------------------
-- Table `time_manager`.`static_page`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`static_page` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content` VARCHAR(5000) NOT NULL,
  `page_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

USE `time_manager` ;

-- -----------------------------------------------------
-- Table `time_manager`.`checks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`checks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `date`  NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_9F8C0079A76ED395`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_manager`.`users` (`id`))
ENGINE = InnoDB;

CREATE INDEX `IDX_9F8C0079A76ED395` ON `time_manager`.`checks` (`user_id` ASC);

CREATE INDEX `IDX_DATE_CHECK` ON `time_manager`.`checks` (`date` ASC);


-- -----------------------------------------------------
-- Table `time_manager`.`ci_sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`ci_sessions` (
  `session_id` VARCHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '0',
  `ip_address` VARCHAR(16) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '0',
  `user_agent` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `last_activity` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  PRIMARY KEY (`session_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `time_manager`.`login_attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`login_attempts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `login` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `time`  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `time_manager`.`parameters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`parameters` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `stats_period` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_69348FEA76ED395`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_manager`.`users` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `IDX_69348FEA76ED395` ON `time_manager`.`parameters` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `time_manager`.`user_autologin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`user_autologin` (
  `key_id` CHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `user_id` INT(11) NOT NULL DEFAULT '0',
  `user_agent` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `last_ip` VARCHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `last_login`  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_id`, `user_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `time_manager`.`user_profiles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`user_profiles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `country` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `website` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `time_manager`.`user_has_news`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`user_has_news` (
  `user_id` INT(11) NOT NULL,
  `static_page_id` INT NOT NULL,
  `number_of_changes` INT NOT NULL,
  PRIMARY KEY (`user_id`, `static_page_id`),
  CONSTRAINT `fk_users_has_static_page_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_manager`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_static_page_static_page1`
    FOREIGN KEY (`static_page_id`)
    REFERENCES `time_manager`.`static_page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_users_has_static_page_static_page1_idx` ON `time_manager`.`user_has_news` (`static_page_id` ASC);

CREATE INDEX `fk_users_has_static_page_users1_idx` ON `time_manager`.`user_has_news` (`user_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
