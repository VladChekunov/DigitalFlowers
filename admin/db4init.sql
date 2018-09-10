CREATE TABLE IF NOT EXISTS `mydb`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(24) NOT NULL,
  `pass` VARCHAR(32) NOT NULL,
  `key` VARCHAR(32) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB

INSERT INTO `dflowers`.`users` (`login`, `pass`) VALUES ('Admin', '696d29e0940a4957748fe3fc9efd22a3');

CREATE TABLE `dflowers`.`pages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(64) NOT NULL,
  `title` VARCHAR(128) NOT NULL,
  `source` MEDIUMTEXT NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `status` INT(1) NOT NULL,
  `order` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `url_UNIQUE` (`url` ASC),
  UNIQUE INDEX `title_UNIQUE` (`title` ASC));
