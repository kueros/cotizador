/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.5.2-MariaDB, for osx10.20 (arm64)
--
-- Host: localhost    Database: yafo_plaft
-- ------------------------------------------------------
-- Server version	11.5.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_accesos`
--

DROP TABLE IF EXISTS `logs_accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_accesos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_accesos_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_accesos`
--

LOCK TABLES `logs_accesos` WRITE;
/*!40000 ALTER TABLE `logs_accesos` DISABLE KEYS */;
INSERT INTO `logs_accesos` VALUES
(1,'omarliberatto@yafoconsultora.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','2024-09-25 14:52:32','2024-09-25 14:52:32'),
(2,'omarliberatto@yafoconsultora.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','2024-09-25 18:10:44','2024-09-25 18:10:44'),
(3,'omarliberatto@yafoconsultora.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','2024-09-25 18:40:05','2024-09-25 18:40:05'),
(4,'omarliberatto@yafoconsultora.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','2024-09-25 19:02:45','2024-09-25 19:02:45'),
(5,'omarliberatto@yafoconsultora.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Safari/605.1.15','2024-09-25 19:26:33','2024-09-25 19:26:33'),
(6,'omarliberatto@yafoconsultora.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','2024-09-25 19:30:01','2024-09-25 19:30:01');
/*!40000 ALTER TABLE `logs_accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_administracion`
--

DROP TABLE IF EXISTS `logs_administracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_administracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_administracion_username_index` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_administracion`
--

LOCK TABLES `logs_administracion` WRITE;
/*!40000 ALTER TABLE `logs_administracion` DISABLE KEYS */;
INSERT INTO `logs_administracion` VALUES
(1,'omar','omar creó el usuario pepe','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 15:11:10','2024-09-25 15:11:10'),
(2,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Creación de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 15:11:10','2024-09-25 15:11:10'),
(3,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 17:37:59','2024-09-25 17:37:59'),
(4,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 17:42:46','2024-09-25 17:42:46'),
(5,'omar','omar creó el usuario esteban','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 17:50:12','2024-09-25 17:50:12'),
(6,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Creación de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 17:50:12','2024-09-25 17:50:12'),
(7,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Borrado de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 17:51:10','2024-09-25 17:51:10'),
(8,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Borrado de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 18:13:40','2024-09-25 18:13:40'),
(9,'omar','omar creó el usuario marcelo','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 18:15:17','2024-09-25 18:15:17'),
(10,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Creación de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 18:15:17','2024-09-25 18:15:17'),
(11,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Borrado de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 18:28:39','2024-09-25 18:28:39'),
(12,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Borrado de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 18:28:48','2024-09-25 18:28:48'),
(13,'omar','omar creó el usuario pepe1','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 19:14:04','2024-09-25 19:14:04'),
(14,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Creación de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 19:14:04','2024-09-25 19:14:04'),
(15,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:13:23','2024-09-25 20:13:23'),
(16,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:13:35','2024-09-25 20:13:35'),
(17,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:13:54','2024-09-25 20:13:54'),
(18,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:18:47','2024-09-25 20:18:47'),
(19,'omar1','omar1 creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:21:52','2024-09-25 20:21:52'),
(20,'omar1','omar1 creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:22:06','2024-09-25 20:22:06'),
(21,'omar1','omar1 creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:32:39','2024-09-25 20:32:39'),
(22,'omar1','omar1 creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:35:25','2024-09-25 20:35:25'),
(23,'omar1','omar1 creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:36:22','2024-09-25 20:36:22'),
(24,'omar1','omar1 creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:36:52','2024-09-25 20:36:52'),
(25,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Actualización de usuario','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 20:44:57','2024-09-25 20:44:57'),
(26,'omar','omar creó el email para omarliberatto@yafoconsultora.com con el asunto: Prueba de envío de correo desde Laravel','\"127.0.0.1\"','\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"','2024-09-25 21:10:15','2024-09-25 21:10:15');
/*!40000 ALTER TABLE `logs_administracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(12,'0001_01_01_000000_create_users_table',1),
(13,'0001_01_01_000001_create_cache_table',1),
(14,'0001_01_01_000002_create_jobs_table',1),
(15,'2024_09_03_141920_create_roles_table',1),
(16,'2024_09_03_141950_create_modulos_table',1),
(17,'2024_09_03_150424_create_permisos_table',1),
(18,'2024_09_03_150439_create_permisos_x_rol_table',1),
(19,'2024_09_06_165415_create_logs_administracions_table',1),
(20,'2024_09_09_150424_create_logs_accesos_table',1),
(21,'2024_09_09_165713_create_variables_table',1),
(22,'2024_09_10_181917_create_notificaciones_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(4) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 0,
  `user_emisor_id` int(11) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `modulo_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos_x_rol`
--

DROP TABLE IF EXISTS `permisos_x_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos_x_rol` (
  `rol_id` int(11) DEFAULT NULL,
  `permiso_id` int(11) DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos_x_rol`
--

LOCK TABLES `permisos_x_rol` WRITE;
/*!40000 ALTER TABLE `permisos_x_rol` DISABLE KEYS */;
/*!40000 ALTER TABLE `permisos_x_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Administrador','2024-09-25 06:22:43','2024-09-25 06:22:43'),
(2,'usuario contable','2024-09-25 16:16:15','2024-09-25 16:16:15');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('aZ9XIDBcK5k7GMCm6AwATjymnd2B17U1ToS2veUO',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSjhlVjhsMUoyVEQwYldjQndDdGlocVJ1RGNlWm5HRlJFVzZPNGpTciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHBzOi8vcGxhZnQudGVzdC91c2VycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1727278363),
('bobv2Trx7GU0KMq5asfpYeksZgJrnPPO2y625Odh',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoicEFaaU1sNzBIa1FzQWFZRDJ4R3gwemZsY3BVSHJKbHEyTWw0bFgxOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vcGxhZnQudGVzdC91c2Vycy80L2VkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1727284016),
('t02q50WtfRIl4717n4gxYII44jirc2bTyV8wNb1g',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQVV2aVFaNXNiT3FqN1FLYXZEQ0ZaREZXVGQzRWxWaTBqUXZKY0ZwWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vcGxhZnQudGVzdC9jb25maWd1cmFjaW9uIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1727287817),
('tCAOHAmbea2AlCMZizBofqJLQw3GOd9YnNPNxUiU',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMnloblR3R1JXTmU4UlVVeVlyeFViMTBIUjRIb0dwRWdFTjYzUU80TCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwczovL3BsYWZ0LnRlc3QvdXNlcnMvNS9lZGl0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1727281628),
('TwbvdBcHy9KgH1QgKij6AULST5wydZd1m8FlXrRL',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWlZJZFU2eHVCOWlGQk9LRTJNVEZaM2JWTUVSZTY0ZEFNZkRLcGttdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHBzOi8vcGxhZnQudGVzdC91c2VycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1727281560);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `habilitado` tinyint(4) NOT NULL DEFAULT 1,
  `eliminado` tinyint(4) NOT NULL DEFAULT 0,
  `fecha_eliminado` datetime DEFAULT NULL,
  `bloqueado` tinyint(4) NOT NULL DEFAULT 0,
  `cambiar_password` tinyint(4) NOT NULL DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `intentos_login` tinyint(4) NOT NULL DEFAULT 0,
  `ultima_fecha_restablecimiento` datetime DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'omar','omar','liberatto','omarliberatto@yafoconsultora.com',1,1,0,NULL,0,0,NULL,NULL,0,NULL,NULL,'$2y$12$r1fdTzDBK7GLLPa0Ylkx5OANuyz.iGVbzrGfQl99uakGST2JnpkHC',NULL,NULL,'2024-09-25 06:31:19','2024-09-25 20:36:52'),
(2,'pepe','jose','perez','pepe@kdk.com',1,1,0,NULL,0,0,NULL,NULL,0,NULL,NULL,NULL,NULL,'2024-09-25 18:28:48','2024-09-25 15:11:10','2024-09-25 18:28:48'),
(4,'marcelo','marcelo','liberatto','marcelo@kdk.com',2,1,0,NULL,0,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-09-25 18:15:17','2024-09-25 20:35:25'),
(5,'pepe12','jose','perez','pepe1@kdk.com',2,1,0,NULL,0,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-09-25 19:14:04','2024-09-25 20:22:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variables`
--

DROP TABLE IF EXISTS `variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `nombre_menu` varchar(255) DEFAULT NULL,
  `valor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variables`
--

LOCK TABLES `variables` WRITE;
/*!40000 ALTER TABLE `variables` DISABLE KEYS */;
INSERT INTO `variables` VALUES
(3,'fecha_minima_edicion_eventos','','2019-01-15',NULL,NULL),
(7,'version','','3.7.3',NULL,NULL),
(8,'fecha_ultima_actualizacion','','2024-08-30',NULL,NULL),
(9,'reset_password_30_dias','','0',NULL,NULL),
(10,'session_time','900','',NULL,NULL),
(11,'notificaciones_locales','Notificaciones Locales','1',NULL,'2024-09-18 05:47:30'),
(12,'notificaciones_email','Notificacoines por Email','1',NULL,'2024-09-13 00:41:50'),
(13,'_notificaciones_email_aleph','Utilizar servicio de envío de email de Aleph Manager','0',NULL,'2024-09-25 21:07:09'),
(18,'integracion_azure','','0',NULL,NULL),
(19,'integracion_gmail','','0',NULL,NULL),
(20,'opav_habilitar_modo_debug','Habilitar modo debug','1',NULL,'2024-09-13 05:46:03'),
(73,'copa_background_home_custom','Utilizar imagen home default','0',NULL,'2024-09-18 05:38:50'),
(74,'background_home_custom_path','Ruta del fondo de la pantalla principal','slide0028_image054.jpg',NULL,'2024-09-16 21:44:29'),
(75,'copa_background_login_custom','Utilizar imagen login default','1',NULL,'2024-09-16 21:46:25'),
(76,'background_login_custom_path','Ruta del fondo de la pantalla de login','login-background.jpg',NULL,NULL),
(97,'opav_habilitar_ia_ciberseguridad','Habilitar IA para ciberseguridad','0',NULL,NULL),
(98,'opav_open_ai_api_key','OpenAI API Token','0',NULL,NULL),
(126,'copa_aleph_estilo_logotipo_default','Utilizar logotipo de Aleph Manager default','1',NULL,'2024-09-16 21:46:27'),
(127,'aleph_estilo_logotipo_custom_path','Ruta del logotipo personalizado','\'\'',NULL,NULL),
(129,'copa_aleph_estilo_color_barra_menu','Utilizar color de barra del menú default','1',NULL,'2024-09-16 21:46:31'),
(130,'aleph_estilo_color_titulos_menu','Color de los títulos del menú','#FFFFFF',NULL,NULL),
(131,'aleph_estilo_color_mouseover_menu','Color del mouseover del menú','#F5F5F5',NULL,NULL),
(145,'fecha_version','','2024-08-30',NULL,NULL),
(155,'copa_ocultar_leyenda','Ocultar leyenda','1',NULL,'2024-09-16 21:46:35'),
(156,'copa_background_email_custom','Utilizar imagen email default','1',NULL,'2024-09-16 21:46:37'),
(157,'background_login_custom_path','Ruta del fondo de la pantalla de login','\'\'',NULL,NULL),
(158,'_notificaciones_email_from','Email*','omarliberatto@yafoconsultora.com',NULL,'2024-09-25 21:09:28'),
(159,'_notificaciones_email_from_name','Nombre*','omar',NULL,'2024-09-25 21:09:28'),
(160,'notificaciones_email_config',NULL,'{\"3\":\"4\"}',NULL,'2024-09-25 21:10:09');
/*!40000 ALTER TABLE `variables` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2024-09-25 15:12:17
