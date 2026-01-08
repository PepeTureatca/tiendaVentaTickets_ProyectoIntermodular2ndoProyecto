-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: user_db
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.22.04.2

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_name` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `admin_lived` varchar(100) DEFAULT NULL,
  `admin_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_email` (`admin_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin@musiverse.com','e10adc3949ba59abbe56e057f20f883e','Administrador','2025-12-26','Valencia',NULL);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chosenseats`
--

DROP TABLE IF EXISTS `chosenseats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chosenseats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `concertid` int NOT NULL,
  `seatid` int NOT NULL,
  `seatnames` varchar(255) NOT NULL,
  `status` enum('Available','Taken') DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chosenseats`
--

LOCK TABLES `chosenseats` WRITE;
/*!40000 ALTER TABLE `chosenseats` DISABLE KEYS */;
/*!40000 ALTER TABLE `chosenseats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seats` (
  `seatid` int NOT NULL AUTO_INCREMENT,
  `seatname` varchar(100) NOT NULL,
  `section` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Available','Taken') DEFAULT 'Available',
  `concertid` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seatid`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
INSERT INTO `seats` VALUES (1,'A1','VIP',2599.00,'Available',1,'2026-01-06 14:11:41'),(2,'A2','VIP',2599.00,'Available',1,'2026-01-06 14:11:41'),(3,'A3','VIP',2599.00,'Taken',1,'2026-01-06 14:11:41'),(4,'A4','VIP',2599.00,'Available',1,'2026-01-06 14:11:41'),(5,'A5','VIP',2599.00,'Taken',1,'2026-01-06 14:11:41'),(6,'B1','UB',5.00,'Available',1,'2026-01-06 14:11:41'),(7,'B2','UB',5.00,'Available',1,'2026-01-06 14:11:41'),(8,'B3','UB',5.00,'Taken',1,'2026-01-06 14:11:41'),(9,'B4','UB',5.00,'Available',1,'2026-01-06 14:11:41'),(10,'B5','UB',5.00,'Taken',1,'2026-01-06 14:11:41'),(11,'C1','LB',123.00,'Available',1,'2026-01-06 14:11:41'),(12,'C2','LB',123.00,'Available',1,'2026-01-06 14:11:41'),(13,'C3','LB',123.00,'Available',1,'2026-01-06 14:11:41'),(14,'C4','LB',123.00,'Taken',1,'2026-01-06 14:11:41'),(15,'C5','LB',123.00,'Available',1,'2026-01-06 14:11:41'),(16,'D1','GEN AD',5.00,'Available',1,'2026-01-06 14:11:41'),(17,'D2','GEN AD',5.00,'Available',1,'2026-01-06 14:11:41'),(18,'D3','GEN AD',5.00,'Taken',1,'2026-01-06 14:11:41'),(19,'D4','GEN AD',5.00,'Available',1,'2026-01-06 14:11:41'),(20,'D5','GEN AD',5.00,'Available',1,'2026-01-06 14:11:41'),(21,'A1','VIP',2599.00,'Available',9,'2026-01-06 14:12:55'),(22,'A2','VIP',2599.00,'Available',9,'2026-01-06 14:12:55'),(23,'A3','VIP',2599.00,'Taken',9,'2026-01-06 14:12:55'),(24,'A4','VIP',2599.00,'Available',9,'2026-01-06 14:12:55'),(25,'A5','VIP',2599.00,'Taken',9,'2026-01-06 14:12:55'),(26,'B1','UB',5.00,'Available',9,'2026-01-06 14:12:55'),(27,'B2','UB',5.00,'Available',9,'2026-01-06 14:12:55'),(28,'B3','UB',5.00,'Taken',9,'2026-01-06 14:12:55'),(29,'B4','UB',5.00,'Available',9,'2026-01-06 14:12:55'),(30,'B5','UB',5.00,'Taken',9,'2026-01-06 14:12:55'),(31,'C1','LB',123.00,'Available',9,'2026-01-06 14:12:55'),(32,'C2','LB',123.00,'Available',9,'2026-01-06 14:12:55'),(33,'C3','LB',123.00,'Available',9,'2026-01-06 14:12:55'),(34,'C4','LB',123.00,'Taken',9,'2026-01-06 14:12:55'),(35,'C5','LB',123.00,'Available',9,'2026-01-06 14:12:55'),(36,'D1','GEN AD',5.00,'Available',9,'2026-01-06 14:12:55'),(37,'D2','GEN AD',5.00,'Available',9,'2026-01-06 14:12:55'),(38,'D3','GEN AD',5.00,'Taken',9,'2026-01-06 14:12:55'),(39,'D4','GEN AD',5.00,'Available',9,'2026-01-06 14:12:55'),(40,'D5','GEN AD',5.00,'Available',9,'2026-01-06 14:12:55');
/*!40000 ALTER TABLE `seats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblbuyer`
--

DROP TABLE IF EXISTS `tblbuyer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblbuyer` (
  `buyer_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `concert_name` varchar(100) DEFAULT NULL,
  `payment_price` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblbuyer`
--

LOCK TABLES `tblbuyer` WRITE;
/*!40000 ALTER TABLE `tblbuyer` DISABLE KEYS */;
/*!40000 ALTER TABLE `tblbuyer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblconcert`
--

DROP TABLE IF EXISTS `tblconcert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblconcert` (
  `concert_id` int NOT NULL AUTO_INCREMENT,
  `concert_name` varchar(100) DEFAULT NULL,
  `concert_date` date DEFAULT NULL,
  `concert_location` varchar(100) DEFAULT NULL,
  `concert_artist` varchar(100) DEFAULT NULL,
  `concert_desc` text,
  `concert_contact` varchar(100) DEFAULT NULL,
  `concert_genre` varchar(50) DEFAULT NULL,
  `concert_venue` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `concert_time` time DEFAULT NULL,
  `ub_price` decimal(10,2) NOT NULL,
  `lb_price` decimal(10,2) NOT NULL,
  `vip_price` decimal(10,2) NOT NULL,
  `genad_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`concert_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblconcert`
--

LOCK TABLES `tblconcert` WRITE;
/*!40000 ALTER TABLE `tblconcert` DISABLE KEYS */;
INSERT INTO `tblconcert` VALUES (9,'TEST','2027-12-01',NULL,'DJ PEPE','DJ PEPE','djpepe@gmail.com','DJ PEPE','DJ PEPE','images.jpeg','12:02:00',5.00,123.00,2599.00,5.00);
/*!40000 ALTER TABLE `tblconcert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblpayment`
--

DROP TABLE IF EXISTS `tblpayment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblpayment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` int NOT NULL,
  `card_number` varchar(19) NOT NULL,
  `cardholder` varchar(100) NOT NULL,
  `monthexp` varchar(2) NOT NULL,
  `yearexp` varchar(4) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `cardtype` varchar(50) NOT NULL,
  `pin` varchar(8) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblpayment`
--

LOCK TABLES `tblpayment` WRITE;
/*!40000 ALTER TABLE `tblpayment` DISABLE KEYS */;
INSERT INTO `tblpayment` VALUES (1,3,'1234567890123456','asdasd','10','2033','123','Mastercard','49225418','Vinculada','2025-12-07 14:53:12'),(2,4,'1234567890123456','Pepe TUreatca','09','2028','123','Mastercard','18451142','Vinculada','2025-12-08 15:12:38');
/*!40000 ALTER TABLE `tblpayment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_form`
--

DROP TABLE IF EXISTS `user_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_form` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phonenum` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `accdate` datetime DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_form`
--

LOCK TABLES `user_form` WRITE;
/*!40000 ALTER TABLE `user_form` DISABLE KEYS */;
INSERT INTO `user_form` VALUES (1,'diego','diego@gmail.com','25f9e794323b453885f5181f1b624d0b','diego','1992-10-27','123456789','aaa','2025-11-24 18:09:54',''),(2,'jose','jose@gmail.com','4e4e169124f6c5ff392ecf1336f9321b','jose','1979-11-14','12345678901','jose1111111111111','2025-11-24 18:15:59','logo.png'),(3,'Pepe Tureatca','pepemaladin01@gmail.com','cbfa2d3cbbc4ca0e23073233b5e78581','Pepe Tureatca',NULL,'123456789','calle escalante',NULL,'https://lh3.googleusercontent.com/a/ACg8ocLuqtbybPOQXWxQucJoGUDFyi0xMh1E2TThsCF0yqLsQO1yvLYW=s96-c'),(4,'pepetur','pepetur@gmail.com','9959a0b5db110c63b853d3a80f6458e9','Pepe TUr','2005-07-18','666666666','escalante 1235','2025-12-08 16:11:58',NULL);
/*!40000 ALTER TABLE `user_form` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-06 15:43:05
