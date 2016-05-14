CREATE TABLE IF NOT EXISTS `users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `facebook_id` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fullname` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `username` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `birthday` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `gender` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `is_active` smallint(6) NOT NULL DEFAULT '1',
  `ip` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lastvisit` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL,
  `token` text COLLATE utf8_bin,
  `token_created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;