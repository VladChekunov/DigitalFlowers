CREATE TABLE IF NOT EXISTS `mydb`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(24) NOT NULL,
  `pass` VARCHAR(32) NOT NULL,
  `key` VARCHAR(32) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB

INSERT INTO `dflowers`.`users` (`login`, `pass`) VALUES ('Admin', '696d29e0940a4957748fe3fc9efd22a3');
