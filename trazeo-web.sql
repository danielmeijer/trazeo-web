-- MySQL dump 10.13  Distrib 5.6.16, for osx10.7 (x86_64)
--
-- Host: localhost    Database: trazeo
-- ------------------------------------------------------
-- Server version	5.6.16

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
-- Table structure for table `Children`
--

DROP TABLE IF EXISTS `Children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Children` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateBirth` datetime NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `sex` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Children`
--

LOCK TABLES `Children` WRITE;
/*!40000 ALTER TABLE `Children` DISABLE KEYS */;
INSERT INTO `Children` VALUES (1,'hidabe_niño_1','2009-01-01 00:00:00',0,'H');
/*!40000 ALTER TABLE `Children` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `routes_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F7C13C46642B8210` (`admin_id`),
  KEY `IDX_F7C13C46AE2C16DC` (`routes_id`),
  CONSTRAINT `FK_F7C13C46AE2C16DC` FOREIGN KEY (`routes_id`) REFERENCES `Routes` (`id`),
  CONSTRAINT `FK_F7C13C46642B8210` FOREIGN KEY (`admin_id`) REFERENCES `UserExtend` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Groups`
--

LOCK TABLES `Groups` WRITE;
/*!40000 ALTER TABLE `Groups` DISABLE KEYS */;
INSERT INTO `Groups` VALUES (1,NULL,NULL,'Grupo 1');
/*!40000 ALTER TABLE `Groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Route`
--

DROP TABLE IF EXISTS `Route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C3050F7D642B8210` (`admin_id`),
  KEY `IDX_C3050F7D8BAC62AF` (`city_id`),
  KEY `IDX_C3050F7DF92F3E70` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Route`
--

LOCK TABLES `Route` WRITE;
/*!40000 ALTER TABLE `Route` DISABLE KEYS */;
/*!40000 ALTER TABLE `Route` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Routes`
--

DROP TABLE IF EXISTS `Routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3579C785642B8210` (`admin_id`),
  KEY `IDX_3579C7858BAC62AF` (`city_id`),
  KEY `IDX_3579C785F92F3E70` (`country_id`),
  CONSTRAINT `FK_3579C785F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `geo_country` (`id`),
  CONSTRAINT `FK_3579C785642B8210` FOREIGN KEY (`admin_id`) REFERENCES `UserExtend` (`id`),
  CONSTRAINT `FK_3579C7858BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `geo_city` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Routes`
--

LOCK TABLES `Routes` WRITE;
/*!40000 ALTER TABLE `Routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UserExtend`
--

DROP TABLE IF EXISTS `UserExtend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserExtend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `nick` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1BB2580EA76ED395` (`user_id`),
  KEY `IDX_1BB2580E8BAC62AF` (`city_id`),
  KEY `IDX_1BB2580EF92F3E70` (`country_id`),
  CONSTRAINT `FK_1BB2580EF92F3E70` FOREIGN KEY (`country_id`) REFERENCES `geo_country` (`id`),
  CONSTRAINT `FK_1BB2580E8BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `geo_city` (`id`),
  CONSTRAINT `FK_1BB2580EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserExtend`
--

LOCK TABLES `UserExtend` WRITE;
/*!40000 ALTER TABLE `UserExtend` DISABLE KEYS */;
INSERT INTO `UserExtend` VALUES (1,1,NULL,NULL,'hidabe');
/*!40000 ALTER TABLE `UserExtend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `children_userextend`
--

DROP TABLE IF EXISTS `children_userextend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `children_userextend` (
  `children_id` int(11) NOT NULL,
  `userextend_id` int(11) NOT NULL,
  PRIMARY KEY (`children_id`,`userextend_id`),
  KEY `IDX_240BA0753D3D2749` (`children_id`),
  KEY `IDX_240BA075A5D248B` (`userextend_id`),
  CONSTRAINT `FK_240BA075A5D248B` FOREIGN KEY (`userextend_id`) REFERENCES `UserExtend` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_240BA0753D3D2749` FOREIGN KEY (`children_id`) REFERENCES `Children` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `children_userextend`
--

LOCK TABLES `children_userextend` WRITE;
/*!40000 ALTER TABLE `children_userextend` DISABLE KEYS */;
INSERT INTO `children_userextend` VALUES (1,1);
/*!40000 ALTER TABLE `children_userextend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fos_user_group`
--

DROP TABLE IF EXISTS `fos_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fos_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user_group`
--

LOCK TABLES `fos_user_group` WRITE;
/*!40000 ALTER TABLE `fos_user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `fos_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fos_user_user`
--

DROP TABLE IF EXISTS `fos_user_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fos_user_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C560D76192FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_C560D761A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user_user`
--

LOCK TABLES `fos_user_user` WRITE;
/*!40000 ALTER TABLE `fos_user_user` DISABLE KEYS */;
INSERT INTO `fos_user_user` VALUES (1,'hidabe','hidabe','fhidalgo@sopinet.com','fhidalgo@sopinet.com',1,'iqk07nsxokoo0wcgsgwc8sg8ows4scc','45vvj4lxVJOEsg93+L6AhDNUAIEJXyBc6rUIL3E4PgyvlHNPOMaCjuqOFPwUMBYD4RtJkmh5+93Rq0x9KGpSaQ==',NULL,0,0,NULL,NULL,NULL,'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}',0,NULL);
/*!40000 ALTER TABLE `fos_user_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fos_user_user_group`
--

DROP TABLE IF EXISTS `fos_user_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fos_user_user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `IDX_B3C77447A76ED395` (`user_id`),
  KEY `IDX_B3C77447FE54D947` (`group_id`),
  CONSTRAINT `FK_B3C77447FE54D947` FOREIGN KEY (`group_id`) REFERENCES `fos_user_group` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B3C77447A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user_user_group`
--

LOCK TABLES `fos_user_user_group` WRITE;
/*!40000 ALTER TABLE `fos_user_user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `fos_user_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_city`
--

DROP TABLE IF EXISTS `geo_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `timezone_id` int(11) DEFAULT NULL,
  `geoname_id` int(11) DEFAULT NULL,
  `name_utf8` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ascii` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `creation_date` datetime NOT NULL,
  `modification_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_297C2D345D83CC1` (`state_id`),
  KEY `IDX_297C2D34F92F3E70` (`country_id`),
  KEY `IDX_297C2D343FE997DE` (`timezone_id`),
  CONSTRAINT `FK_297C2D343FE997DE` FOREIGN KEY (`timezone_id`) REFERENCES `geo_timezone` (`id`),
  CONSTRAINT `FK_297C2D345D83CC1` FOREIGN KEY (`state_id`) REFERENCES `geo_country` (`id`),
  CONSTRAINT `FK_297C2D34F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `geo_country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_city`
--

LOCK TABLES `geo_city` WRITE;
/*!40000 ALTER TABLE `geo_city` DISABLE KEYS */;
/*!40000 ALTER TABLE `geo_city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_country`
--

DROP TABLE IF EXISTS `geo_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code_format` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code_regex` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_prefix` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E465446477153098` (`code`),
  UNIQUE KEY `UNIQ_E46544645E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_country`
--

LOCK TABLES `geo_country` WRITE;
/*!40000 ALTER TABLE `geo_country` DISABLE KEYS */;
INSERT INTO `geo_country` VALUES (1,'AD','Andorra','ad','AD###','^(?:AD)*(\\d{3})$','376'),(2,'AE','United Arab Emirates','ae','','','971'),(3,'AF','Afghanistan','af','','','93'),(4,'AG','Antigua and Barbuda','ag','','','+1-268'),(5,'AI','Anguilla','ai','','','+1-264'),(6,'AL','Albania','al','','','355'),(7,'AM','Armenia','am','######','^(\\d{6})$','374'),(8,'AO','Angola','ao','','','244'),(9,'AQ','Antarctica','aq','','',''),(10,'AR','Argentina','ar','@####@@@','^([A-Z]\\d{4}[A-Z]{3})$','54'),(11,'AS','American Samoa','as','','','+1-684'),(12,'AT','Austria','at','####','^(\\d{4})$','43'),(13,'AU','Australia','au','####','^(\\d{4})$','61'),(14,'AW','Aruba','aw','','','297'),(15,'AX','Aland Islands','ax','','','+358-18'),(16,'AZ','Azerbaijan','az','AZ ####','^(?:AZ)*(\\d{4})$','994'),(17,'BA','Bosnia and Herzegovina','ba','#####','^(\\d{5})$','387'),(18,'BB','Barbados','bb','BB#####','^(?:BB)*(\\d{5})$','+1-246'),(19,'BD','Bangladesh','bd','####','^(\\d{4})$','880'),(20,'BE','Belgium','be','####','^(\\d{4})$','32'),(21,'BF','Burkina Faso','bf','','','226'),(22,'BG','Bulgaria','bg','####','^(\\d{4})$','359'),(23,'BH','Bahrain','bh','####|###','^(\\d{3}\\d?)$','973'),(24,'BI','Burundi','bi','','','257'),(25,'BJ','Benin','bj','','','229'),(26,'BL','Saint Barthelemy','gp','### ###','','590'),(27,'BM','Bermuda','bm','@@ ##','^([A-Z]{2}\\d{2})$','+1-441'),(28,'BN','Brunei','bn','@@####','^([A-Z]{2}\\d{4})$','673'),(29,'BO','Bolivia','bo','','','591'),(30,'BQ','Bonaire, Saint Eustatius and Saba ','bq','','','599'),(31,'BR','Brazil','br','#####-###','^(\\d{8})$','55'),(32,'BS','Bahamas','bs','','','+1-242'),(33,'BT','Bhutan','bt','','','975'),(34,'BV','Bouvet Island','bv','','',''),(35,'BW','Botswana','bw','','','267'),(36,'BY','Belarus','by','######','^(\\d{6})$','375'),(37,'BZ','Belize','bz','','','501'),(38,'CA','Canada','ca','@#@ #@#','^([ABCEGHJKLMNPRSTVXY]\\d[ABCEGHJKLMNPRSTVWXYZ]) ?(\\d[ABCEGHJKLMNPRSTVWXYZ]\\d)$ ','1'),(39,'CC','Cocos Islands','cc','','','61'),(40,'CD','Democratic Republic of the Congo','cd','','','243'),(41,'CF','Central African Republic','cf','','','236'),(42,'CG','Republic of the Congo','cg','','','242'),(43,'CH','Switzerland','ch','####','^(\\d{4})$','41'),(44,'CI','Ivory Coast','ci','','','225'),(45,'CK','Cook Islands','ck','','','682'),(46,'CL','Chile','cl','#######','^(\\d{7})$','56'),(47,'CM','Cameroon','cm','','','237'),(48,'CN','China','cn','######','^(\\d{6})$','86'),(49,'CO','Colombia','co','','','57'),(50,'CR','Costa Rica','cr','####','^(\\d{4})$','506'),(51,'CU','Cuba','cu','CP #####','^(?:CP)*(\\d{5})$','53'),(52,'CV','Cape Verde','cv','####','^(\\d{4})$','238'),(53,'CW','Curacao','cw','','','599'),(54,'CX','Christmas Island','cx','####','^(\\d{4})$','61'),(55,'CY','Cyprus','cy','####','^(\\d{4})$','357'),(56,'CZ','Czech Republic','cz','### ##','^(\\d{5})$','420'),(57,'DE','Germany','de','#####','^(\\d{5})$','49'),(58,'DJ','Djibouti','dj','','','253'),(59,'DK','Denmark','dk','####','^(\\d{4})$','45'),(60,'DM','Dominica','dm','','','+1-767'),(61,'DO','Dominican Republic','do','#####','^(\\d{5})$','+1-809 and 1-829'),(62,'DZ','Algeria','dz','#####','^(\\d{5})$','213'),(63,'EC','Ecuador','ec','@####@','^([a-zA-Z]\\d{4}[a-zA-Z])$','593'),(64,'EE','Estonia','ee','#####','^(\\d{5})$','372'),(65,'EG','Egypt','eg','#####','^(\\d{5})$','20'),(66,'EH','Western Sahara','eh','','','212'),(67,'ER','Eritrea','er','','','291'),(68,'ES','Spain','es','#####','^(\\d{5})$','34'),(69,'ET','Ethiopia','et','####','^(\\d{4})$','251'),(70,'FI','Finland','fi','#####','^(?:FI)*(\\d{5})$','358'),(71,'FJ','Fiji','fj','','','679'),(72,'FK','Falkland Islands','fk','','','500'),(73,'FM','Micronesia','fm','#####','^(\\d{5})$','691'),(74,'FO','Faroe Islands','fo','FO-###','^(?:FO)*(\\d{3})$','298'),(75,'FR','France','fr','#####','^(\\d{5})$','33'),(76,'GA','Gabon','ga','','','241'),(77,'GB','United Kingdom','uk','@# #@@|@## #@@|@@# #@@|@@## #@@|@#@ #@@|@@#@ #@@|GIR0AA','^(([A-Z]\\d{2}[A-Z]{2})|([A-Z]\\d{3}[A-Z]{2})|([A-Z]{2}\\d{2}[A-Z]{2})|([A-Z]{2}\\d{3}[A-Z]{2})|([A-Z]\\d[A-Z]\\d[A-Z]{2})|([A-Z]{2}\\d[A-Z]\\d[A-Z]{2})|(GIR0AA))$','44'),(78,'GD','Grenada','gd','','','+1-473'),(79,'GE','Georgia','ge','####','^(\\d{4})$','995'),(80,'GF','French Guiana','gf','#####','^((97|98)3\\d{2})$','594'),(81,'GG','Guernsey','gg','@# #@@|@## #@@|@@# #@@|@@## #@@|@#@ #@@|@@#@ #@@|GIR0AA','^(([A-Z]\\d{2}[A-Z]{2})|([A-Z]\\d{3}[A-Z]{2})|([A-Z]{2}\\d{2}[A-Z]{2})|([A-Z]{2}\\d{3}[A-Z]{2})|([A-Z]\\d[A-Z]\\d[A-Z]{2})|([A-Z]{2}\\d[A-Z]\\d[A-Z]{2})|(GIR0AA))$','+44-1481'),(82,'GH','Ghana','gh','','','233'),(83,'GI','Gibraltar','gi','','','350'),(84,'GL','Greenland','gl','####','^(\\d{4})$','299'),(85,'GM','Gambia','gm','','','220'),(86,'GN','Guinea','gn','','','224'),(87,'GP','Guadeloupe','gp','#####','^((97|98)\\d{3})$','590'),(88,'GQ','Equatorial Guinea','gq','','','240'),(89,'GR','Greece','gr','### ##','^(\\d{5})$','30'),(90,'GS','South Georgia and the South Sandwich Islands','gs','','',''),(91,'GT','Guatemala','gt','#####','^(\\d{5})$','502'),(92,'GU','Guam','gu','969##','^(969\\d{2})$','+1-671'),(93,'GW','Guinea-Bissau','gw','####','^(\\d{4})$','245'),(94,'GY','Guyana','gy','','','592'),(95,'HK','Hong Kong','hk','','','852'),(96,'HM','Heard Island and McDonald Islands','hm','','',' '),(97,'HN','Honduras','hn','@@####','^([A-Z]{2}\\d{4})$','504'),(98,'HR','Croatia','hr','HR-#####','^(?:HR)*(\\d{5})$','385'),(99,'HT','Haiti','ht','HT####','^(?:HT)*(\\d{4})$','509'),(100,'HU','Hungary','hu','####','^(\\d{4})$','36'),(101,'ID','Indonesia','id','#####','^(\\d{5})$','62'),(102,'IE','Ireland','ie','','','353'),(103,'IL','Israel','il','#####','^(\\d{5})$','972'),(104,'IM','Isle of Man','im','@# #@@|@## #@@|@@# #@@|@@## #@@|@#@ #@@|@@#@ #@@|GIR0AA','^(([A-Z]\\d{2}[A-Z]{2})|([A-Z]\\d{3}[A-Z]{2})|([A-Z]{2}\\d{2}[A-Z]{2})|([A-Z]{2}\\d{3}[A-Z]{2})|([A-Z]\\d[A-Z]\\d[A-Z]{2})|([A-Z]{2}\\d[A-Z]\\d[A-Z]{2})|(GIR0AA))$','+44-1624'),(105,'IN','India','in','######','^(\\d{6})$','91'),(106,'IO','British Indian Ocean Territory','io','','','246'),(107,'IQ','Iraq','iq','#####','^(\\d{5})$','964'),(108,'IR','Iran','ir','##########','^(\\d{10})$','98'),(109,'IS','Iceland','is','###','^(\\d{3})$','354'),(110,'IT','Italy','it','#####','^(\\d{5})$','39'),(111,'JE','Jersey','je','@# #@@|@## #@@|@@# #@@|@@## #@@|@#@ #@@|@@#@ #@@|GIR0AA','^(([A-Z]\\d{2}[A-Z]{2})|([A-Z]\\d{3}[A-Z]{2})|([A-Z]{2}\\d{2}[A-Z]{2})|([A-Z]{2}\\d{3}[A-Z]{2})|([A-Z]\\d[A-Z]\\d[A-Z]{2})|([A-Z]{2}\\d[A-Z]\\d[A-Z]{2})|(GIR0AA))$','+44-1534'),(112,'JM','Jamaica','jm','','','+1-876'),(113,'JO','Jordan','jo','#####','^(\\d{5})$','962'),(114,'JP','Japan','jp','###-####','^(\\d{7})$','81'),(115,'KE','Kenya','ke','#####','^(\\d{5})$','254'),(116,'KG','Kyrgyzstan','kg','######','^(\\d{6})$','996'),(117,'KH','Cambodia','kh','#####','^(\\d{5})$','855'),(118,'KI','Kiribati','ki','','','686'),(119,'KM','Comoros','km','','','269'),(120,'KN','Saint Kitts and Nevis','kn','','','+1-869'),(121,'KP','North Korea','kp','###-###','^(\\d{6})$','850'),(122,'KR','South Korea','kr','SEOUL ###-###','^(?:SEOUL)*(\\d{6})$','82'),(123,'XK','Kosovo','','','',''),(124,'KW','Kuwait','kw','#####','^(\\d{5})$','965'),(125,'KY','Cayman Islands','ky','','','+1-345'),(126,'KZ','Kazakhstan','kz','######','^(\\d{6})$','7'),(127,'LA','Laos','la','#####','^(\\d{5})$','856'),(128,'LB','Lebanon','lb','#### ####|####','^(\\d{4}(\\d{4})?)$','961'),(129,'LC','Saint Lucia','lc','','','+1-758'),(130,'LI','Liechtenstein','li','####','^(\\d{4})$','423'),(131,'LK','Sri Lanka','lk','#####','^(\\d{5})$','94'),(132,'LR','Liberia','lr','####','^(\\d{4})$','231'),(133,'LS','Lesotho','ls','###','^(\\d{3})$','266'),(134,'LT','Lithuania','lt','LT-#####','^(?:LT)*(\\d{5})$','370'),(135,'LU','Luxembourg','lu','####','^(\\d{4})$','352'),(136,'LV','Latvia','lv','LV-####','^(?:LV)*(\\d{4})$','371'),(137,'LY','Libya','ly','','','218'),(138,'MA','Morocco','ma','#####','^(\\d{5})$','212'),(139,'MC','Monaco','mc','#####','^(\\d{5})$','377'),(140,'MD','Moldova','md','MD-####','^(?:MD)*(\\d{4})$','373'),(141,'ME','Montenegro','me','#####','^(\\d{5})$','382'),(142,'MF','Saint Martin','gp','### ###','','590'),(143,'MG','Madagascar','mg','###','^(\\d{3})$','261'),(144,'MH','Marshall Islands','mh','','','692'),(145,'MK','Macedonia','mk','####','^(\\d{4})$','389'),(146,'ML','Mali','ml','','','223'),(147,'MM','Myanmar','mm','#####','^(\\d{5})$','95'),(148,'MN','Mongolia','mn','######','^(\\d{6})$','976'),(149,'MO','Macao','mo','','','853'),(150,'MP','Northern Mariana Islands','mp','','','+1-670'),(151,'MQ','Martinique','mq','#####','^(\\d{5})$','596'),(152,'MR','Mauritania','mr','','','222'),(153,'MS','Montserrat','ms','','','+1-664'),(154,'MT','Malta','mt','@@@ ###|@@@ ##','^([A-Z]{3}\\d{2}\\d?)$','356'),(155,'MU','Mauritius','mu','','','230'),(156,'MV','Maldives','mv','#####','^(\\d{5})$','960'),(157,'MW','Malawi','mw','','','265'),(158,'MX','Mexico','mx','#####','^(\\d{5})$','52'),(159,'MY','Malaysia','my','#####','^(\\d{5})$','60'),(160,'MZ','Mozambique','mz','####','^(\\d{4})$','258'),(161,'NA','Namibia','na','','','264'),(162,'NC','New Caledonia','nc','#####','^(\\d{5})$','687'),(163,'NE','Niger','ne','####','^(\\d{4})$','227'),(164,'NF','Norfolk Island','nf','','','672'),(165,'NG','Nigeria','ng','######','^(\\d{6})$','234'),(166,'NI','Nicaragua','ni','###-###-#','^(\\d{7})$','505'),(167,'NL','Netherlands','nl','#### @@','^(\\d{4}[A-Z]{2})$','31'),(168,'NO','Norway','no','####','^(\\d{4})$','47'),(169,'NP','Nepal','np','#####','^(\\d{5})$','977'),(170,'NR','Nauru','nr','','','674'),(171,'NU','Niue','nu','','','683'),(172,'NZ','New Zealand','nz','####','^(\\d{4})$','64'),(173,'OM','Oman','om','###','^(\\d{3})$','968'),(174,'PA','Panama','pa','','','507'),(175,'PE','Peru','pe','','','51'),(176,'PF','French Polynesia','pf','#####','^((97|98)7\\d{2})$','689'),(177,'PG','Papua New Guinea','pg','###','^(\\d{3})$','675'),(178,'PH','Philippines','ph','####','^(\\d{4})$','63'),(179,'PK','Pakistan','pk','#####','^(\\d{5})$','92'),(180,'PL','Poland','pl','##-###','^(\\d{5})$','48'),(181,'PM','Saint Pierre and Miquelon','pm','#####','^(97500)$','508'),(182,'PN','Pitcairn','pn','','','870'),(183,'PR','Puerto Rico','pr','#####-####','^(\\d{9})$','+1-787 and 1-939'),(184,'PS','Palestinian Territory','ps','','','970'),(185,'PT','Portugal','pt','####-###','^(\\d{7})$','351'),(186,'PW','Palau','pw','96940','^(96940)$','680'),(187,'PY','Paraguay','py','####','^(\\d{4})$','595'),(188,'QA','Qatar','qa','','','974'),(189,'RE','Reunion','re','#####','^((97|98)(4|7|8)\\d{2})$','262'),(190,'RO','Romania','ro','######','^(\\d{6})$','40'),(191,'RS','Serbia','rs','######','^(\\d{6})$','381'),(192,'RU','Russia','ru','######','^(\\d{6})$','7'),(193,'RW','Rwanda','rw','','','250'),(194,'SA','Saudi Arabia','sa','#####','^(\\d{5})$','966'),(195,'SB','Solomon Islands','sb','','','677'),(196,'SC','Seychelles','sc','','','248'),(197,'SD','Sudan','sd','#####','^(\\d{5})$','249'),(198,'SS','South Sudan','','','','211'),(199,'SE','Sweden','se','SE-### ##','^(?:SE)*(\\d{5})$','46'),(200,'SG','Singapore','sg','######','^(\\d{6})$','65'),(201,'SH','Saint Helena','sh','STHL 1ZZ','^(STHL1ZZ)$','290'),(202,'SI','Slovenia','si','SI- ####','^(?:SI)*(\\d{4})$','386'),(203,'SJ','Svalbard and Jan Mayen','sj','','','47'),(204,'SK','Slovakia','sk','###  ##','^(\\d{5})$','421'),(205,'SL','Sierra Leone','sl','','','232'),(206,'SM','San Marino','sm','4789#','^(4789\\d)$','378'),(207,'SN','Senegal','sn','#####','^(\\d{5})$','221'),(208,'SO','Somalia','so','@@  #####','^([A-Z]{2}\\d{5})$','252'),(209,'SR','Suriname','sr','','','597'),(210,'ST','Sao Tome and Principe','st','','','239'),(211,'SV','El Salvador','sv','CP ####','^(?:CP)*(\\d{4})$','503'),(212,'SX','Sint Maarten','sx','','','599'),(213,'SY','Syria','sy','','','963'),(214,'SZ','Swaziland','sz','@###','^([A-Z]\\d{3})$','268'),(215,'TC','Turks and Caicos Islands','tc','TKCA 1ZZ','^(TKCA 1ZZ)$','+1-649'),(216,'TD','Chad','td','','','235'),(217,'TF','French Southern Territories','tf','','',''),(218,'TG','Togo','tg','','','228'),(219,'TH','Thailand','th','#####','^(\\d{5})$','66'),(220,'TJ','Tajikistan','tj','######','^(\\d{6})$','992'),(221,'TK','Tokelau','tk','','','690'),(222,'TL','East Timor','tl','','','670'),(223,'TM','Turkmenistan','tm','######','^(\\d{6})$','993'),(224,'TN','Tunisia','tn','####','^(\\d{4})$','216'),(225,'TO','Tonga','to','','','676'),(226,'TR','Turkey','tr','#####','^(\\d{5})$','90'),(227,'TT','Trinidad and Tobago','tt','','','+1-868'),(228,'TV','Tuvalu','tv','','','688'),(229,'TW','Taiwan','tw','#####','^(\\d{5})$','886'),(230,'TZ','Tanzania','tz','','','255'),(231,'UA','Ukraine','ua','#####','^(\\d{5})$','380'),(232,'UG','Uganda','ug','','','256'),(233,'UM','United States Minor Outlying Islands','um','','','1'),(234,'US','United States','us','#####-####','^\\d{5}(-\\d{4})?$','1'),(235,'UY','Uruguay','uy','#####','^(\\d{5})$','598'),(236,'UZ','Uzbekistan','uz','######','^(\\d{6})$','998'),(237,'VA','Vatican','va','','','379'),(238,'VC','Saint Vincent and the Grenadines','vc','','','+1-784'),(239,'VE','Venezuela','ve','####','^(\\d{4})$','58'),(240,'VG','British Virgin Islands','vg','','','+1-284'),(241,'VI','U.S. Virgin Islands','vi','','','+1-340'),(242,'VN','Vietnam','vn','######','^(\\d{6})$','84'),(243,'VU','Vanuatu','vu','','','678'),(244,'WF','Wallis and Futuna','wf','#####','^(986\\d{2})$','681'),(245,'WS','Samoa','ws','','','685'),(246,'YE','Yemen','ye','','','967'),(247,'YT','Mayotte','yt','#####','^(\\d{5})$','262'),(248,'ZA','South Africa','za','####','^(\\d{4})$','27'),(249,'ZM','Zambia','zm','#####','^(\\d{5})$','260'),(250,'ZW','Zimbabwe','zw','','','263'),(251,'CS','Serbia and Montenegro','cs','#####','^(\\d{5})$','381'),(252,'AN','Netherlands Antilles','an','','','599');
/*!40000 ALTER TABLE `geo_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_state`
--

DROP TABLE IF EXISTS `geo_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `timezone_id` int(11) DEFAULT NULL,
  `geoname_id` int(11) DEFAULT NULL,
  `name_utf8` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ascii` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `creation_date` datetime NOT NULL,
  `modification_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A397F5D4F92F3E70` (`country_id`),
  KEY `IDX_A397F5D43FE997DE` (`timezone_id`),
  CONSTRAINT `FK_A397F5D43FE997DE` FOREIGN KEY (`timezone_id`) REFERENCES `geo_timezone` (`id`),
  CONSTRAINT `FK_A397F5D4F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `geo_country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_state`
--

LOCK TABLES `geo_state` WRITE;
/*!40000 ALTER TABLE `geo_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `geo_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_timezone`
--

DROP TABLE IF EXISTS `geo_timezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_timezone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D9B8C536F92F3E70` (`country_id`),
  CONSTRAINT `FK_D9B8C536F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `geo_country` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_timezone`
--

LOCK TABLES `geo_timezone` WRITE;
/*!40000 ALTER TABLE `geo_timezone` DISABLE KEYS */;
INSERT INTO `geo_timezone` VALUES (1,44,'Africa/Abidjan'),(2,82,'Africa/Accra'),(3,69,'Africa/Addis_Ababa'),(4,62,'Africa/Algiers'),(5,67,'Africa/Asmara'),(6,146,'Africa/Bamako'),(7,41,'Africa/Bangui'),(8,85,'Africa/Banjul'),(9,93,'Africa/Bissau'),(10,157,'Africa/Blantyre'),(11,42,'Africa/Brazzaville'),(12,24,'Africa/Bujumbura'),(13,65,'Africa/Cairo'),(14,138,'Africa/Casablanca'),(15,68,'Africa/Ceuta'),(16,86,'Africa/Conakry'),(17,207,'Africa/Dakar'),(18,230,'Africa/Dar_es_Salaam'),(19,58,'Africa/Djibouti'),(20,47,'Africa/Douala'),(21,66,'Africa/El_Aaiun'),(22,205,'Africa/Freetown'),(23,35,'Africa/Gaborone'),(24,250,'Africa/Harare'),(25,248,'Africa/Johannesburg'),(26,198,'Africa/Juba'),(27,232,'Africa/Kampala'),(28,197,'Africa/Khartoum'),(29,193,'Africa/Kigali'),(30,40,'Africa/Kinshasa'),(31,165,'Africa/Lagos'),(32,76,'Africa/Libreville'),(33,218,'Africa/Lome'),(34,8,'Africa/Luanda'),(35,40,'Africa/Lubumbashi'),(36,249,'Africa/Lusaka'),(37,88,'Africa/Malabo'),(38,160,'Africa/Maputo'),(39,133,'Africa/Maseru'),(40,214,'Africa/Mbabane'),(41,208,'Africa/Mogadishu'),(42,132,'Africa/Monrovia'),(43,115,'Africa/Nairobi'),(44,216,'Africa/Ndjamena'),(45,163,'Africa/Niamey'),(46,152,'Africa/Nouakchott'),(47,21,'Africa/Ouagadougou'),(48,25,'Africa/Porto-Novo'),(49,210,'Africa/Sao_Tome'),(50,137,'Africa/Tripoli'),(51,224,'Africa/Tunis'),(52,161,'Africa/Windhoek'),(53,234,'America/Adak'),(54,234,'America/Anchorage'),(55,5,'America/Anguilla'),(56,4,'America/Antigua'),(57,31,'America/Araguaina'),(58,10,'America/Argentina/Buenos_Aires'),(59,10,'America/Argentina/Catamarca'),(60,10,'America/Argentina/Cordoba'),(61,10,'America/Argentina/Jujuy'),(62,10,'America/Argentina/La_Rioja'),(63,10,'America/Argentina/Mendoza'),(64,10,'America/Argentina/Rio_Gallegos'),(65,10,'America/Argentina/Salta'),(66,10,'America/Argentina/San_Juan'),(67,10,'America/Argentina/San_Luis'),(68,10,'America/Argentina/Tucuman'),(69,10,'America/Argentina/Ushuaia'),(70,14,'America/Aruba'),(71,187,'America/Asuncion'),(72,38,'America/Atikokan'),(73,31,'America/Bahia'),(74,158,'America/Bahia_Banderas'),(75,18,'America/Barbados'),(76,31,'America/Belem'),(77,37,'America/Belize'),(78,38,'America/Blanc-Sablon'),(79,31,'America/Boa_Vista'),(80,49,'America/Bogota'),(81,234,'America/Boise'),(82,38,'America/Cambridge_Bay'),(83,31,'America/Campo_Grande'),(84,158,'America/Cancun'),(85,239,'America/Caracas'),(86,80,'America/Cayenne'),(87,125,'America/Cayman'),(88,234,'America/Chicago'),(89,158,'America/Chihuahua'),(90,50,'America/Costa_Rica'),(91,38,'America/Creston'),(92,31,'America/Cuiaba'),(93,53,'America/Curacao'),(94,84,'America/Danmarkshavn'),(95,38,'America/Dawson'),(96,38,'America/Dawson_Creek'),(97,234,'America/Denver'),(98,234,'America/Detroit'),(99,60,'America/Dominica'),(100,38,'America/Edmonton'),(101,31,'America/Eirunepe'),(102,211,'America/El_Salvador'),(103,31,'America/Fortaleza'),(104,38,'America/Glace_Bay'),(105,84,'America/Godthab'),(106,38,'America/Goose_Bay'),(107,215,'America/Grand_Turk'),(108,78,'America/Grenada'),(109,87,'America/Guadeloupe'),(110,91,'America/Guatemala'),(111,63,'America/Guayaquil'),(112,94,'America/Guyana'),(113,38,'America/Halifax'),(114,51,'America/Havana'),(115,158,'America/Hermosillo'),(116,234,'America/Indiana/Indianapolis'),(117,234,'America/Indiana/Knox'),(118,234,'America/Indiana/Marengo'),(119,234,'America/Indiana/Petersburg'),(120,234,'America/Indiana/Tell_City'),(121,234,'America/Indiana/Vevay'),(122,234,'America/Indiana/Vincennes'),(123,234,'America/Indiana/Winamac'),(124,38,'America/Inuvik'),(125,38,'America/Iqaluit'),(126,112,'America/Jamaica'),(127,234,'America/Juneau'),(128,234,'America/Kentucky/Louisville'),(129,234,'America/Kentucky/Monticello'),(130,30,'America/Kralendijk'),(131,29,'America/La_Paz'),(132,175,'America/Lima'),(133,234,'America/Los_Angeles'),(134,212,'America/Lower_Princes'),(135,31,'America/Maceio'),(136,166,'America/Managua'),(137,31,'America/Manaus'),(138,142,'America/Marigot'),(139,151,'America/Martinique'),(140,158,'America/Matamoros'),(141,158,'America/Mazatlan'),(142,234,'America/Menominee'),(143,158,'America/Merida'),(144,234,'America/Metlakatla'),(145,158,'America/Mexico_City'),(146,181,'America/Miquelon'),(147,38,'America/Moncton'),(148,158,'America/Monterrey'),(149,235,'America/Montevideo'),(150,38,'America/Montreal'),(151,153,'America/Montserrat'),(152,32,'America/Nassau'),(153,234,'America/New_York'),(154,38,'America/Nipigon'),(155,234,'America/Nome'),(156,31,'America/Noronha'),(157,234,'America/North_Dakota/Beulah'),(158,234,'America/North_Dakota/Center'),(159,234,'America/North_Dakota/New_Salem'),(160,158,'America/Ojinaga'),(161,174,'America/Panama'),(162,38,'America/Pangnirtung'),(163,209,'America/Paramaribo'),(164,234,'America/Phoenix'),(165,99,'America/Port-au-Prince'),(166,227,'America/Port_of_Spain'),(167,31,'America/Porto_Velho'),(168,183,'America/Puerto_Rico'),(169,38,'America/Rainy_River'),(170,38,'America/Rankin_Inlet'),(171,31,'America/Recife'),(172,38,'America/Regina'),(173,38,'America/Resolute'),(174,31,'America/Rio_Branco'),(175,158,'America/Santa_Isabel'),(176,31,'America/Santarem'),(177,46,'America/Santiago'),(178,61,'America/Santo_Domingo'),(179,31,'America/Sao_Paulo'),(180,84,'America/Scoresbysund'),(181,234,'America/Shiprock'),(182,234,'America/Sitka'),(183,26,'America/St_Barthelemy'),(184,38,'America/St_Johns'),(185,120,'America/St_Kitts'),(186,129,'America/St_Lucia'),(187,241,'America/St_Thomas'),(188,238,'America/St_Vincent'),(189,38,'America/Swift_Current'),(190,97,'America/Tegucigalpa'),(191,84,'America/Thule'),(192,38,'America/Thunder_Bay'),(193,158,'America/Tijuana'),(194,38,'America/Toronto'),(195,240,'America/Tortola'),(196,38,'America/Vancouver'),(197,38,'America/Whitehorse'),(198,38,'America/Winnipeg'),(199,234,'America/Yakutat'),(200,38,'America/Yellowknife'),(201,9,'Antarctica/Casey'),(202,9,'Antarctica/Davis'),(203,9,'Antarctica/DumontDUrville'),(204,13,'Antarctica/Macquarie'),(205,9,'Antarctica/Mawson'),(206,9,'Antarctica/McMurdo'),(207,9,'Antarctica/Palmer'),(208,9,'Antarctica/Rothera'),(209,9,'Antarctica/South_Pole'),(210,9,'Antarctica/Syowa'),(211,9,'Antarctica/Vostok'),(212,203,'Arctic/Longyearbyen'),(213,246,'Asia/Aden'),(214,126,'Asia/Almaty'),(215,113,'Asia/Amman'),(216,192,'Asia/Anadyr'),(217,126,'Asia/Aqtau'),(218,126,'Asia/Aqtobe'),(219,223,'Asia/Ashgabat'),(220,107,'Asia/Baghdad'),(221,23,'Asia/Bahrain'),(222,16,'Asia/Baku'),(223,219,'Asia/Bangkok'),(224,128,'Asia/Beirut'),(225,116,'Asia/Bishkek'),(226,28,'Asia/Brunei'),(227,148,'Asia/Choibalsan'),(228,48,'Asia/Chongqing'),(229,131,'Asia/Colombo'),(230,213,'Asia/Damascus'),(231,19,'Asia/Dhaka'),(232,222,'Asia/Dili'),(233,2,'Asia/Dubai'),(234,220,'Asia/Dushanbe'),(235,184,'Asia/Gaza'),(236,48,'Asia/Harbin'),(237,184,'Asia/Hebron'),(238,242,'Asia/Ho_Chi_Minh'),(239,95,'Asia/Hong_Kong'),(240,148,'Asia/Hovd'),(241,192,'Asia/Irkutsk'),(242,101,'Asia/Jakarta'),(243,101,'Asia/Jayapura'),(244,103,'Asia/Jerusalem'),(245,3,'Asia/Kabul'),(246,192,'Asia/Kamchatka'),(247,179,'Asia/Karachi'),(248,48,'Asia/Kashgar'),(249,169,'Asia/Kathmandu'),(250,192,'Asia/Khandyga'),(251,105,'Asia/Kolkata'),(252,192,'Asia/Krasnoyarsk'),(253,159,'Asia/Kuala_Lumpur'),(254,159,'Asia/Kuching'),(255,124,'Asia/Kuwait'),(256,149,'Asia/Macau'),(257,192,'Asia/Magadan'),(258,101,'Asia/Makassar'),(259,178,'Asia/Manila'),(260,173,'Asia/Muscat'),(261,55,'Asia/Nicosia'),(262,192,'Asia/Novokuznetsk'),(263,192,'Asia/Novosibirsk'),(264,192,'Asia/Omsk'),(265,126,'Asia/Oral'),(266,117,'Asia/Phnom_Penh'),(267,101,'Asia/Pontianak'),(268,121,'Asia/Pyongyang'),(269,188,'Asia/Qatar'),(270,126,'Asia/Qyzylorda'),(271,147,'Asia/Rangoon'),(272,194,'Asia/Riyadh'),(273,192,'Asia/Sakhalin'),(274,236,'Asia/Samarkand'),(275,122,'Asia/Seoul'),(276,48,'Asia/Shanghai'),(277,200,'Asia/Singapore'),(278,229,'Asia/Taipei'),(279,236,'Asia/Tashkent'),(280,79,'Asia/Tbilisi'),(281,108,'Asia/Tehran'),(282,33,'Asia/Thimphu'),(283,114,'Asia/Tokyo'),(284,148,'Asia/Ulaanbaatar'),(285,48,'Asia/Urumqi'),(286,192,'Asia/Ust-Nera'),(287,127,'Asia/Vientiane'),(288,192,'Asia/Vladivostok'),(289,192,'Asia/Yakutsk'),(290,192,'Asia/Yekaterinburg'),(291,7,'Asia/Yerevan'),(292,185,'Atlantic/Azores'),(293,27,'Atlantic/Bermuda'),(294,68,'Atlantic/Canary'),(295,52,'Atlantic/Cape_Verde'),(296,74,'Atlantic/Faroe'),(297,185,'Atlantic/Madeira'),(298,109,'Atlantic/Reykjavik'),(299,90,'Atlantic/South_Georgia'),(300,201,'Atlantic/St_Helena'),(301,72,'Atlantic/Stanley'),(302,13,'Australia/Adelaide'),(303,13,'Australia/Brisbane'),(304,13,'Australia/Broken_Hill'),(305,13,'Australia/Currie'),(306,13,'Australia/Darwin'),(307,13,'Australia/Eucla'),(308,13,'Australia/Hobart'),(309,13,'Australia/Lindeman'),(310,13,'Australia/Lord_Howe'),(311,13,'Australia/Melbourne'),(312,13,'Australia/Perth'),(313,13,'Australia/Sydney'),(314,167,'Europe/Amsterdam'),(315,1,'Europe/Andorra'),(316,89,'Europe/Athens'),(317,191,'Europe/Belgrade'),(318,57,'Europe/Berlin'),(319,204,'Europe/Bratislava'),(320,20,'Europe/Brussels'),(321,190,'Europe/Bucharest'),(322,100,'Europe/Budapest'),(323,57,'Europe/Busingen'),(324,140,'Europe/Chisinau'),(325,59,'Europe/Copenhagen'),(326,102,'Europe/Dublin'),(327,83,'Europe/Gibraltar'),(328,81,'Europe/Guernsey'),(329,70,'Europe/Helsinki'),(330,104,'Europe/Isle_of_Man'),(331,226,'Europe/Istanbul'),(332,111,'Europe/Jersey'),(333,192,'Europe/Kaliningrad'),(334,231,'Europe/Kiev'),(335,185,'Europe/Lisbon'),(336,202,'Europe/Ljubljana'),(337,77,'Europe/London'),(338,135,'Europe/Luxembourg'),(339,68,'Europe/Madrid'),(340,154,'Europe/Malta'),(341,15,'Europe/Mariehamn'),(342,36,'Europe/Minsk'),(343,139,'Europe/Monaco'),(344,192,'Europe/Moscow'),(345,168,'Europe/Oslo'),(346,75,'Europe/Paris'),(347,141,'Europe/Podgorica'),(348,56,'Europe/Prague'),(349,136,'Europe/Riga'),(350,110,'Europe/Rome'),(351,192,'Europe/Samara'),(352,206,'Europe/San_Marino'),(353,17,'Europe/Sarajevo'),(354,231,'Europe/Simferopol'),(355,145,'Europe/Skopje'),(356,22,'Europe/Sofia'),(357,199,'Europe/Stockholm'),(358,64,'Europe/Tallinn'),(359,6,'Europe/Tirane'),(360,231,'Europe/Uzhgorod'),(361,130,'Europe/Vaduz'),(362,237,'Europe/Vatican'),(363,12,'Europe/Vienna'),(364,134,'Europe/Vilnius'),(365,192,'Europe/Volgograd'),(366,180,'Europe/Warsaw'),(367,98,'Europe/Zagreb'),(368,231,'Europe/Zaporozhye'),(369,43,'Europe/Zurich'),(370,143,'Indian/Antananarivo'),(371,106,'Indian/Chagos'),(372,54,'Indian/Christmas'),(373,39,'Indian/Cocos'),(374,119,'Indian/Comoro'),(375,217,'Indian/Kerguelen'),(376,196,'Indian/Mahe'),(377,156,'Indian/Maldives'),(378,155,'Indian/Mauritius'),(379,247,'Indian/Mayotte'),(380,189,'Indian/Reunion'),(381,245,'Pacific/Apia'),(382,172,'Pacific/Auckland'),(383,172,'Pacific/Chatham'),(384,73,'Pacific/Chuuk'),(385,46,'Pacific/Easter'),(386,243,'Pacific/Efate'),(387,118,'Pacific/Enderbury'),(388,221,'Pacific/Fakaofo'),(389,71,'Pacific/Fiji'),(390,228,'Pacific/Funafuti'),(391,63,'Pacific/Galapagos'),(392,176,'Pacific/Gambier'),(393,195,'Pacific/Guadalcanal'),(394,92,'Pacific/Guam'),(395,234,'Pacific/Honolulu'),(396,233,'Pacific/Johnston'),(397,118,'Pacific/Kiritimati'),(398,73,'Pacific/Kosrae'),(399,144,'Pacific/Kwajalein'),(400,144,'Pacific/Majuro'),(401,176,'Pacific/Marquesas'),(402,233,'Pacific/Midway'),(403,170,'Pacific/Nauru'),(404,171,'Pacific/Niue'),(405,164,'Pacific/Norfolk'),(406,162,'Pacific/Noumea'),(407,11,'Pacific/Pago_Pago'),(408,186,'Pacific/Palau'),(409,182,'Pacific/Pitcairn'),(410,73,'Pacific/Pohnpei'),(411,177,'Pacific/Port_Moresby'),(412,45,'Pacific/Rarotonga'),(413,150,'Pacific/Saipan'),(414,176,'Pacific/Tahiti'),(415,118,'Pacific/Tarawa'),(416,225,'Pacific/Tongatapu'),(417,233,'Pacific/Wake'),(418,244,'Pacific/Wallis');
/*!40000 ALTER TABLE `geo_timezone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups_children`
--

DROP TABLE IF EXISTS `groups_children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_children` (
  `groups_id` int(11) NOT NULL,
  `children_id` int(11) NOT NULL,
  PRIMARY KEY (`groups_id`,`children_id`),
  KEY `IDX_1A854B65F373DCF` (`groups_id`),
  KEY `IDX_1A854B653D3D2749` (`children_id`),
  CONSTRAINT `FK_1A854B653D3D2749` FOREIGN KEY (`children_id`) REFERENCES `Children` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1A854B65F373DCF` FOREIGN KEY (`groups_id`) REFERENCES `Groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups_children`
--

LOCK TABLES `groups_children` WRITE;
/*!40000 ALTER TABLE `groups_children` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups_children` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userextend_groups`
--

DROP TABLE IF EXISTS `userextend_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userextend_groups` (
  `userextend_id` int(11) NOT NULL,
  `groups_id` int(11) NOT NULL,
  PRIMARY KEY (`userextend_id`,`groups_id`),
  KEY `IDX_C97EDDD1A5D248B` (`userextend_id`),
  KEY `IDX_C97EDDD1F373DCF` (`groups_id`),
  CONSTRAINT `FK_C97EDDD1F373DCF` FOREIGN KEY (`groups_id`) REFERENCES `Groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C97EDDD1A5D248B` FOREIGN KEY (`userextend_id`) REFERENCES `UserExtend` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userextend_groups`
--

LOCK TABLES `userextend_groups` WRITE;
/*!40000 ALTER TABLE `userextend_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `userextend_groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-03-13 10:31:23
