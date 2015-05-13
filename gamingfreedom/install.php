<?php

include("gfx3/lib.php");

EDatabase::q("
CREATE TABLE IF NOT EXISTS `gfx3_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tgroup` text NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ocs_apitraffic` (
  `ip` bigint(20) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ocs_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `owner` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `content2` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT '0',
  `score` int(3) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL,
  `date` varchar(50) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ocs_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT '1',
  `score` int(3) NOT NULL DEFAULT '50',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `license` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `type` varchar(45) NOT NULL,
  `downloadname1` varchar(255) DEFAULT NULL,
  `downloadlink1` varchar(255) DEFAULT NULL,
  `preview1` varchar(255) NOT NULL DEFAULT 'http://gamingfreedom.org/screenshot-unavailable.png',
  `preview2` varchar(255) NOT NULL DEFAULT 'http://gamingfreedom.org/screenshot-unavailable.png',
  `preview3` varchar(255) NOT NULL DEFAULT 'http://gamingfreedom.org/screenshot-unavailable.png',
  `personid` varchar(255) NOT NULL,
  `version` varchar(25) DEFAULT NULL,
  `summary` text,
  `description` text,
  `changelog` text,
  PRIMARY KEY (`id`),
  KEY `score` (`score`),
  KEY `personid` (`personid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ocs_fan` (
  `person` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  KEY `person` (`person`,`content`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ocs_person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;");

?>
