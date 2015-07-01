<?php
/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */

/*
 * Contains different methods used in testing
 * environment. Mostly for developers or used in
 * the admin panel of the ocs server 
 */
class OCSTest{
	
	public static function reset_ocs_database()
	{
		EDatabase::q("DROP TABLE IF EXISTS `ocs_apitraffic`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_comment`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_content`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_fan`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_person`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_activity`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_friendship`;");
		EDatabase::q("DROP TABLE IF EXISTS `ocs_friendinvitation`;");
		
		EDatabase::q("
		CREATE TABLE IF NOT EXISTS `ocs_apitraffic` (
		  `ip` bigint(20) NOT NULL,
		  `count` int(11) NOT NULL,
		  PRIMARY KEY (`ip`)
		) ENGINE=MyISAM;
		");

		EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_comment` (
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
		) ENGINE=InnoDB;
		");
		EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_content` (
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
		  `preview1` varchar(255) NOT NULL,
		  `preview2` varchar(255) NOT NULL,
		  `preview3` varchar(255) NOT NULL,
		  `personid` varchar(255) NOT NULL,
		  `version` varchar(25) DEFAULT NULL,
		  `summary` text,
		  `description` text,
		  `changelog` text,
		  PRIMARY KEY (`id`),
		  KEY `score` (`score`),
		  KEY `personid` (`personid`)
		) ENGINE=MyISAM;
		");
		EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_fan` (
		  `person` int(11) NOT NULL,
		  `content` int(11) NOT NULL,
		  KEY `person` (`person`,`content`)
		) ENGINE=InnoDB;
		");
		EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_person` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `login` varchar(45) NOT NULL,
		  `password` varchar(45) NOT NULL,
		  `firstname` varchar(45) NOT NULL,
		  `lastname` varchar(45) NOT NULL,
		  `email` varchar(100) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM;");

		EDatabase::q("CREATE TABLE `ocs_activity` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `type` int(2) NOT NULL,
		  `person` int(11) NOT NULL,
		  `timestamp` int(15) NOT NULL,
		  `message` text NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `person` (`person`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

		EDatabase::q("CREATE TABLE `ocs_friendship` (
		  `id1` int(11) NOT NULL,
		  `id2` int(11) NOT NULL,
		  UNIQUE KEY `id1` (`id1`,`id2`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		EDatabase::q("CREATE TABLE `ocs_friendinvitation` (
		  `fromuser` varchar(255) NOT NULL,
		  `touser` varchar(255) NOT NULL,
		  `message` text NOT NULL,
		  UNIQUE KEY `from` (`fromuser`,`touser`),
		  KEY `fromuser` (`fromuser`),
		  KEY `touser` (`touser`),
		  KEY `fromuser_2` (`fromuser`),
		  KEY `touser_2` (`touser`),
		  KEY `fromuser_3` (`fromuser`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
	}
	
}

?>
