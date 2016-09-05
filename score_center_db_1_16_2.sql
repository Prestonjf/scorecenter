# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.1.9-MariaDB)
# Database: score_center_db
# Generation Time: 2016-09-05 20:24:19 +0000
#
# @package: Tournament Score Center (TSC) - Tournament scoring web application.
# @version: 1.16.2, 09.05.2016
# @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
# @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table EVENT
# ------------------------------------------------------------

DROP TABLE IF EXISTS `EVENT`;

CREATE TABLE `EVENT` (
  `EVENT_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) DEFAULT NULL,
  `COMMENTS` longtext,
  `SCORE_SYSTEM_CODE` varchar(30) DEFAULT NULL,
  `CREATED_BY` int(11) DEFAULT NULL,
  `OFFICIAL_EVENT_FLAG` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`EVENT_ID`),
  KEY `USER_ID_FK` (`CREATED_BY`),
  CONSTRAINT `USER_ID_FK` FOREIGN KEY (`CREATED_BY`) REFERENCES `USER` (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `EVENT` WRITE;
/*!40000 ALTER TABLE `EVENT` DISABLE KEYS */;

INSERT INTO `EVENT` (`EVENT_ID`, `NAME`, `COMMENTS`, `SCORE_SYSTEM_CODE`, `CREATED_BY`, `OFFICIAL_EVENT_FLAG`)
VALUES
	(1,'Air Trajectory','','HIGHRAWTIER',NULL,1),
	(2,'Astronomy','','HIGHRAW',NULL,1),
	(3,'Cell Biology','','HIGHRAW',NULL,1),
	(4,'Mission Possible','','HIGHRAWTIER',NULL,1),
	(5,'Write It, Do It','','HIGHRAWTIER',NULL,1),
	(6,'Experimental Design','','HIGHRAWTIER',NULL,1),
	(7,'Rocks & Minerals','','HIGHRAW',NULL,1),
	(8,'Game On','','HIGHRAWTIER',NULL,1),
	(9,'Wheeled Vehicle','','HIGHRAWTIER',NULL,1),
	(10,'Circuit Lab','','HIGHRAW',NULL,1),
	(11,'Bottle Rocket','','HIGHRAWTIER',NULL,1),
	(12,'Fossils','','HIGHRAW',NULL,1),
	(14,'Wright Stuff','','HIGHRAWTIER',NULL,1),
	(15,'Electric Vehicle','','LOWRAW',NULL,1),
	(16,'Bridge Building','','HIGHRAWTIER4LOW',NULL,1),
	(17,'Chemistry Lab','','HIGHRAW',NULL,1),
	(18,'Dynamic Planet','','HIGHRAW',NULL,1),
	(19,'Forensics','','HIGHRAW',NULL,1),
	(20,'Geologic Mapping','','HIGHRAW',NULL,1),
	(21,'Green Generation','','HIGHRAW',NULL,1),
	(22,'Protein Modeling','','HIGHRAW',NULL,1),
	(23,'Anatomy & Physiology','','HIGHRAW',NULL,1),
	(24,'Disease Detectives','','HIGHRAW',NULL,1),
	(25,'Hydrogeology','','HIGHRAW',NULL,1),
	(26,'Invasive Species','','HIGHRAW',NULL,1),
	(27,'It\'s About Time','','HIGHRAW',NULL,1),
	(28,'Robot Arm','','HIGHRAWTIER',NULL,1),
	(29,'Wind Power','','HIGHRAW',NULL,1),
	(30,'Bio-Process Lab','','HIGHRAW',NULL,1),
	(31,'Crave The Wave','','HIGHRAW',NULL,1),
	(32,'Crime Busters','','HIGHRAW',NULL,1),
	(33,'Elastic Launched Glider','','HIGHRAWTIER',NULL,1),
	(34,'Food Science','','HIGHRAW',NULL,1),
	(35,'Meteorology','','HIGHRAW',NULL,1),
	(36,'Picture This','','HIGHRAW',NULL,1),
	(37,'Reach For The Stars','','HIGHRAW',NULL,1),
	(38,'Road Scholar','','HIGHRAW',NULL,1),
	(39,'Scrambler','','LOWRAW',NULL,1),
	(40,'Roller Coaster','','LOWRAWTIER',NULL,NULL),
	(41,'Source Code','','HIGHRAW',NULL,NULL),
	(43,'Fast Facts','','HIGHRAW',1,1),
	(44,'Hovercraft','','HIGHRAW',1,1),
	(45,'Microbe Mission','','HIGHRAW',1,1),
	(46,'Optics','','HIGHRAW',1,1),
	(47,'Towers','','HIGHRAWTIER',1,1),
	(48,'Ecology','','HIGHRAW',1,1),
	(49,'Helicopters','','HIGHRAWTIER',1,1),
	(50,'Materials Science','','HIGHRAW',1,1),
	(51,'Remote Sensing','','HIGHRAW',1,1);

/*!40000 ALTER TABLE `EVENT` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table REF_DATA
# ------------------------------------------------------------

DROP TABLE IF EXISTS `REF_DATA`;

CREATE TABLE `REF_DATA` (
  `REF_DATA_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `DOMAIN_CODE` varchar(30) DEFAULT NULL,
  `REF_DATA_CODE` varchar(30) DEFAULT NULL,
  `PARENT_REF_DATA_CODE` varchar(30) DEFAULT NULL,
  `SORT_ORDER` int(11) DEFAULT NULL,
  `DISPLAY_TEXT` longtext,
  PRIMARY KEY (`REF_DATA_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `REF_DATA` WRITE;
/*!40000 ALTER TABLE `REF_DATA` DISABLE KEYS */;

INSERT INTO `REF_DATA` (`REF_DATA_ID`, `DOMAIN_CODE`, `REF_DATA_CODE`, `PARENT_REF_DATA_CODE`, `SORT_ORDER`, `DISPLAY_TEXT`)
VALUES
	(1,'ROLE','ADMIN',NULL,0,'Admin'),
	(2,'ROLE','VERIFIER',NULL,1,'Verifier'),
	(3,'ROLE','SUPERVISOR',NULL,2,'Event Supervisor'),
	(4,'PASSWORDRESET','SALT',NULL,0,'w6dMqnoztG591yiuLO8V'),
	(5,'REGISTRATIONCODE','SUPERVISOR',NULL,0,'Science101'),
	(6,'REGISTRATIONCODE','VERIFIER',NULL,1,'Science1358219'),
	(7,'REGISTRATIONCODE','ADMIN',NULL,2,'Science$$1357986420'),
	(8,'MAILSERVER','HOST',NULL,0,''),
	(9,'MAILSERVER','PORT',NULL,1,''),
	(10,'MAILSERVER','USERNAME',NULL,2,''),
	(11,'MAILSERVER','PASSWORD',NULL,3,''),
	(12,'MAILSERVER','SMTPSECURE',NULL,4,''),
	(13,'EMAILMESSAGE','ACCOUNTCREATE',NULL,0,'Thank you for creating an account on Tournament Score Center. You will now be able to enter scores for events assigned to you. If you are a score verifier, you will be able to enter scores for entire tournaments. You may access Score Center at the following address with the user name and password below.'),
	(14,'EMAILMESSAGE','PASSWORDRESET',NULL,1,'A password reset for account <account name> has been requested from the Tournament Score Center application. To reset your password, select the hyperlink below and update your password on the account screen. If this message was sent in error, please disregard this email.'),
	(15,'SCOREALGORITHM','HIGHRAW',NULL,0,'High Raw Score'),
	(16,'SCOREALGORITHM','HIGHRAWTIER',NULL,1,'High Raw Score / Tier Ranked'),
	(17,'SCOREALGORITHM','LOWRAW',NULL,2,'Low Raw Score'),
	(18,'SCOREALGORITHM','LOWRAWTIER',NULL,3,'Low Raw Score / Tier Ranked'),
	(19,'SCOREALGORITHM','HIGHRAWTIER4LOW',NULL,4,'High Raw Score / Tier Ranked / 4th Tier Low'),
	(20,'ROLE','SUPERUSER',NULL,-1,'Super User'),
	(21,'STATE','AL',NULL,0,'Alabama'),
	(22,'REGION','REGION1',NULL,0,'Region 1'),
	(23,'REGION','REGION2',NULL,1,'Region 2'),
	(24,'REGION','REGION3',NULL,2,'Region 3'),
	(25,'REGION','REGION4',NULL,3,'Region 4'),
	(26,'REGION','REGION5',NULL,4,'Region 5'),
	(27,'REGION','REGION6',NULL,5,'Region 6'),
	(28,'REGION','REGION7',NULL,6,'Region 7'),
	(29,'REGION','REGION8',NULL,7,'Region 8'),
	(30,'REGION','REGION9',NULL,8,'Region 9'),
	(31,'REGION','REGION10',NULL,9,'Region 10'),
	(32,'REGION','REGION11',NULL,10,'Region 11'),
	(33,'REGION','REGION12',NULL,11,'Region 12'),
	(34,'REGION','REGION13',NULL,12,'Region 13'),
	(35,'REGION','REGION14',NULL,13,'Region 14'),
	(36,'REGION','REGION15',NULL,14,'Region 15'),
	(37,'REGION','REGION16',NULL,15,'Region 16'),
	(38,'REGION','REGION17',NULL,16,'Region 17'),
	(39,'REGION','REGION18',NULL,17,'Region 18'),
	(40,'REGION','REGION19',NULL,18,'Region 19'),
	(41,'REGION','REGION20',NULL,19,'Region 20'),
	(42,'STATE','AK',NULL,1,'Alaska'),
	(43,'STATE','AZ',NULL,2,'Arizona'),
	(44,'STATE','AR',NULL,3,'Arkansas'),
	(45,'STATE','CA',NULL,4,'California'),
	(46,'STATE','CO',NULL,5,'Colorado'),
	(47,'STATE','CT',NULL,6,'Connecticut'),
	(48,'STATE','DE',NULL,7,'Delaware'),
	(49,'STATE','FL',NULL,8,'Florida'),
	(50,'STATE','GA',NULL,9,'Georgia'),
	(51,'STATE','HI',NULL,10,'Hawaii'),
	(52,'STATE','ID',NULL,11,'Idaho'),
	(53,'STATE','IL',NULL,12,'Illinois'),
	(54,'STATE','IN',NULL,13,'Indiana'),
	(55,'STATE','IA',NULL,14,'Iowa'),
	(56,'STATE','KS',NULL,15,'Kansas'),
	(57,'STATE','KY',NULL,16,'Kentucky'),
	(58,'STATE','LA',NULL,17,'Louisiana'),
	(59,'STATE','ME',NULL,18,'Maine'),
	(60,'STATE','MD',NULL,19,'Maryland'),
	(61,'STATE','MA',NULL,20,'Massachusetts'),
	(62,'STATE','MI',NULL,21,'Michigan'),
	(63,'STATE','MN',NULL,22,'Minnesota'),
	(64,'STATE','MS',NULL,23,'Mississippi'),
	(65,'STATE','MO',NULL,24,'Missouri'),
	(66,'STATE','MT',NULL,25,'Montana'),
	(67,'STATE','NE',NULL,26,'Nebraska'),
	(68,'STATE','NV',NULL,27,'Nevada'),
	(69,'STATE','NH',NULL,28,'New Hampshire'),
	(70,'STATE','NJ',NULL,29,'New Jersey'),
	(71,'STATE','NM',NULL,30,'New Mexico'),
	(72,'STATE','NY',NULL,31,'New York'),
	(73,'STATE','NC',NULL,32,'North Carolina'),
	(74,'STATE','ND',NULL,33,'North Dakota'),
	(75,'STATE','OH',NULL,34,'Ohio'),
	(76,'STATE','OK',NULL,35,'Oklahoma'),
	(77,'STATE','OR',NULL,36,'Oregon'),
	(78,'STATE','PA',NULL,37,'Pennsylvania'),
	(79,'STATE','RI',NULL,38,'Rhode Island'),
	(80,'STATE','SC',NULL,39,'South Carolina'),
	(81,'STATE','SD',NULL,40,'South Dakota'),
	(82,'STATE','TN',NULL,41,'Tennessee'),
	(83,'STATE','TX',NULL,42,'Texas'),
	(84,'STATE','UT',NULL,43,'Utah'),
	(85,'STATE','VT',NULL,44,'Vermont'),
	(86,'STATE','VA',NULL,45,'Virginia'),
	(87,'STATE','WA',NULL,46,'Washington'),
	(88,'STATE','WV',NULL,47,'West Virginia'),
	(89,'STATE','WI',NULL,48,'Wisconsin'),
	(90,'STATE','WY',NULL,49,'Wyoming'),
	(91,'STATE','IO',NULL,50,'International');

/*!40000 ALTER TABLE `REF_DATA` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table TEAM
# ------------------------------------------------------------

DROP TABLE IF EXISTS `TEAM`;

CREATE TABLE `TEAM` (
  `TEAM_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) DEFAULT NULL,
  `CITY` varchar(100) DEFAULT NULL,
  `EMAIL_ADDRESS` varchar(100) DEFAULT NULL,
  `PHONE_NUMBER` varchar(100) DEFAULT NULL,
  `DESCRIPTION` longtext,
  `DIVISION` varchar(5) DEFAULT NULL,
  `CREATED_BY` int(11) DEFAULT NULL,
  `STATE` varchar(30) DEFAULT NULL,
  `REGION` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`TEAM_ID`),
  KEY `USER_ID_FK1` (`CREATED_BY`),
  CONSTRAINT `USER_ID_FK1` FOREIGN KEY (`CREATED_BY`) REFERENCES `USER` (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `TEAM` WRITE;
/*!40000 ALTER TABLE `TEAM` DISABLE KEYS */;


/*!40000 ALTER TABLE `TEAM` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table TEAM_EVENT_SCORE
# ------------------------------------------------------------

DROP TABLE IF EXISTS `TEAM_EVENT_SCORE`;

CREATE TABLE `TEAM_EVENT_SCORE` (
  `TEAM_EVENT_SCORE_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TOURN_TEAM_ID` int(11) DEFAULT NULL,
  `TOURN_EVENT_ID` int(11) DEFAULT NULL,
  `SCORE` int(11) DEFAULT NULL,
  `POINTS_EARNED` int(11) DEFAULT NULL,
  `RAW_SCORE` text,
  `TIER_TEXT` text,
  `TIE_BREAK_TEXT` text,
  `TEAM_STATUS` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`TEAM_EVENT_SCORE_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `TEAM_EVENT_SCORE` WRITE;
/*!40000 ALTER TABLE `TEAM_EVENT_SCORE` DISABLE KEYS */;


/*!40000 ALTER TABLE `TEAM_EVENT_SCORE` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table TOURNAMENT
# ------------------------------------------------------------

DROP TABLE IF EXISTS `TOURNAMENT`;

CREATE TABLE `TOURNAMENT` (
  `TOURNAMENT_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) DEFAULT NULL,
  `LOCATION` varchar(100) DEFAULT NULL,
  `DIVISION` varchar(10) DEFAULT NULL,
  `DATE` date DEFAULT NULL,
  `NUMBER_EVENTS` int(11) DEFAULT NULL,
  `NUMBER_TEAMS` int(11) DEFAULT NULL,
  `HIGHEST_SCORE_POSSIBLE` int(11) DEFAULT NULL,
  `DESCRIPTION` longtext,
  `HIGH_LOW_WIN_FLAG` int(11) DEFAULT NULL,
  `EVENTS_AWARDED` int(11) DEFAULT NULL,
  `OVERALL_AWARDED` int(11) DEFAULT NULL,
  `BEST_NEW_TEAM_FLAG` int(11) DEFAULT NULL,
  `MOST_IMPROVED_FLAG` int(11) DEFAULT NULL,
  `LINKED_TOURN_1` int(11) DEFAULT NULL,
  `LINKED_TOURN_2` int(11) DEFAULT NULL,
  `SCORES_LOCKED_FLAG` int(11) DEFAULT NULL,
  `HIGHEST_SCORE_POSSIBLE_ALT` int(11) DEFAULT NULL,
  `ADDITIONAL_POINTS_NP` int(11) DEFAULT NULL,
  `ADDITIONAL_POINTS_DQ` int(11) DEFAULT NULL,
  `EVENTS_AWARDED_ALT` int(11) DEFAULT NULL,
  `OVERALL_AWARDED_ALT` int(11) DEFAULT NULL,
  `ADMIN_USER_ID` int(11) DEFAULT NULL,
  `TEAM_LIST_1_TEXT` varchar(100) DEFAULT NULL,
  `TEAM_LIST_2_TEXT` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`TOURNAMENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `TOURNAMENT` WRITE;
/*!40000 ALTER TABLE `TOURNAMENT` DISABLE KEYS */;

/*!40000 ALTER TABLE `TOURNAMENT` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table TOURNAMENT_EVENT
# ------------------------------------------------------------

DROP TABLE IF EXISTS `TOURNAMENT_EVENT`;

CREATE TABLE `TOURNAMENT_EVENT` (
  `TOURN_EVENT_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TOURNAMENT_ID` int(11) DEFAULT NULL,
  `EVENT_ID` int(11) DEFAULT NULL,
  `TRIAL_EVENT_FLAG` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `SUBMITTED_FLAG` int(11) DEFAULT NULL,
  `VERIFIED_FLAG` int(11) DEFAULT NULL,
  `COMMENTS` longtext,
  `PRIM_ALT_FLAG` int(11) DEFAULT NULL,
  PRIMARY KEY (`TOURN_EVENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `TOURNAMENT_EVENT` WRITE;
/*!40000 ALTER TABLE `TOURNAMENT_EVENT` DISABLE KEYS */;


/*!40000 ALTER TABLE `TOURNAMENT_EVENT` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table TOURNAMENT_TEAM
# ------------------------------------------------------------

DROP TABLE IF EXISTS `TOURNAMENT_TEAM`;

CREATE TABLE `TOURNAMENT_TEAM` (
  `TOURN_TEAM_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TOURNAMENT_ID` int(11) DEFAULT NULL,
  `TEAM_ID` int(11) DEFAULT NULL,
  `TEAM_NUMBER` text,
  `ALTERNATE_FLAG` int(11) DEFAULT NULL,
  `BEST_NEW_TEAM_FLAG` int(11) DEFAULT NULL,
  `MOST_IMPROVED_TEAM_FLAG` int(11) DEFAULT NULL,
  PRIMARY KEY (`TOURN_TEAM_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `TOURNAMENT_TEAM` WRITE;
/*!40000 ALTER TABLE `TOURNAMENT_TEAM` DISABLE KEYS */;


/*!40000 ALTER TABLE `TOURNAMENT_TEAM` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table TOURNAMENT_VERIFIER
# ------------------------------------------------------------

DROP TABLE IF EXISTS `TOURNAMENT_VERIFIER`;

CREATE TABLE `TOURNAMENT_VERIFIER` (
  `TOURN_VERIFIER_ID` int(11) DEFAULT NULL,
  `TOURNAMENT_ID` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `TOURNAMENT_VERIFIER` WRITE;
/*!40000 ALTER TABLE `TOURNAMENT_VERIFIER` DISABLE KEYS */;


/*!40000 ALTER TABLE `TOURNAMENT_VERIFIER` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table USER
# ------------------------------------------------------------

DROP TABLE IF EXISTS `USER`;

CREATE TABLE `USER` (
  `USER_ID` int(4) NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(65) NOT NULL DEFAULT '',
  `PASSWORD` varchar(1000) NOT NULL DEFAULT '',
  `ROLE_CODE` varchar(30) DEFAULT NULL,
  `FIRST_NAME` varchar(65) DEFAULT NULL,
  `LAST_NAME` varchar(65) DEFAULT NULL,
  `PASSWORD_RESET_SALT` varchar(1000) DEFAULT NULL,
  `ACCOUNT_ACTIVE_FLAG` int(11) DEFAULT NULL,
  `PHONE_NUMBER` varchar(30) DEFAULT NULL,
  `AUTO_CREATED_FLAG` int(11) DEFAULT NULL,
  `STATE_CODE` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `USER` WRITE;
/*!40000 ALTER TABLE `USER` DISABLE KEYS */;

INSERT INTO `USER` (`USER_ID`, `USERNAME`, `PASSWORD`, `ROLE_CODE`, `FIRST_NAME`, `LAST_NAME`, `PASSWORD_RESET_SALT`, `ACCOUNT_ACTIVE_FLAG`, `PHONE_NUMBER`, `AUTO_CREATED_FLAG`, `STATE_CODE`)
VALUES
	(1,'admin@scorecenter.com','$1$iI.MPdlD$ICYP6s7GQH5ucF0kgxCx8.','SUPERUSER','Score Center','Admin',NULL,1,'',NULL,'MI');

/*!40000 ALTER TABLE `USER` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table USER_LOGIN_LOG
# ------------------------------------------------------------

DROP TABLE IF EXISTS `USER_LOGIN_LOG`;

CREATE TABLE `USER_LOGIN_LOG` (
  `USER_LOGIN_LOG_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `USER_ID` int(11) DEFAULT NULL,
  `LOGIN_TIME` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`USER_LOGIN_LOG_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `USER_LOGIN_LOG` WRITE;
/*!40000 ALTER TABLE `USER_LOGIN_LOG` DISABLE KEYS */;


/*!40000 ALTER TABLE `USER_LOGIN_LOG` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
