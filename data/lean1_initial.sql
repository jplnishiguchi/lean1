-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: localhost    Database: lean1
-- ------------------------------------------------------
-- Server version	5.7.25-0ubuntu0.18.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `employee_deductions`
--

DROP TABLE IF EXISTS `employee_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_deductions` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(19) unsigned NOT NULL,
  `cutoff_start` date NOT NULL,
  `cutoff_end` date NOT NULL,
  `type` varchar(45) NOT NULL,
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `remarks` varchar(80) NOT NULL DEFAULT 'N/A',
  PRIMARY KEY (`id`,`employee_id`),
  KEY `fk_employee_deductions_employees_idx` (`employee_id`),
  CONSTRAINT `fk_employee_deductions_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_deductions`
--

LOCK TABLES `employee_deductions` WRITE;
/*!40000 ALTER TABLE `employee_deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_groups`
--

DROP TABLE IF EXISTS `employee_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_groups`
--

LOCK TABLES `employee_groups` WRITE;
/*!40000 ALTER TABLE `employee_groups` DISABLE KEYS */;
INSERT INTO `employee_groups` VALUES (1,'App Dev','Application Development'),(2,'HR','Human Resources');
/*!40000 ALTER TABLE `employee_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_roles`
--

DROP TABLE IF EXISTS `employee_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_roles`
--

LOCK TABLES `employee_roles` WRITE;
/*!40000 ALTER TABLE `employee_roles` DISABLE KEYS */;
INSERT INTO `employee_roles` VALUES (1,'Junior Developer','Junior Developer from App Dev'),(2,'HR Associate','Human Resources junior member');
/*!40000 ALTER TABLE `employee_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `employee_number` varchar(45) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `middle_name` varchar(45) NOT NULL DEFAULT 'NM',
  `last_name` varchar(45) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `nickname` varchar(45) NOT NULL,
  `employment_status` enum('Consultant','Freelance','Contractual','Probationary','Regular') DEFAULT 'Consultant',
  `date_hired` date DEFAULT NULL,
  `group` varchar(45) DEFAULT NULL COMMENT 'Department (e.g. Admin, PMO, Dev)',
  `role` varchar(45) DEFAULT NULL COMMENT 'Designation (e.g. HR, Accountant, Manager, Project Manager, Business Analyst, Solutions Architect, Senior Software Engineer, Junior Software Engineer, Intern, OJT)',
  `shift` enum('Regular','Flex') DEFAULT 'Regular',
  `personal_email` varchar(255) NOT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `address` varchar(45) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) NOT NULL,
  `emergency_contact_name` varchar(45) DEFAULT NULL,
  `emergency_contact_phone` varchar(45) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sss` decimal(10,2) DEFAULT '0.00',
  `hdmf` decimal(10,2) DEFAULT '0.00',
  `philhealth` decimal(10,2) DEFAULT '0.00',
  `tax_type` varchar(45) NOT NULL DEFAULT 'Regular',
  `tax_status` varchar(45) NOT NULL DEFAULT 'Single w/o Dependent(s)',
  `sick_leaves` tinyint(4) unsigned DEFAULT '0',
  `vacation_leaves` tinyint(4) unsigned DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'1001','Carina2','Passia','Balmores','Female','Caren','Regular','2013-06-01','Admin','HR','Regular','caren@gmail.com','cbalmores@codebridge.com.ph','Sampaloc,Manila','9971234','639112121243','Mrs. Balmores','639928371283',30000.00,0.00,0.00,0.00,'Regular','Single w/o Dependent(s)',15,15),(2,'1002','Louise Ann','Cruz','San Jose','Female','L.A','Regular','2013-12-01','Dev','Junior Software Engineer','Regular','lasj@gmail.com','lasanjose@codebridge.com.ph','San Mateo, Rizal','9478819','639279712357','Mrs. San Jose','639292434147',40000.00,0.00,0.00,0.00,'Regular','Single w/o Dependent(s)',15,15),(3,'1002','Jose','Vizcarra','Fernandez','Male','Jr','Regular','2013-03-23','Admin','dev','Regular','jfernandez@yahoo.com','jfernandez@codebridge.com.ph','Meycuayan,Bulacan','9971234','639112121255','Mr. Fernandez','639164211729',5000.00,0.00,0.00,0.00,'Regular','Single w/o Dependent(s)',15,15);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `location` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `map` varchar(255) NOT NULL DEFAULT '---',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `holiday` varchar(45) NOT NULL,
  `proclamation` enum('Regular','Special Non-Working') NOT NULL DEFAULT 'Regular',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `controller` varchar(45) NOT NULL,
  `action` varchar(45) NOT NULL,
  `route` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages_employee_roles`
--

DROP TABLE IF EXISTS `pages_employee_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages_employee_roles` (
  `page_id` int(10) unsigned NOT NULL COMMENT 'Employee has and belongs to many Pages',
  `employee_role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`page_id`,`employee_role_id`),
  KEY `fk_pages_employee_roles_employee_roles_idx` (`employee_role_id`),
  KEY `fk_pages_employee_roles_pages_idx` (`page_id`),
  CONSTRAINT `fk_pages_employee_roles_employee_roles` FOREIGN KEY (`employee_role_id`) REFERENCES `employee_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pages_employee_roles_pages` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages_employee_roles`
--

LOCK TABLES `pages_employee_roles` WRITE;
/*!40000 ALTER TABLE `pages_employee_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages_employee_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_login`
--

DROP TABLE IF EXISTS `playdough_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_login` (
  `username` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(35) DEFAULT NULL,
  `level` int(2) NOT NULL DEFAULT '0',
  `content_userid` varchar(20) DEFAULT NULL,
  `content_code` varchar(20) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `owner` varchar(20) DEFAULT NULL,
  `sku` varchar(20) DEFAULT NULL,
  `sku_description` varchar(40) DEFAULT NULL,
  `locked` int(1) NOT NULL DEFAULT '0',
  `locked_date` datetime DEFAULT NULL,
  `logged` int(1) NOT NULL DEFAULT '0',
  `pwd_exp_date` datetime NOT NULL,
  `login_exp_date` datetime DEFAULT NULL,
  `last_activity_time` datetime DEFAULT NULL,
  `is_new_user` int(1) NOT NULL DEFAULT '1',
  `role` varchar(255) NOT NULL DEFAULT 'service-desk',
  `partnerApp` varchar(20) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'ACTIVE',
  `employee_id` bigint(19) unsigned DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_login`
--

LOCK TABLES `playdough_login` WRITE;
/*!40000 ALTER TABLE `playdough_login` DISABLE KEYS */;
INSERT INTO `playdough_login` VALUES ('superadmin','','8fa4f55e10b13c490e33ab4163e7dfb6',0,'','','2019-04-18 18:10:27','','','',0,NULL,1,'2020-03-27 00:00:00','2019-04-19 07:10:27','2019-04-18 11:10:27',0,'system-administrator','','ACTIVE',NULL),('testreguser','test@test.com','d92c4da66ae8542106c2678d13deee15',0,NULL,NULL,'2019-04-18 17:58:52',NULL,NULL,NULL,0,NULL,0,'2024-10-19 00:00:00',NULL,NULL,1,'regular-user',NULL,'ACTIVE',1);
/*!40000 ALTER TABLE `playdough_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_login_pwd_history`
--

DROP TABLE IF EXISTS `playdough_login_pwd_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_login_pwd_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_login_pwd_history`
--

LOCK TABLES `playdough_login_pwd_history` WRITE;
/*!40000 ALTER TABLE `playdough_login_pwd_history` DISABLE KEYS */;
INSERT INTO `playdough_login_pwd_history` VALUES (157,'2019-04-14 14:55:45','testreguser','d92c4da66ae8542106c2678d13deee15');
/*!40000 ALTER TABLE `playdough_login_pwd_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_options`
--

DROP TABLE IF EXISTS `playdough_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_key` varchar(255) NOT NULL,
  `option_value` text NOT NULL,
  `option_type` varchar(10) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `option_description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_key` (`option_key`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_options`
--

LOCK TABLES `playdough_options` WRITE;
/*!40000 ALTER TABLE `playdough_options` DISABLE KEYS */;
INSERT INTO `playdough_options` VALUES (1,'password_length','3','int','2014-06-10 09:02:31','Password Length'),(2,'max_login_attempts','3','int','2014-06-12 05:51:08','Maximum number of failed attempts before lockout'),(3,'inactivity_timeout_seconds','72000','int','2015-03-30 07:18:39','Maximum seconds of inactivity before auto logout'),(4,'rememberme_timeout_seconds','2000','int','2014-05-12 08:59:44','Number of seconds for remembering a user'),(5,'expiration_notification','6','int','2016-04-13 10:56:02','Number of days before user is notified of his password expiration'),(6,'lockout_seconds','20','int','2014-07-09 15:06:49','Number of seconds before automatic unlock after a user was locked out'),(7,'pwd_regexp','/[a-zA-Z\\d!@#$%^&]{1}/','string','2014-05-12 08:59:29','Password regexpression'),(8,'pwd_history','5','int','2014-06-01 13:09:59','Number of passwords to be remembered'),(9,'allow_concurrent_sessions','TRUE','boolean','2014-06-22 14:26:47','Option for allowing concurrent session (TRUE or FALSE)'),(10,'login_warning','LEAN1 development team: This is where the dashboard will be in future releases.','string','2019-04-15 16:18:29','Option for the message shown in the warning banner upon login.');
/*!40000 ALTER TABLE `playdough_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_pages`
--

DROP TABLE IF EXISTS `playdough_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) NOT NULL,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `status` varchar(15) NOT NULL,
  `route` varchar(100) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_pages`
--

LOCK TABLES `playdough_pages` WRITE;
/*!40000 ALTER TABLE `playdough_pages` DISABLE KEYS */;
INSERT INTO `playdough_pages` VALUES (1,'index-index','index','index','INACTIVE','','2014-05-05 06:53:19'),(2,'login-index','login','index','ACTIVE','','2014-05-05 06:53:19'),(3,'user-index','user','index','','','2014-05-05 06:59:09'),(4,'user-add','user','add','','','2014-05-05 06:59:09'),(5,'user-search','user','search','INACTIVE','','2014-05-05 06:59:09'),(6,'user-checkusername','user','checkusername','','','2014-05-05 06:59:09'),(7,'user-update','user','update','','','2014-05-05 06:59:09'),(8,'user-pwdreset','user','pwdreset','ACTIVE','','2014-05-05 06:59:09'),(11,'roles-search','roles','search','','','2014-05-05 06:59:09'),(12,'roles-index','roles','index','','','2014-05-05 06:59:09'),(13,'pwdchange-index','pwdchange','index','','','2014-05-05 06:59:09'),(15,'customerror-not-allowed','customerror','not-allowed','','','2014-05-05 06:59:09'),(16,'login-logout','login','logout','','','2014-05-05 06:59:09'),(17,'pwdreset-index','pwdreset','index','','','2014-05-05 06:59:09'),(20,'pages-index','pages','index','','','2014-05-06 14:15:20'),(21,'pages-search','pages','search','','','2014-05-06 14:15:20'),(22,'roles-update','roles','update','','','2014-05-06 14:16:07'),(23,'pages-add','pages','add','','','2014-05-06 14:17:54'),(24,'options-index','options','index','ACTIVE','','2014-05-06 14:18:46'),(25,'options-search','options','search','ACTIVE','','2014-05-06 14:19:02'),(26,'roles-add','roles','add','ACTIVE','','2014-05-06 14:19:54'),(27,'options-update','options','update','ACTIVE','','2014-05-06 14:39:30'),(28,'pages-update','pages','update','ACTIVE','','2014-05-06 14:41:16'),(31,'roles-delete','roles','delete','ACTIVE','','2014-05-07 00:11:46'),(32,'pages-delete','pages','delete','ACTIVE','','2014-05-07 01:44:48'),(33,'user-delete','user','delete','ACTIVE','','2014-05-07 02:40:14'),(34,'user-bulkadd','user','bulkadd','ACTIVE','','2014-05-08 06:14:50'),(35,'user-bulkdelete','user','bulkdelete','','','2014-05-08 10:34:25'),(36,'weblogs-index','weblogs','index','ACTIVE','','2014-05-09 02:03:43'),(37,'weblogs-search','weblogs','search','ACTIVE','','2014-05-09 02:04:03'),(73,'user-loggedsearch','user','loggedsearch','ACTIVE','','2014-09-17 08:21:15'),(74,'user-loggedusers','user','loggedusers','ACTIVE','','2014-09-17 08:21:15'),(75,'request-view','request','view','ACTIVE','','2016-04-22 05:29:37'),(76,'request-index','request','index','ACTIVE','','2016-04-22 05:32:25'),(77,'request-getall','request','getall','ACTIVE','','2016-04-22 05:35:42'),(78,'schedules-view','schedules','view','ACTIVE','','2016-04-22 06:22:29'),(79,'schedules-add','schedules','add','ACTIVE','','2016-04-22 06:23:04'),(80,'schedules-search','schedules','search','ACTIVE','','2016-04-22 06:23:22'),(81,'empgroup-groups','empgroup','groups','ACTIVE','','2016-04-22 22:09:15'),(82,'empgroup-groupsearch','empgroup','groupsearch','ACTIVE','','2016-04-22 22:45:27'),(83,'empgroup-groupadd','empgroup','groupadd','ACTIVE','','2016-04-22 23:24:12'),(84,'empgroup-groupcheck','empgroup','groupcheck','ACTIVE','','2016-04-23 00:29:35'),(85,'empgroup-groupupdate','empgroup','groupupdate','ACTIVE','','2016-04-23 01:08:57'),(86,'empgroup-roles','emgroup','roles','ACTIVE','','2016-04-23 02:46:23'),(87,'empgroup-rolesearch','empgroup','rolesearch','ACTIVE','','2016-04-23 02:46:43'),(88,'empgroup-roleadd','empgroup','roleadd','ACTIVE','','2016-04-23 03:00:34'),(89,'empgroup-rolecheck','empgroup','rolecheck','ACTIVE','','2016-04-23 03:00:54'),(90,'empgroup-roleupdate','empgroup','roleupdate','ACTIVE','','2016-04-23 03:07:53'),(91,'employee-index','employee','index','ACTIVE','','2019-04-13 14:40:43'),(92,'employee-search','employee','search','ACTIVE','','2019-04-13 14:41:08'),(93,'employee-viewallsearch','employee','viewallsearch','ACTIVE','','2019-04-14 13:01:14'),(94,'employee-update','employee','update','ACTIVE','','2019-04-14 13:03:26'),(95,'user-myaccount','user','myaccount','ACTIVE','','2019-04-14 17:03:32'),(96,'employee-add','employee','add','ACTIVE','','2019-04-17 00:25:14');
/*!40000 ALTER TABLE `playdough_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_roles`
--

DROP TABLE IF EXISTS `playdough_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role` varchar(200) NOT NULL,
  `status` varchar(15) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_roles`
--

LOCK TABLES `playdough_roles` WRITE;
/*!40000 ALTER TABLE `playdough_roles` DISABLE KEYS */;
INSERT INTO `playdough_roles` VALUES (1,'system-administrator','ACTIVE','0000-00-00 00:00:00'),(3,'guest','ACTIVE','0000-00-00 00:00:00'),(7,'regular-user','ACTIVE','2019-04-14 00:14:46');
/*!40000 ALTER TABLE `playdough_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_roles_pages`
--

DROP TABLE IF EXISTS `playdough_roles_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_roles_pages` (
  `role` int(10) NOT NULL,
  `page` int(10) NOT NULL,
  PRIMARY KEY (`role`,`page`),
  KEY `fk_role` (`role`),
  KEY `fk_page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_roles_pages`
--

LOCK TABLES `playdough_roles_pages` WRITE;
/*!40000 ALTER TABLE `playdough_roles_pages` DISABLE KEYS */;
INSERT INTO `playdough_roles_pages` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,15),(1,16),(1,17),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),(1,37),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(1,45),(1,47),(1,48),(1,53),(1,54),(1,55),(1,56),(1,57),(1,58),(1,59),(1,60),(1,61),(1,62),(1,63),(1,64),(1,65),(1,66),(1,73),(1,74),(3,1),(3,2),(3,3),(3,16),(3,17),(3,45),(3,57),(3,58),(3,70),(3,71),(3,100),(3,101),(3,102),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),(7,7),(7,8),(7,11),(7,12),(7,13),(7,15),(7,16),(7,17),(7,20),(7,21),(7,22),(7,23),(7,24),(7,25),(7,26),(7,27),(7,28),(7,31),(7,32),(7,33),(7,34),(7,35),(7,36),(7,37),(7,73),(7,74),(7,75),(7,76),(7,77),(7,78),(7,79),(7,80),(7,81),(7,82),(7,83),(7,84),(7,85),(7,86),(7,87),(7,88),(7,89),(7,90),(7,91),(7,92);
/*!40000 ALTER TABLE `playdough_roles_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playdough_web_logs`
--

DROP TABLE IF EXISTS `playdough_web_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playdough_web_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `ipaddress` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `post_data` text NOT NULL,
  `notes` text,
  `event_status` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `source_url` varchar(255) DEFAULT NULL,
  `log_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16630 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playdough_web_logs`
--

LOCK TABLES `playdough_web_logs` WRITE;
/*!40000 ALTER TABLE `playdough_web_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `playdough_web_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `process_logs`
--

DROP TABLE IF EXISTS `process_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `process_logs` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `who` bigint(19) unsigned NOT NULL,
  `type` enum('system','user') NOT NULL DEFAULT 'system',
  `origin` enum('web','mobile','api') NOT NULL DEFAULT 'web',
  `model` varchar(45) NOT NULL COMMENT 'The database table where an action has been performed.',
  `controller` varchar(45) NOT NULL,
  `action` varchar(45) NOT NULL,
  `data` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime NOT NULL COMMENT 'Date when the action was created.',
  PRIMARY KEY (`id`,`who`),
  KEY `fk_process_logs_employees_idx` (`who`),
  CONSTRAINT `fk_process_logs_employees` FOREIGN KEY (`who`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `process_logs`
--

LOCK TABLES `process_logs` WRITE;
/*!40000 ALTER TABLE `process_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `process_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requests` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(19) unsigned NOT NULL,
  `date` date NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'VL' COMMENT 'Can be one of the following values: OT, UT, VL, SL, EL, OOO, Offset Work, Offset Leave, Offset Undertime',
  `reason` varchar(100) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `status` enum('Filed','Approved','Disapproved') NOT NULL DEFAULT 'Filed',
  `status_reason` varchar(100) NOT NULL DEFAULT 'Filed',
  `approved_by` bigint(19) unsigned NOT NULL DEFAULT '0',
  `approved_date` datetime NOT NULL,
  PRIMARY KEY (`id`,`employee_id`),
  KEY `fk_requests_employees_idx` (`employee_id`),
  CONSTRAINT `fk_requests_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedules` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(19) unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `timezone` varchar(45) NOT NULL DEFAULT 'Asia/Manila',
  `type` enum('Permanent','Temporary') NOT NULL DEFAULT 'Permanent',
  `start_date` date NOT NULL DEFAULT '1000-01-01',
  `end_date` date NOT NULL DEFAULT '1000-01-01',
  `approved_by` bigint(19) unsigned NOT NULL,
  `approved_date` datetime NOT NULL,
  PRIMARY KEY (`id`,`employee_id`),
  KEY `fk_schedules_employees_idx` (`employee_id`),
  CONSTRAINT `fk_schedules_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedules`
--

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
INSERT INTO `schedules` VALUES (1,1,'09:00:00','18:00:00','Asia/Manila','Temporary','2015-06-01','2015-12-31',1,'2016-04-21 20:16:42'),(2,2,'09:00:00','18:00:00','Asia/Manila','Permanent','2015-12-01','2030-12-01',1,'2016-04-21 20:17:09'),(3,1,'10:00:00','19:00:00','Asia/Manila','Temporary','2016-01-01','2016-12-31',1,'2016-04-22 14:59:31'),(8,2,'10:00:00','19:00:00','Asia/Manila','Temporary','2016-04-01','2016-04-30',0,'2016-04-22 20:41:00');
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `data_type` varchar(45) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Web app''s general settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_groups`
--

DROP TABLE IF EXISTS `system_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_groups`
--

LOCK TABLES `system_groups` WRITE;
/*!40000 ALTER TABLE `system_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_roles`
--

DROP TABLE IF EXISTS `system_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_roles`
--

LOCK TABLES `system_roles` WRITE;
/*!40000 ALTER TABLE `system_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_logs`
--

DROP TABLE IF EXISTS `time_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_logs` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(19) unsigned NOT NULL,
  `timezone` varchar(45) NOT NULL DEFAULT 'Asia/Manila',
  `medium` enum('web','mobile','selfie','biometric','nfc') NOT NULL DEFAULT 'web',
  `date` date NOT NULL,
  `clock_in` time NOT NULL,
  `clock_out_lunch` time DEFAULT NULL,
  `clock_in_lunch` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `tardiness` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `nsd` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`,`employee_id`),
  KEY `fk_time_logs_employees_idx` (`employee_id`),
  CONSTRAINT `fk_time_logs_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_logs`
--

LOCK TABLES `time_logs` WRITE;
/*!40000 ALTER TABLE `time_logs` DISABLE KEYS */;
INSERT INTO `time_logs` VALUES (1,1,'Asia/Manila','web','2016-04-25','00:00:00','00:00:00','00:00:00','00:00:00','Bulacan',0.00,80.00,80.00,25.00),(2,3,'Asia/Manila','web','2016-04-25','15:00:00','20:00:00','22:00:00','23:00:00','Meycauayan',0.00,80.00,80.00,25.00);
/*!40000 ALTER TABLE `time_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(19) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `system_role` varchar(45) NOT NULL DEFAULT 'users',
  `system_group` varchar(45) NOT NULL DEFAULT 'users',
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`,`employee_id`),
  KEY `fk_users_employees_idx` (`employee_id`),
  CONSTRAINT `fk_users_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-04-20  0:11:29
