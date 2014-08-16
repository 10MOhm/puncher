SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';
USE `time_manager` ;

-- -----------------------------------------------------
-- Table `time_manager`.`static_page`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `time_manager`.`static_page` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content` VARCHAR(5000) NOT NULL,
  `page_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
ALTER TABLE `time_manager`.`static_page` ADD INDEX(`page_name`);

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


-- ------------------------------------------------------
-- Changes to the check table, delete useless column 
-- check in and add index
-- ------------------------------------------------------
ALTER TABLE `time_manager`.`checks` DROP COLUMN `check_in`;
CREATE INDEX `IDX_DATE_CHECK` ON `time_manager`.`checks` (`date` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;