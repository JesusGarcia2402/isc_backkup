-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: isc
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_control` varchar(20) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `titulo_trabajo` varchar(200) DEFAULT NULL,
  `modalidad_titulacion` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_control` (`numero_control`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (58,'1','Armando','Ampliación de redes wifi en las distintas zonas de la zona industrial Pastejé y configuración en los equipos de red para detección y bloqueo de DHCP no seguro','Titulación Integral en la modalidad de Tesis Profesional','2025-06-24 17:22:29'),(59,'2','Jesus',NULL,'Titulación Integral en la modalidad de Proyecto de Investigación','2025-06-24 17:22:52');
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_modificaciones`
--

DROP TABLE IF EXISTS `historial_modificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_modificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(11) NOT NULL,
  `paso` varchar(100) DEFAULT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `alumno` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_modificaciones`
--

LOCK TABLES `historial_modificaciones` WRITE;
/*!40000 ALTER TABLE `historial_modificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_modificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maestros`
--

DROP TABLE IF EXISTS `maestros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maestros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maestros`
--

LOCK TABLES `maestros` WRITE;
/*!40000 ALTER TABLE `maestros` DISABLE KEYS */;
INSERT INTO `maestros` VALUES (1,'M. en C. C. Adriana Reyes Nava',1),(2,'Ing. Marcial Jesús Martínez Blas',1),(3,'M. en C. C. Juan Carlos Suárez Sánchez',1),(4,'M. en T. C. Erika López González',1),(5,'M. en T. I. Teresa Plata Hernández',1),(6,'M. en P. Bruno Emyr Carreto Cid de León',1),(7,'Dr. Juan Alberto Antonio Velázquez',1),(8,'Dr. Leopoldo Gil Antonio',1),(9,'Ing. Jovani del Boque Florentino',1),(10,'M. en C. I. Grisel Miranda Piña',1);
/*!40000 ALTER TABLE `maestros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proceso_titulacion`
--

DROP TABLE IF EXISTS `proceso_titulacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proceso_titulacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(11) NOT NULL,
  `protocolo_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `protocolo_doc` varchar(255) DEFAULT NULL,
  `titulo_trabajo` varchar(800) DEFAULT NULL,
  `numero_registro` varchar(50) DEFAULT NULL,
  `protocolo_fecha` timestamp NULL DEFAULT NULL,
  `asesor_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `asesor_doc` varchar(255) DEFAULT NULL,
  `asesor_nombres` text DEFAULT NULL,
  `comision_revisora1` text DEFAULT NULL,
  `asesor_fecha` timestamp NULL DEFAULT NULL,
  `validacion_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `validacion_doc` varchar(255) DEFAULT NULL,
  `validacion_fecha` timestamp NULL DEFAULT NULL,
  `fecha_titulacion_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `fecha_titulacion_doc` varchar(255) DEFAULT NULL,
  `comision_revisora2` text DEFAULT NULL,
  `fecha_titulacion_fecha` timestamp NULL DEFAULT NULL,
  `presidente` varchar(255) DEFAULT NULL,
  `secretario` varchar(255) DEFAULT NULL,
  `vocal` varchar(255) DEFAULT NULL,
  `suplente` varchar(255) DEFAULT NULL,
  `hora_titulacion` time DEFAULT NULL,
  `digitalizacion_doc` varchar(255) DEFAULT NULL,
  `digitalizacion_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `registro_oficio_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `registro_oficio_doc` varchar(255) DEFAULT NULL,
  `liberacion_asesor_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `liberacion_asesor_doc` varchar(255) DEFAULT NULL,
  `numero_registro_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `numero_registro_doc` varchar(255) DEFAULT NULL,
  `oficio_resultados_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `oficio_resultados_doc` varchar(255) DEFAULT NULL,
  `carta_postulacion_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `carta_postulacion_doc` varchar(255) DEFAULT NULL,
  `oficio_aceptacion_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `oficio_aceptacion_doc` varchar(255) DEFAULT NULL,
  `asignacion_revisores_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `asignacion_revisores_doc` varchar(255) DEFAULT NULL,
  `liberacion_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `liberacion_doc` varchar(255) DEFAULT NULL,
  `registro_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `registro_doc` varchar(255) DEFAULT NULL,
  `registro_carta_dual_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `registro_carta_dual_doc` varchar(255) DEFAULT NULL,
  `ultima_modificacion_por` varchar(100) DEFAULT NULL,
  `ultima_modificacion_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `numero_cds` int(11) NOT NULL,
  `protocolos_status` enum('pendiente','en-progreso','completado') DEFAULT 'pendiente',
  `protocolo_doc1` varchar(255) DEFAULT NULL,
  `protocolo_doc2` varchar(255) DEFAULT NULL,
  `protocolo_doc3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  CONSTRAINT `proceso_titulacion_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proceso_titulacion`
--

LOCK TABLES `proceso_titulacion` WRITE;
/*!40000 ALTER TABLE `proceso_titulacion` DISABLE KEYS */;
INSERT INTO `proceso_titulacion` VALUES (52,58,'completado','assets/uploads/protocolo_685adf432b3b6.pdf','Ampliación de redes wifi en las distintas zonas de la zona industrial Pastejé y configuración en los equipos de red para detección y bloqueo de DHCP no seguro','I0529',NULL,'completado','assets/uploads/asesor_685adf707c6e9.pdf','M. en C. I. Grisel Miranda Piña','Dr. Leopoldo Gil Antonio, Ing. Jovani del Boque Florentino, M. en C. C. Juan Carlos Suárez Sánchez',NULL,'pendiente',NULL,NULL,'completado','assets/uploads/titulacion_685adfbd5a5ed.pdf',NULL,'2025-03-10 06:00:00','M. en C. C. Adriana Reyes Nava','Dr. Leopoldo Gil Antonio','M. en C. C. Juan Carlos Suárez Sánchez','M. en T. C. Erika López González','13:00:00','assets/uploads/digitalizacion_685ae028e3b59.pdf','completado','completado','assets/uploads/registro_oficio_685ae05158ae5.pdf','pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'completado','assets/uploads/liberacion_685ae06d190df.pdf','pendiente',NULL,'pendiente',NULL,NULL,'2025-06-24 17:29:17',6,'pendiente',NULL,NULL,NULL),(53,59,'pendiente',NULL,NULL,NULL,NULL,'pendiente',NULL,NULL,NULL,NULL,'pendiente',NULL,NULL,'pendiente',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pendiente','pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,'pendiente',NULL,NULL,'2025-06-24 17:22:52',0,'pendiente',NULL,NULL,NULL);
/*!40000 ALTER TABLE `proceso_titulacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `titulados_anio`
--

DROP TABLE IF EXISTS `titulados_anio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `titulados_anio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anio` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `titulados_anio`
--

LOCK TABLES `titulados_anio` WRITE;
/*!40000 ALTER TABLE `titulados_anio` DISABLE KEYS */;
INSERT INTO `titulados_anio` VALUES (57,2025,1),(58,2025,1),(59,2025,1),(60,2025,1),(61,2025,1),(62,2025,1),(63,2025,1),(64,2025,1),(65,2025,1),(66,2025,1),(67,2025,1),(68,2025,1),(69,2025,1),(70,2025,1),(71,2025,1),(72,2025,1),(73,2025,1),(74,2025,1),(75,2025,1),(76,2025,1),(77,2025,1),(78,2025,1),(79,2025,1),(80,2025,1);
/*!40000 ALTER TABLE `titulados_anio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'','Juan Carlos Ambriz Polo','admin123','2025-05-26 18:20:45');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-24 11:38:14
