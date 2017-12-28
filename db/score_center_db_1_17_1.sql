# ************************************************************
# Tournament Score Center DB Updates 1.17.1
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1
# Database: score_center_db
#
# @package: Tournament Score Center (TSC) - Tournament scoring web application.
# @version: 1.17.1, 12.28.2017
# @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
# @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
# ************************************************************

CREATE TABLE `USER_ROLE` (
  `USER_ROLE_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `USER_ID` int(11) DEFAULT NULL,
  `ROLE_CODE` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`USER_ROLE_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO USER_ROLE (USER_ID, ROLE_CODE)  (SELECT user_id, role_code FROM USER);

ALTER TABLE TOURNAMENT_VERIFIER ADD PRIMARY KEY(TOURN_VERIFIER_ID);
