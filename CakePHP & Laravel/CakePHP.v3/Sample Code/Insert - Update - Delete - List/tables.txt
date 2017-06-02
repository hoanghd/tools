CREATE TABLE `posts` (
`id` int(11) unsigned NOT NULL auto_increment,
`name` varchar(255) default NULL,
`date` datetime default NULL,
`content` text,
`user_id` int(11) default NULL,
PRIMARY KEY (`id`)
);

CREATE TABLE `tags` (
`id` int(11) unsigned NOT NULL auto_increment,
`name` varchar(100) default NULL,
`longname` varchar(255) default NULL,
PRIMARY KEY (`id`)
);

CREATE TABLE `posts_tags` (
`id` int(11) unsigned NOT NULL auto_increment,
`post_id` int(11) unsigned default NULL,
`tag_id` int(11) unsigned default NULL,
PRIMARY KEY (`id`)
);

CREATE TABLE `comments` (
`id` int(11) unsigned NOT NULL auto_increment,
`name` varchar(100) default NULL,
`content` text,
`post_id` int(11) default NULL,
PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
`username` VARCHAR(50),
`password` VARCHAR(255),
`role` VARCHAR(20),
`created` DATETIME DEFAULT NULL,
`modified` DATETIME DEFAULT NULL,
PRIMARY KEY (`id`)
);
