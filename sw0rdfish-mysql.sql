-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema sw0rdfish
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users` ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `firstName` VARCHAR(255) NOT NULL,
  `lastName` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `createdDate` DATETIME NOT NULL,
  `updatedDate` DATETIME NULL,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `secrets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `secrets` ;

CREATE TABLE IF NOT EXISTS `secrets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  `category` VARCHAR(255) NOT NULL,
  `userId` INT NOT NULL,
  `createdDate` DATETIME NOT NULL,
  `updatedDate` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_user_id_idx` (`userId` ASC),
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bank_account_secrets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank_account_secrets` ;

CREATE TABLE IF NOT EXISTS `bank_account_secrets` (
  `id` INT NOT NULL,
  `accountNumber` TEXT NOT NULL,
  `routingNumber` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_bank_account_secrets_id`
    FOREIGN KEY (`id`)
    REFERENCES `secrets` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `credit_card_secrets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `credit_card_secrets` ;

CREATE TABLE IF NOT EXISTS `credit_card_secrets` (
  `id` INT NOT NULL,
  `cardholder` TEXT NOT NULL,
  `number` TEXT NOT NULL,
  `expirationYear` TEXT NOT NULL,
  `expirationMonth` TEXT NOT NULL,
  `csc` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_credit_card_secrets_id`
    FOREIGN KEY (`id`)
    REFERENCES `secrets` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `email_secrets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `email_secrets` ;

CREATE TABLE IF NOT EXISTS `email_secrets` (
  `id` INT NOT NULL,
  `email` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `url` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_email_secrets_id`
    FOREIGN KEY (`id`)
    REFERENCES `secrets` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `website_credential_secrets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `website_credential_secrets` ;

CREATE TABLE IF NOT EXISTS `website_credential_secrets` (
  `id` INT NOT NULL,
  `url` TEXT NOT NULL,
  `username` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_website_credential_secrets_id`
    FOREIGN KEY (`id`)
    REFERENCES `secrets` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `generic_secrets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `generic_secrets` ;

CREATE TABLE IF NOT EXISTS `generic_secrets` (
  `id` INT NOT NULL,
  `secret` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_generic_secrets_id`
    FOREIGN KEY (`id`)
    REFERENCES `secrets` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_tokens` ;

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `userId` INT NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `expiration` DATETIME NULL,
  `createdDate` DATETIME NOT NULL,
  `updatedDate` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_user_tokens_id_idx` (`userId` ASC),
  CONSTRAINT `fk_user_tokens_id`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
