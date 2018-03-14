
CREATE DATABASE  IF NOT EXISTS `router` /*!40100 DEFAULT CHARACTER SET latin1 */;



DROP USER IF EXISTS 'router'@'localhost';
FLUSH PRIVILEGES;
CREATE USER 'router'@'localhost' IDENTIFIED BY 'router';
GRANT ALL PRIVILEGES ON router.* TO 'router'@'localhost';
FLUSH PRIVILEGES;


USE `router`;

-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 10.0.1.1    Database: router
-- ------------------------------------------------------
-- Server version 5.7.13-0ubuntu0.16.04.2

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
-- Table structure for table `dhcps`
--

DROP TABLE IF EXISTS `dhcps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dhcps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hostname` varchar(45) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `physical_address` varchar(45) NOT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'active',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `ip_address_UNIQUE` (`ip_address`),
  UNIQUE KEY `hostname_UNIQUE` (`hostname`),
  UNIQUE KEY `physical_address_UNIQUE` (`physical_address`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dhcps`
--

LOCK TABLES `dhcps` WRITE;
/*!40000 ALTER TABLE `dhcps` DISABLE KEYS */;
/*!40000 ALTER TABLE `dhcps` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `title` varchar(20) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `url` varchar(45) DEFAULT NULL,
  `slug` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,0,'Home',1,2,'home','home','internal','enabled',NULL,NULL),(2,0,'Hosts',3,6,'dhcp','host','internal','enabled',NULL,NULL),(3,0,'Active Hosts',4,5,'dns/home/1','dns','internal','enabled',NULL,NULL),(4,0,'Iptables Config',7,8,'iptables','iptables','internal','enabled',NULL,NULL),(5,0,'Router Settings',9,10,'settings','settings','internal','enabled',NULL,NULL),(6,0,'Proxy Server',11,12,'proxy','proxy','internal','enabled',NULL,NULL),(7,0,'Menus',13,14,'menus','menus','internal','enabled',NULL,NULL),(8,0,'Logs',15,16,'logs','logs','internal','enabled',NULL,NULL),(9,0,'root',0,19,NULL,NULL,'internal','disabled',NULL,NULL),(10,0,'na',17,18,NULL,NULL,'internal','disabled',NULL,NULL);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proxy_sites`
--

DROP TABLE IF EXISTS `proxy_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxy_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `port` varchar(5) NOT NULL DEFAULT '80',
  `proxy_url` varchar(255) NOT NULL,
  `priority` varchar(45) NOT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'disabled',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxy_sites`
--

LOCK TABLES `proxy_sites` WRITE;
/*!40000 ALTER TABLE `proxy_sites` DISABLE KEYS */;
/*!40000 ALTER TABLE `proxy_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `key_UNIQUE` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'external_iface','eth1','test','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'internal_iface','eth0','ex.:eth1,br0','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'external_iface_link','','provide a value to calculate your bandwith rate','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'internal_iface_link','1000','provide a value to calculate your bandwith rate','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'disk01','/','primary partition','0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,'internal_iface_ip','10.0.1.1','Lan Interface Ip','0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,'internal_iface_hostname','router','local hostname','0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,'internal_iface_domain','betasyntax.com','domain name','0000-00-00 00:00:00','0000-00-00 00:00:00'),(15,'show_branding','false','whether or not to show the branding.true to show \'false\' to hide','0000-00-00 00:00:00','0000-00-00 00:00:00'),(16,'apache_sites_available','/etc/apache2/sites-available/','test','0000-00-00 00:00:00','0000-00-00 00:00:00'),(17,'apache_sites_enabled','/etc/apache2/sites-enabled/','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(18,'setup_first_run','0','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(19,'external_iface_ip','192.0.246.252',NULL,NULL,NULL),(20,'external_iface_gateway',NULL,NULL,NULL,NULL),(21,'external_iface_netmask',NULL,NULL,NULL,NULL),(22,'external_iface_network',NULL,NULL,NULL,NULL),(23,'external_iface_broadcast',NULL,NULL,NULL,NULL),(24,'external_iface_type','dhcp',NULL,NULL,NULL),(25,'internal_iface_type','static',NULL,NULL,NULL),(26,'internal_iface_broadcast','10.0.1.255',NULL,NULL,NULL),(27,'internal_iface_network','10.0.1.0',NULL,NULL,NULL),(28,'internal_iface_netmask','255.255.255.0',NULL,NULL,NULL);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(45) NOT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (45,'admin@admin.com','$2y$10$tgIenSu5WNCkQ4kJQC1rgus1cueQ7pF6i4lfLxuS8IkUhv/.UW49C','enabled','','2016-07-22 02:46:15','2016-07-17 20:00:18');
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

-- Dump completed on 2016-07-26  3:31:05
