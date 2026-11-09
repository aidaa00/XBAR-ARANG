-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: web_arang
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `web_arang`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `web_arang` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `web_arang`;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `actor_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Admin',
  `action` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_ago` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Just now',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,'Admin Eve','Admin','Updated the product\'s catalogue','24m ago','2026-10-07 17:12:48'),(2,'Visitor','Visitor','Clicked Request Quotation','1h ago','2026-10-07 17:12:48'),(3,'Administrator Aidaa','Admin','Deleted product \'Tamarind Charcoal\'','Just now','2026-10-08 04:43:45'),(4,'Administrator Aidaa','Admin','Updated product \'Mix Charcoal\'','Just now','2026-10-08 04:43:58'),(5,'Administrator Aidaa','Admin','Updated product \'Mix Charcoal\'','Just now','2026-10-08 04:44:11'),(6,'Administrator Aidaa','Admin','Created product \'lllllll\'','Just now','2026-10-08 04:45:28'),(7,'Administrator Aidaa','Admin','Updated admin profile settings','Just now','2026-10-08 04:45:52'),(8,'Administrator Eve','Admin','Updated admin profile settings','Just now','2026-10-08 04:50:35'),(9,'eve','Admin','Updated website presentation and content','Just now','2026-10-08 06:39:15'),(10,'eve','Admin','Updated website presentation and content','Just now','2026-10-08 06:40:06'),(11,'eve','Admin','Updated website presentation and content','Just now','2026-10-08 06:41:54'),(12,'eve','Admin','Updated website presentation and content','Just now','2026-10-08 07:08:23'),(13,'eve','Admin','Updated website presentation and content','Just now','2026-10-08 07:46:18');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
INSERT INTO `galleries` VALUES (1,'Timber Processing Site','galeri1.jpg','assets/images/galeri1.jpg',1,'2026-10-07 17:12:48'),(2,'Warehouse Charcoal Sorting','galeri2.jpg','assets/images/galeri2.jpg',2,'2026-10-07 17:12:48'),(3,'Weight Scale & Quality Check','galeri3.jpg','assets/images/galeri3.jpg',3,'2026-10-07 17:12:48'),(4,'Sample Firewood Sizing','galeri4.jpg','assets/images/galeri4.jpg',4,'2026-10-07 17:12:48'),(6,'eskrim','gallery_1791441555_04cca4.jpeg','uploads/gallery_1791441555_04cca4.jpeg',5,'2026-10-08 06:39:15');
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history_milestones`
--

DROP TABLE IF EXISTS `history_milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_milestones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `year_range` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history_milestones`
--

LOCK TABLES `history_milestones` WRITE;
/*!40000 ALTER TABLE `history_milestones` DISABLE KEYS */;
INSERT INTO `history_milestones` VALUES (18,'2017 - 2018','As the manufacturer, we built three kilns at a cost of 9 million per unit, with specifications of 9 meters in length and 2 meters in width. We supply our output to local middlemen in Bondowoso.',1,'2026-10-08 07:46:18'),(19,'2019 - 2022','We partnered with an external investor to construct 25 kilns at a production site in Sukowono Village, Pujer District. With these 25 kilns, we are able to ship an output of 4-5 containers per month. We export the charcoal to buyers in the Middle East.',2,'2026-10-08 07:46:18'),(20,'2022 - 2026','We have transformed into charcoal aggregators, partnering with farmers and kiln owners in Bondowoso to help market their charcoal production.',3,'2026-10-08 07:46:18'),(21,'2026 - 2028','semoga sukses',4,'2026-10-08 07:46:18');
/*!40000 ALTER TABLE `history_milestones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Charcoal',
  `recent_price` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_stock` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw_material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `advantages` text COLLATE utf8mb4_unicode_ci,
  `application` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_tons` int DEFAULT '0',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (2,'Mix Charcoal','mix-charcoal','Mix Charcoal','USD 330','50 Tons','Crafted from a blend of premium hardwoods, such as Kukun, Klampok, Kenitu, Mimbo, and Asam???this mixed wood charcoal is designed for barbecues, grilling, and commercial kitchens. It delivers a clean, spark-free burn with minimal smoke, low ash residue, and a subtle, non-intrusive scent.','Blend of hardwoods (Kukun, Klampok, Kenitu, Mimbo, Asam)','Spark-free burn, minimal smoke, low ash residue, subtle scent.','Barbecues, grilling, and commercial kitchens.','uploads/prod_1791434651_1de52da6.png',70,2,'2026-10-07 17:12:48','2026-10-08 04:44:11'),(3,'Firewood','firewood','Firewood','USD 205','10 Tons','Offered in precise lengths ranging from 36 cm to 47 cm, this natural firewood features a steady supply of hard timber like Lamtoro, Klampok, Kenitu, Mimbo, and Mahogany (or custom wood species upon request). It is ideal for residential heating, commercial cooking, outdoor grilling, and heavy industrial fuel applications.','Lamtoro, Klampok, Kenitu, Mimbo, Mahogany','Uniform cut lengths (36 to 47 cm), steady long-lasting heat, sustainably gathered hardwood.','Residential heating, commercial cooking, outdoor grilling, industrial fuel applications.','assets/images/firewood.jpg',50,3,'2026-10-07 17:12:48','2026-10-07 17:12:48'),(4,'Charcoal Powder','charcoal-powder','Charcoal Powder','USD 33,57','1 Kg','Derived from blended timber sources, this versatile charcoal powder (or charcoal fines) can be tailored to buyer specifications regarding wood type. It serves as a highly effective medium for livestock care, soil enrichment, water filtration, and chemical neutralization while maintaining a reliable, high-volume supply.','Blended timber sources','Ultra-fine mesh, high carbon density, superior chemical adsorption capacity.','Livestock feed additive, soil enrichment, water filtration, industrial chemical neutralization.','assets/images/charcoal-powder.jpg',65,4,'2026-10-07 17:12:48','2026-10-07 17:12:48'),(5,'Industrial Charcoal','industrial-charcoal','Industrial Charcoal','USD 255','10 Tons','Specifically tailored for factory and industrial facility usage, this heavy-duty charcoal is produced from Black Klampis and Lamtoro woods. It burns clean with low smoke, minimal ash production, and a non-pungent scent, while providing the energetic spark profile required for industrial heating systems.','Black Klampis and Lamtoro woods','High thermal output, prolonged heat retention, clean burn with minimal ash, energetic spark profile.','Factory and industrial facility boilers, metal forging, high-temp heating systems.','assets/images/industrial-charcoal.jpg',20,5,'2026-10-07 17:12:48','2026-10-07 17:12:48'),(6,'lllllll','lllllll-1791434728','Charcoal','USD 330','60','kayu bakar','kayu bakar',NULL,NULL,'uploads/prod_1791434728_1299e96e.png',0,0,'2026-10-08 04:45:28','2026-10-08 04:45:28');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotations`
--

DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'New',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotations`
--

LOCK TABLES `quotations` WRITE;
/*!40000 ALTER TABLE `quotations` DISABLE KEYS */;
INSERT INTO `quotations` VALUES (1,'Johnathan Miller','j.miller@bbqgrill-global.com','+1 415 882 9901','Miller Grill & BBQ USA','Tamarind Charcoal','2 Containers (40 Tons)','Inquiring for monthly shipment to Port of Los Angeles.','New','2026-10-07 17:12:48');
/*!40000 ALTER TABLE `quotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/images/admin-avatar.jpg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'aidaa','aidaa@gmail.com','$2y$10$0jtDZhYwzk/s9qWpsusKlO3RDJtQb2tDYo4vwpttERhux.gNC8gx6','Administrator Eve','admin','uploads/avatar_1791435035_2ae097e7.png','2026-10-07 17:12:48','2026-10-08 04:50:35'),(2,'admineve','eve@xbar.com','$2y$10$0jtDZhYwzk/s9qWpsusKlO3RDJtQb2tDYo4vwpttERhux.gNC8gx6','Admin Eve','admin','assets/images/admin-avatar.jpg','2026-10-07 17:12:48','2026-10-07 17:12:48'),(3,'eve','eve01@gmail.com','$2y$10$6fd2VrxN9b9ce6U5G2kzG.DwxtgHr7ATG3K.CnW.uxsFSL1xYfk/W','eve','admin','assets/images/admin-avatar.jpg','2026-10-08 04:51:11','2026-10-08 04:51:11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `website_content`
--

DROP TABLE IF EXISTS `website_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_key` (`content_key`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `website_content`
--

LOCK TABLES `website_content` WRITE;
/*!40000 ALTER TABLE `website_content` DISABLE KEYS */;
INSERT INTO `website_content` VALUES (1,'website_title','Natural Energy, Global Quality ror','2026-10-08 07:08:23'),(2,'hero_short_desc','And Firewood For Global Culinary (BBQ/Grill), Industrial, And Agricultural Needs. Ready To Consistently Supply Both Local And International Markets.','2026-10-08 07:04:25'),(3,'hero_tagline','And Firewood For Global Culinary (BBQ/Grill), Industrial, And Agricultural Needs. Ready To Consistently Supply Both Local And International Markets.','2026-10-08 07:08:23'),(4,'hero_image','uploads/hero_1791441606.jpeg','2026-10-08 06:40:06'),(5,'about_desc','X BAR is an Indonesia-based supplier of quality charcoal and wood-based products, serving both local and international markets. We specialize in Tamarind Charcoal, Mixed Charcoal, Firewood, and Charcoal Powder, carefully selected and processed to deliver consistent quality and reliable performance. Our commitment is simple: Quality Products, Reliable Service, and Long-Term Partnerships. From Indonesia to the world, X BAR is committed to bringing trusted Indonesian products to global markets while continuously improving our quality, service, and responsible use of natural resources.','2026-10-08 07:04:25'),(6,'history_desc','We have transformed into charcoal aggregators, partnering with farmers and kiln owners in Bondowoso to help market their charcoal production.','2026-10-08 07:46:18'),(7,'vision','To become a trusted, world-class Indonesian company providing high-quality wood- and charcoal-based energy products.','2026-10-08 07:04:25'),(8,'mission','1. Provide consistently high-quality charcoal and wood products for the international market.\r\n2. Build long-term relationships based on trust, quality, and excellent service.\r\n3. Develop products sustainably through the responsible use of natural resources.\r\n4. Bring the quality and excellence of Indonesian products to the global market.','2026-10-08 07:08:23'),(9,'contact_address','JL. PB. Sudirman II/26, Bondowoso, 68213','2026-10-07 17:12:48'),(10,'contact_phone','081333775663','2026-10-07 17:12:48'),(11,'contact_email','cvsudut@gmail.com','2026-10-07 17:12:48'),(12,'map_image','assets/images/map-location.jpg','2026-10-07 17:12:48'),(44,'about_year','','2026-10-08 07:46:18'),(45,'history_year_1','2017 - 2018','2026-10-08 07:46:18'),(46,'history_text_1','As the manufacturer, we built three kilns at a cost of 9 million per unit, with specifications of 9 meters in length and 2 meters in width. We supply our output to local middlemen in Bondowoso.','2026-10-08 07:46:18'),(47,'history_year_2','2019 - 2022','2026-10-08 07:46:18'),(48,'history_text_2','We partnered with an external investor to construct 25 kilns at a production site in Sukowono Village, Pujer District. With these 25 kilns, we are able to ship an output of 4-5 containers per month. We export the charcoal to buyers in the Middle East.','2026-10-08 07:46:18'),(49,'history_year_3','2022 - 2026','2026-10-08 07:46:18'),(50,'vision_mission_year','2026 - 2030','2026-10-08 07:04:25');
/*!40000 ALTER TABLE `website_content` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-08 14:52:22


--
-- Table structure for table `visitor_logs`
--

DROP TABLE IF EXISTS `visitor_logs`;
CREATE TABLE `visitor_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `page_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referrer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`),
  KEY `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
