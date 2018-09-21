CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(24) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `key` varchar(32) DEFAULT NULL,
  `group` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `dflowers`.`users` (`login`, `pass`) VALUES ('Admin', '696d29e0940a4957748fe3fc9efd22a3');

CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `source` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `status` int(1) NOT NULL,
  `order` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_UNIQUE` (`url`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;


CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `price` int(10) NOT NULL,
  `old_price` int(10) DEFAULT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `image` varchar(128) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
