-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: db_lspro
-- ------------------------------------------------------
-- Server version	5.7.39

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

USE `u905945074_lspro`;

--
-- Table structure for table `audit_documents`
--

DROP TABLE IF EXISTS `audit_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `audit_finding_id` bigint(20) unsigned NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` datetime NOT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `audit_documents_audit_finding_id_index` (`audit_finding_id`),
  CONSTRAINT `audit_documents_audit_finding_id_foreign` FOREIGN KEY (`audit_finding_id`) REFERENCES `audit_findings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_documents`
--

LOCK TABLES `audit_documents` WRITE;
/*!40000 ALTER TABLE `audit_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_findings`
--

DROP TABLE IF EXISTS `audit_findings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_findings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint(20) unsigned NOT NULL,
  `audit_type` enum('kecukupan','kesesuaian') COLLATE utf8mb4_unicode_ci NOT NULL,
  `finding_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('minor','mayor','observation') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_required` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','closed','requires_verification') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `pic_responsible_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_findings_pic_responsible_id_foreign` (`pic_responsible_id`),
  KEY `audit_findings_pengajuan_id_index` (`pengajuan_id`),
  KEY `audit_findings_audit_type_index` (`audit_type`),
  KEY `audit_findings_status_index` (`status`),
  CONSTRAINT `audit_findings_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_findings_pic_responsible_id_foreign` FOREIGN KEY (`pic_responsible_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_findings`
--

LOCK TABLES `audit_findings` WRITE;
/*!40000 ALTER TABLE `audit_findings` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_findings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bandings`
--

DROP TABLE IF EXISTS `bandings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bandings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `pengajuan_id` bigint(20) unsigned DEFAULT NULL,
  `jenis` enum('banding','keluhan','laporan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subjek` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('terkirim','diproses','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terkirim',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bandings_user_id_foreign` (`user_id`),
  KEY `bandings_pengajuan_id_foreign` (`pengajuan_id`),
  CONSTRAINT `bandings_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bandings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bandings`
--

LOCK TABLES `bandings` WRITE;
/*!40000 ALTER TABLE `bandings` DISABLE KEYS */;
INSERT INTO `bandings` VALUES (1,10,4,'keluhan','belum di acc','ga di acc acc','1781510108_banding_6a2fafdc1db74.pdf','diproses','kami proses','2026-06-15 07:55:08','2026-06-15 07:59:27');
/*!40000 ALTER TABLE `bandings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_rejections`
--

DROP TABLE IF EXISTS `document_rejections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_rejections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint(20) unsigned NOT NULL,
  `document_category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `catatan_kurang` text COLLATE utf8mb4_unicode_ci,
  `catatan_tu` text COLLATE utf8mb4_unicode_ci,
  `rejected_at` datetime DEFAULT NULL,
  `resubmitted_at` datetime DEFAULT NULL,
  `status` enum('pending','rejected','resubmitted','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_rejections_pengajuan_id_index` (`pengajuan_id`),
  KEY `document_rejections_status_index` (`status`),
  CONSTRAINT `document_rejections_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_rejections`
--

LOCK TABLES `document_rejections` WRITE;
/*!40000 ALTER TABLE `document_rejections` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_rejections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `internal_chats`
--

DROP TABLE IF EXISTS `internal_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `internal_chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `internal_chats_user_id_foreign` (`user_id`),
  KEY `internal_chats_group_name_index` (`group_name`),
  CONSTRAINT `internal_chats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_chats`
--

LOCK TABLES `internal_chats` WRITE;
/*!40000 ALTER TABLE `internal_chats` DISABLE KEYS */;
INSERT INTO `internal_chats` VALUES (1,10,'cs_client_10','test','2026-06-15 06:44:23','2026-06-15 06:44:23'),(2,7,'cs_client_10','Halo Bapak/Ibu dari Klien 1. Proses evaluasi sedang dikerjakan oleh tim teknis kami. Kami akan segera memberi update jika dokumen dinyatakan lengkap.','2026-06-15 06:44:56','2026-06-15 06:44:56'),(3,10,'cs_client_10','test','2026-06-15 07:55:28','2026-06-15 07:55:28'),(4,7,'cs_client_10','masuk','2026-06-15 07:59:06','2026-06-15 07:59:06'),(5,7,'cs_client_10','test','2026-06-15 08:15:35','2026-06-15 08:15:35');
/*!40000 ALTER TABLE `internal_chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `tahap` enum('sertifikasi_tahap_1','sertifikasi_tahap_2','audit_kecukupan','audit_kesesuaian','penerbitan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_index` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint(20) unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `amount_total` decimal(12,2) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_date` datetime DEFAULT NULL,
  `payment_method` enum('bank_transfer','cash','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('unpaid','pending_verification','paid','overdue','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_pengajuan_id_index` (`pengajuan_id`),
  KEY `invoices_status_index` (`status`),
  CONSTRAINT `invoices_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,2,'INV-LSPRO-2026-00001','2026-06-15','2026-06-22',15000000.00,0.00,NULL,NULL,'Pembayaran telah diverifikasi oleh Keuangan.','paid','2026-06-15 04:17:55','2026-06-15 05:50:29');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2026_05_13_042921_create_pengajuans_table',1),(4,'2026_05_19_112404_add_ceklis_columns_to_pengajuans_table',2),(5,'2026_05_29_133538_add_file_permohonan_ttd_to_pengajuans_table',3),(6,'2026_06_10_000001_create_invoices_table',4),(7,'2026_06_10_000002_create_invoice_items_table',4),(8,'2026_06_10_000003_create_document_rejections_table',4),(9,'2026_06_10_000004_create_audit_findings_table',4),(10,'2026_06_10_000005_create_audit_documents_table',4),(11,'2026_06_10_000006_create_survailen_schedules_table',4),(12,'2026_06_10_000007_create_resertifikasi_scope_changes_table',4),(13,'2026_06_10_000008_create_notifications_table',4),(14,'2026_06_10_000009_create_pengajuan_status_histories_table',5),(15,'2026_06_11_000001_add_jenis_sertifikasi_to_pengajuans_table',6),(16,'2026_06_11_000002_add_columns_to_survailen_schedules_table',6),(17,'2026_06_11_000003_create_bandings_table',6),(18,'2026_06_11_131457_update_roles_and_add_columns_to_users_table',7),(19,'2026_06_11_131500_add_draft_columns_to_pengajuans_table',7),(20,'2026_06_11_131502_add_subjek_to_bandings_table',7),(21,'2026_06_11_131526_create_internal_chats_table',7),(22,'2026_06_11_144142_add_google_id_to_users_table',8),(23,'2026_06_11_232222_add_sub_role_to_users_table',9),(24,'2026_06_12_000001_add_profile_fields_to_users_table',10),(25,'2026_06_13_151532_add_email_verified_at_to_users_table',11),(26,'2026_06_14_224622_add_is_read_tu_to_pengajuans_table',12),(27,'2026_06_15_094520_add_file_ceklis_to_pengajuans',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `pengajuan_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('reminder_survailen','dokumen_ditolak','audit_scheduled','pembayaran_invoice','perubahan_status') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `email_sent_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  KEY `notifications_pengajuan_id_index` (`pengajuan_id`),
  KEY `notifications_is_read_index` (`is_read`),
  KEY `notifications_type_index` (`type`),
  CONSTRAINT `notifications_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,10,1,'perubahan_status','Pengecekan Awal Selesai','Pengecekan awal oleh TU selesai. Silakan unggah dokumen kelengkapan (SIUP, TDI, dll).',1,0,NULL,NULL,'2026-06-15 03:19:42','2026-06-15 03:58:33'),(2,10,2,'perubahan_status','Pengecekan Awal Selesai','Pengecekan awal oleh TU selesai. Silakan unggah dokumen kelengkapan (SIUP, TDI, dll).',1,0,NULL,NULL,'2026-06-15 03:41:28','2026-06-15 03:58:31'),(5,7,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',1,0,NULL,NULL,'2026-06-15 05:15:09','2026-06-15 05:25:40'),(6,4,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',0,0,NULL,NULL,'2026-06-15 05:15:09','2026-06-15 05:15:09'),(7,5,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',0,0,NULL,NULL,'2026-06-15 05:15:09','2026-06-15 05:15:09'),(8,6,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',1,0,NULL,NULL,'2026-06-15 05:15:09','2026-06-15 08:28:17'),(11,7,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',1,0,NULL,NULL,'2026-06-15 05:25:06','2026-06-15 05:25:40'),(12,4,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',0,0,NULL,NULL,'2026-06-15 05:25:06','2026-06-15 05:25:06'),(13,5,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',0,0,NULL,NULL,'2026-06-15 05:25:06','2026-06-15 05:25:06'),(14,6,2,'perubahan_status','Pembayaran Tagihan','Klien telah mengunggah bukti pembayaran untuk invoice #INV-LSPRO-2026-00001',1,0,NULL,NULL,'2026-06-15 05:25:06','2026-06-15 08:28:17'),(15,10,2,'perubahan_status','Pembayaran Diterima','Pembayaran untuk invoice #INV-LSPRO-2026-00001 telah diverifikasi. Pengajuan dilanjutkan.',1,0,NULL,NULL,'2026-06-15 05:50:29','2026-06-15 05:51:06'),(16,8,4,'perubahan_status','Keluhan/Laporan Baru','Keluhan/Laporan baru diajukan oleh ',0,0,NULL,NULL,'2026-06-15 07:55:08','2026-06-15 07:55:08'),(17,4,4,'perubahan_status','Keluhan/Laporan Baru','Keluhan/Laporan baru diajukan oleh ',0,0,NULL,NULL,'2026-06-15 07:55:08','2026-06-15 07:55:08'),(18,5,4,'perubahan_status','Keluhan/Laporan Baru','Keluhan/Laporan baru diajukan oleh ',0,0,NULL,NULL,'2026-06-15 07:55:08','2026-06-15 07:55:08'),(19,6,4,'perubahan_status','Keluhan/Laporan Baru','Keluhan/Laporan baru diajukan oleh ',1,0,NULL,NULL,'2026-06-15 07:55:08','2026-06-15 08:28:17'),(20,10,4,'perubahan_status','Keluhan Terkirim','Keluhan/Laporan Anda telah diterima.',1,0,NULL,NULL,'2026-06-15 07:55:08','2026-06-15 07:55:13'),(21,10,4,'perubahan_status','Pengecekan Awal Selesai','Pengecekan awal oleh TU selesai. Silakan unggah dokumen kelengkapan (SIUP, TDI, dll).',1,0,NULL,NULL,'2026-06-15 07:58:00','2026-06-17 08:32:47'),(22,10,4,'perubahan_status','Jadwal Survailen Dikirim','Yth. Klien,\r\n\r\nMengingatkan kembali bahwa jadwal survailen sertifikasi produk Anda untuk tahun ke-1 (Tahun 2026) telah tiba.\r\nSilakan login ke aplikasi LSPro dan unggah dokumen persyaratan survailen sebelum tanggal 16 June 2026.\r\n\r\nLink Login: http://localhost:8000/login\r\n\r\nTerima Kasih,\r\nLSPro BRMP SDLP',1,0,NULL,NULL,'2026-06-15 07:58:53','2026-06-17 08:32:47'),(23,10,3,'perubahan_status','Pengecekan Awal Selesai','Pengecekan awal oleh TU selesai. Silakan unggah dokumen kelengkapan (SIUP, TDI, dll).',1,0,NULL,NULL,'2026-06-15 08:15:00','2026-06-17 08:32:47'),(24,10,3,'perubahan_status','Jadwal Survailen Dikirim','Yth. Klien,\r\n\r\nMengingatkan kembali bahwa jadwal survailen sertifikasi produk Anda untuk tahun ke-1 (Tahun 2026) telah tiba.\r\nSilakan login ke aplikasi LSPro dan unggah dokumen persyaratan survailen sebelum tanggal 17 June 2026.\r\n\r\nLink Login: http://127.0.0.1:8000/login\r\n\r\nTerima Kasih,\r\nLSPro BRMP SDLP',1,0,NULL,NULL,'2026-06-15 08:15:26','2026-06-17 08:32:47'),(25,10,7,'perubahan_status','Perlu Perbaikan','Pengajuan #7 perlu perbaikan form. Cek catatan Tata Usaha.',0,0,NULL,NULL,'2026-06-18 00:41:15','2026-06-18 00:41:15');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `pengajuan_status_histories`
--

DROP TABLE IF EXISTS `pengajuan_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajuan_status_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint(20) unsigned NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `from_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_status_histories_actor_id_foreign` (`actor_id`),
  KEY `pengajuan_status_histories_pengajuan_id_created_at_index` (`pengajuan_id`,`created_at`),
  CONSTRAINT `pengajuan_status_histories_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengajuan_status_histories_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengajuan_status_histories`
--

LOCK TABLES `pengajuan_status_histories` WRITE;
/*!40000 ALTER TABLE `pengajuan_status_histories` DISABLE KEYS */;
INSERT INTO `pengajuan_status_histories` VALUES (1,1,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-13 01:04:30','2026-05-13 01:04:30'),(2,2,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-13 01:10:23','2026-05-13 01:10:23'),(3,3,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-13 02:00:08','2026-05-13 02:00:08'),(4,4,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-13 04:06:12','2026-05-13 04:06:12'),(5,5,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-13 04:08:07','2026-05-13 04:08:07'),(6,6,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-17 21:08:09','2026-05-17 21:08:09'),(7,7,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-17 21:17:19','2026-05-17 21:17:19'),(8,8,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-17 21:19:31','2026-05-17 21:19:31'),(9,9,1,NULL,'diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-17 21:45:29','2026-05-17 21:45:29'),(10,10,1,NULL,'diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-17 23:15:59','2026-05-17 23:15:59'),(11,11,1,NULL,'lengkap','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-19 04:43:39','2026-05-19 04:43:39'),(12,12,1,NULL,'lengkap','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-19 04:36:45','2026-05-19 04:36:45'),(13,13,1,NULL,'proses_evaluator','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-27 06:59:32','2026-05-27 06:59:32'),(14,14,1,NULL,'proses_audit','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-27 06:59:23','2026-05-27 06:59:23'),(15,15,1,NULL,'selesai','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-27 06:58:28','2026-05-27 06:58:28'),(16,16,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-19 05:27:31','2026-05-19 05:27:31'),(17,17,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-19 05:36:43','2026-05-19 05:36:43'),(18,18,1,NULL,'proses_audit','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-27 07:15:56','2026-05-27 07:15:56'),(19,19,1,NULL,'Diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-25 00:13:08','2026-05-25 00:13:08'),(20,20,1,NULL,'diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-25 01:25:57','2026-05-25 01:25:57'),(21,21,1,NULL,'perbaikan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-26 01:27:29','2026-05-26 01:27:29'),(22,22,1,NULL,'proses_audit','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 04:49:53','2026-05-29 04:49:53'),(23,23,1,NULL,'proses_audit','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 04:45:27','2026-05-29 04:45:27'),(24,24,1,NULL,'proses_audit','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 04:34:00','2026-05-29 04:34:00'),(25,25,1,NULL,'selesai','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 05:39:14','2026-05-29 05:39:14'),(26,26,1,NULL,'menunggu_ttd','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 07:59:20','2026-05-29 07:59:20'),(27,27,1,NULL,'diajukan','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 08:40:23','2026-05-29 08:40:23'),(28,28,1,NULL,'menunggu_ttd','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-05-29 16:04:09','2026-05-29 16:04:09'),(29,29,1,NULL,'menunggu_ttd','Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.','2026-06-09 03:10:50','2026-06-09 03:10:50'),(30,30,1,NULL,'diajukan','Formulir permohonan disimpan dan dikirim untuk pengecekan Tata Usaha.','2026-06-11 02:19:29','2026-06-11 02:19:29'),(31,1,10,'draft','menunggu_ttd','Form 7.2-1 dibuat otomatis. Klien perlu mengunggah dokumen bertanda tangan dan bermeterai.','2026-06-15 02:10:50','2026-06-15 02:10:50'),(32,1,7,'diajukan','menunggu_lampiran','Pengecekan awal selesai. Menunggu klien mengunggah dokumen kelengkapan.','2026-06-15 03:14:13','2026-06-15 03:14:13'),(33,2,7,'diajukan','menunggu_lampiran','Pengecekan awal selesai. Menunggu klien mengunggah dokumen kelengkapan.','2026-06-15 03:41:28','2026-06-15 03:41:28'),(34,2,10,'menunggu_lampiran','evaluasi_724_tu','Klien telah mengunggah dokumen kelengkapan.','2026-06-15 03:56:25','2026-06-15 03:56:25'),(35,4,7,'diajukan','menunggu_lampiran','Pengecekan awal selesai. Menunggu klien mengunggah dokumen kelengkapan.','2026-06-15 07:58:00','2026-06-15 07:58:00'),(36,3,7,'diajukan','menunggu_lampiran','Pengecekan awal selesai. Menunggu klien mengunggah dokumen kelengkapan.','2026-06-15 08:15:00','2026-06-15 08:15:00'),(37,7,7,'diajukan','perbaikan','Formulir ditandai perlu perbaikan oleh Tata Usaha.','2026-06-18 00:41:15','2026-06-18 00:41:15');
/*!40000 ALTER TABLE `pengajuan_status_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengajuans`
--

DROP TABLE IF EXISTS `pengajuans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajuans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tahap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_registrasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pengajuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sertifikasi',
  `jenis_sertifikasi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Diajukan',
  `is_read_tu` tinyint(1) NOT NULL DEFAULT '0',
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `current_step` int(11) NOT NULL DEFAULT '1',
  `progress_percent` int(11) NOT NULL DEFAULT '0',
  `ceklis_dokumen` text COLLATE utf8mb4_unicode_ci,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `nama_tu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan_admin` text COLLATE utf8mb4_unicode_ci,
  `data_form` json DEFAULT NULL,
  `file_permohonan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_permohonan_ttd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ceklis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ceklis_ttd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuans_user_id_foreign` (`user_id`),
  CONSTRAINT `pengajuans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengajuans`
--

LOCK TABLES `pengajuans` WRITE;
/*!40000 ALTER TABLE `pengajuans` DISABLE KEYS */;
INSERT INTO `pengajuans` VALUES (1,10,'1',NULL,'sertifikasi','Pupuk Organik','menunggu_lampiran',1,0,1,0,NULL,NULL,NULL,NULL,'{\"merek\": \"Pupuk Teknika\", \"hp_wmm\": \"23131321312\", \"no_sni\": \"11231321\", \"perihal\": \"Permohonan Sertifikasi SPPT SNI\", \"tk_mutu\": \"11\", \"tk_staf\": \"11\", \"draft_id\": \"1\", \"lampiran\": \"1 Berkas\", \"nama_wmm\": \"bew\", \"telp_wmm\": \"12331\", \"total_tk\": \"111\", \"email_wmm\": \"rakhabuana@apps.ipb.ac.id\", \"judul_sni\": \"pupuk\", \"komoditas\": \"Pupuk NPK cair\", \"sni_acuan\": \"11231321\", \"hp_pemohon\": \"12331\", \"nama_pupuk\": \"Pupuk NPK cair\", \"tk_nonstaf\": \"11\", \"asal_pabrik\": \"Indonesia\", \"badan_hukum\": \"PT\", \"jenis_pupuk\": \"npk 123\", \"jumlah_lini\": \"1\", \"kota_kantor\": \"bogor\", \"kota_pabrik\": \"bogor\", \"merek_pupuk\": \"Pupuk Teknika\", \"nama_produk\": \"Pupuk NPK cair\", \"nomor_surat\": \"001/LSPRO/VI/2026\", \"standar_smm\": \"ISO 9001:2015\", \"telp_kantor\": \"21314332\", \"telp_pabrik\": \"342423\", \"tipe_produk\": \"npk 123\", \"tk_produksi\": \"11\", \"api_importir\": \"1233214131\", \"email_kantor\": \"rakhabuana@apps.ipb.ac.id\", \"email_pabrik\": \"rakhabuana@apps.ipb.ac.id\", \"jarak_pabrik\": \"11\", \"merek_produk\": \"Pupuk Teknika\", \"nama_pemohon\": \"rakha\", \"telp_pemohon\": \"12331\", \"waktu_pabrik\": \"60 menit\", \"alamat_kantor\": \"fsdffsd\", \"alamat_pabrik\": \"fsdffsd\", \"bahasa_pabrik\": \"Indonesia\", \"hp_penghubung\": \"4324432342\", \"nama_importir\": \"abew\", \"status_produk\": \"Produksi Sendiri\", \"alamat_pemohon\": \"fsdffsd\", \"status_pemohon\": \"Produsen\", \"alamat_importir\": \"fsdffsd\", \"jabatan_pemohon\": \"jendral\", \"nama_penghubung\": \"rakhabuana\", \"nama_perusahaan\": \"PT CAHAYA BUANA\", \"provinsi_kantor\": \"jawa barat\", \"provinsi_pabrik\": \"jawa barat\", \"email_penghubung\": \"rakhabuana@apps.ipb.ac.id\", \"jenis_sertifikasi\": \"Pupuk Organik\", \"penerjemah_pabrik\": \"Tersedia jika diperlukan\", \"jabatan_penghubung\": \"manager\", \"kapasitas_produksi\": \"20\", \"tahapan_pembubuhan_sni\": \"wdasfdasfasf\", \"kewarganegaraan_pemohon\": \"Indonesia\"}','1/Form_7.2-1_001_LSPRO_VI_2026_1.pdf',NULL,NULL,NULL,'2026-06-15 02:09:47','2026-06-15 03:14:13'),(2,10,'1',NULL,'Sertifikasi',NULL,'proses_evaluasi',1,0,1,0,NULL,NULL,NULL,NULL,'\"{\\\"nama_pemohon\\\":\\\"rakha\\\",\\\"jabatan_pemohon\\\":\\\"jendral\\\",\\\"alamat_pemohon\\\":\\\"fsdffsd\\\",\\\"telp_pemohon\\\":\\\"12331\\\",\\\"hp_pemohon\\\":\\\"12331\\\",\\\"kewarganegaraan_pemohon\\\":\\\"Indonesia\\\",\\\"nama_perusahaan\\\":\\\"PT CAHAYA BUANA\\\",\\\"nama_penghubung\\\":\\\"rakhabuana\\\",\\\"alamat_kantor\\\":\\\"fsdffsd\\\",\\\"alamat_pabrik\\\":\\\"fsdffsd\\\",\\\"nama_produk\\\":\\\"Pupuk NPK cair\\\",\\\"merek_produk\\\":\\\"Pupuk Teknika\\\",\\\"tipe_produk\\\":\\\"npk 123\\\",\\\"no_sni\\\":\\\"11231321\\\",\\\"kapasitas_produksi\\\":\\\"20\\\",\\\"standar_smm\\\":\\\"ISO 9001:2015\\\",\\\"total_tk\\\":\\\"100\\\",\\\"tk_produksi\\\":\\\"25\\\",\\\"tk_mutu\\\":\\\"25\\\",\\\"tk_staf\\\":\\\"25\\\",\\\"tk_nonstaf\\\":\\\"25\\\",\\\"lampiran\\\":[],\\\"akte_perusahaan\\\":\\\"1781495437_akte_perusahaan.pdf\\\",\\\"izin_usaha_industri\\\":\\\"1781495437_izin_usaha_industri.pdf\\\",\\\"siup_tdup\\\":\\\"1781495437_siup_tdup.pdf\\\",\\\"sertifikat_merek\\\":\\\"1781495437_sertifikat_merek.pdf\\\",\\\"pelimpahan_merek\\\":\\\"1781495437_pelimpahan_merek.pdf\\\",\\\"bukti_importir\\\":\\\"1781495437_bukti_importir.pdf\\\",\\\"api_umum\\\":\\\"1781495782_api_umum.pdf\\\",\\\"struktur_organisasi\\\":\\\"1781495782_struktur_organisasi.pdf\\\",\\\"alur_produksi_mutu\\\":\\\"1781495782_alur_produksi_mutu.pdf\\\",\\\"daftar_alat_mesin\\\":\\\"1781495782_daftar_alat_mesin.pdf\\\",\\\"daftar_alat_uji_kalibrasi\\\":\\\"1781495782_daftar_alat_uji_kalibrasi.pdf\\\",\\\"surat_pernyataan_diri\\\":\\\"1781495782_surat_pernyataan_diri.pdf\\\",\\\"pedoman_mutu\\\":\\\"1781495782_pedoman_mutu.pdf\\\",\\\"daftar_prosedur_ik\\\":\\\"1781495782_daftar_prosedur_ik.pdf\\\",\\\"perjanjian_sertifikasi\\\":\\\"1781495782_perjanjian_sertifikasi.pdf\\\",\\\"ilustrasi_tanda_sni\\\":\\\"1781495782_ilustrasi_tanda_sni.pdf\\\"}\"','Form_7.2-1_1781494720_2.docx',NULL,NULL,NULL,'2026-06-15 03:38:40','2026-06-15 05:50:29'),(3,10,'1',NULL,'Sertifikasi',NULL,'menunggu_lampiran',1,0,1,0,NULL,NULL,NULL,NULL,'\"{\\\"nama_pemohon\\\":\\\"Muhamad Rakha Buana\\\",\\\"jabatan_pemohon\\\":\\\"Direktur Utama\\\",\\\"alamat_pemohon\\\":\\\"gunung batu\\\",\\\"telp_pemohon\\\":\\\"12331\\\",\\\"hp_pemohon\\\":\\\"+6289522704092\\\",\\\"kewarganegaraan_pemohon\\\":\\\"Indonesia\\\",\\\"nama_perusahaan\\\":\\\"PT CAHAYA BUANA\\\",\\\"nama_penghubung\\\":\\\"abew buana\\\",\\\"alamat_kantor\\\":\\\"ciwaringin\\\",\\\"alamat_pabrik\\\":\\\"fsdffsd\\\",\\\"nama_produk\\\":\\\"Pupuk NPK padat\\\",\\\"merek_produk\\\":\\\"Pupuk Teknika\\\",\\\"tipe_produk\\\":\\\"npk 123\\\",\\\"no_sni\\\":\\\"11231321\\\",\\\"kapasitas_produksi\\\":\\\"20\\\",\\\"standar_smm\\\":\\\"ISO 9001:2015\\\",\\\"total_tk\\\":\\\"100\\\",\\\"tk_produksi\\\":\\\"25\\\",\\\"tk_mutu\\\":\\\"25\\\",\\\"tk_staf\\\":\\\"25\\\",\\\"tk_nonstaf\\\":\\\"25\\\",\\\"lampiran\\\":[]}\"','Form_7.2-1_1781509290_3.docx',NULL,NULL,NULL,'2026-06-15 07:41:30','2026-06-15 08:15:00'),(4,10,'1',NULL,'Sertifikasi',NULL,'menunggu_lampiran',1,0,1,0,NULL,NULL,NULL,NULL,'\"{\\\"nama_pemohon\\\":\\\"Muhamad Rakha Buana\\\",\\\"jabatan_pemohon\\\":\\\"Direktur Utama\\\",\\\"alamat_pemohon\\\":\\\"fsdffsd\\\",\\\"telp_pemohon\\\":\\\"12331\\\",\\\"hp_pemohon\\\":\\\"12331\\\",\\\"kewarganegaraan_pemohon\\\":\\\"Indonesia\\\",\\\"nama_perusahaan\\\":\\\"PT CAHAYA BUANA\\\",\\\"nama_penghubung\\\":\\\"rakhabuana\\\",\\\"alamat_kantor\\\":\\\"fsdffsd\\\",\\\"alamat_pabrik\\\":\\\"fsdffsd\\\",\\\"nama_produk\\\":\\\"Pupuk NPK padat\\\",\\\"merek_produk\\\":\\\"Pupuk Teknika\\\",\\\"tipe_produk\\\":\\\"npk 123\\\",\\\"no_sni\\\":\\\"11231321\\\",\\\"kapasitas_produksi\\\":\\\"20\\\",\\\"standar_smm\\\":\\\"ISO 9001:2015\\\",\\\"total_tk\\\":\\\"100\\\",\\\"tk_produksi\\\":\\\"25\\\",\\\"tk_mutu\\\":\\\"25\\\",\\\"tk_staf\\\":\\\"25\\\",\\\"tk_nonstaf\\\":\\\"25\\\",\\\"lampiran\\\":[]}\"','Form_7.2-1_1781510042_4.docx',NULL,NULL,NULL,'2026-06-15 07:54:01','2026-06-15 07:58:00'),(5,10,'1',NULL,'sertifikasi',NULL,'draft',0,0,1,0,NULL,NULL,NULL,NULL,'\"{\\\"tahap\\\":\\\"1\\\",\\\"jenis_sertifikasi\\\":\\\"Sertifikasi Baru\\\",\\\"lampiran\\\":\\\"1 Berkas\\\",\\\"perihal\\\":\\\"Permohonan Sertifikasi SPPT SNI\\\",\\\"nama_pemohon\\\":\\\"Muhamad Rakha Buana\\\",\\\"jabatan_pemohon\\\":null,\\\"alamat_pemohon\\\":null,\\\"telp_pemohon\\\":null,\\\"hp_pemohon\\\":null,\\\"kewarganegaraan_pemohon\\\":\\\"Indonesia\\\",\\\"status_pemohon\\\":\\\"Produsen\\\",\\\"nama_penghubung\\\":null,\\\"jabatan_penghubung\\\":null,\\\"hp_penghubung\\\":null,\\\"email_penghubung\\\":null,\\\"nama_perusahaan\\\":null,\\\"badan_hukum\\\":null,\\\"alamat_kantor\\\":null,\\\"kota_kantor\\\":null,\\\"provinsi_kantor\\\":null,\\\"telp_kantor\\\":null,\\\"email_kantor\\\":null,\\\"alamat_pabrik\\\":null,\\\"kota_pabrik\\\":null,\\\"provinsi_pabrik\\\":null,\\\"telp_pabrik\\\":null,\\\"email_pabrik\\\":null,\\\"bahasa_pabrik\\\":\\\"Indonesia\\\",\\\"penerjemah_pabrik\\\":\\\"Tersedia jika diperlukan\\\",\\\"jarak_pabrik\\\":null,\\\"waktu_pabrik\\\":null,\\\"nama_importir\\\":null,\\\"api_importir\\\":null,\\\"alamat_importir\\\":null,\\\"nama_produk\\\":null,\\\"judul_sni\\\":null,\\\"no_sni\\\":null,\\\"merek_produk\\\":null,\\\"tipe_produk\\\":null,\\\"asal_pabrik\\\":null,\\\"status_produk\\\":\\\"Produksi Sendiri\\\",\\\"kapasitas_produksi\\\":null,\\\"jumlah_lini\\\":\\\"1\\\",\\\"standar_smm\\\":\\\"ISO 9001:2015\\\",\\\"nama_wmm\\\":null,\\\"telp_wmm\\\":null,\\\"hp_wmm\\\":null,\\\"email_wmm\\\":null,\\\"tahapan_pembubuhan_sni\\\":null,\\\"total_tk\\\":null,\\\"tk_produksi\\\":null,\\\"tk_mutu\\\":null,\\\"tk_staf\\\":null,\\\"tk_nonstaf\\\":null}\"',NULL,NULL,NULL,NULL,'2026-06-17 08:38:22','2026-06-17 08:38:46'),(6,10,'1',NULL,'sertifikasi',NULL,'draft',0,0,1,0,NULL,NULL,NULL,NULL,'\"{\\\"tahap\\\":\\\"1\\\",\\\"jenis_sertifikasi\\\":\\\"Pupuk Organik\\\",\\\"lampiran\\\":\\\"1 Berkas\\\",\\\"perihal\\\":\\\"Permohonan Sertifikasi SPPT SNI\\\",\\\"nama_pemohon\\\":\\\"Muhamad Rakha Buana\\\",\\\"jabatan_pemohon\\\":\\\"Direktur Utama\\\",\\\"alamat_pemohon\\\":\\\"fsdffsd\\\",\\\"telp_pemohon\\\":\\\"12331\\\",\\\"hp_pemohon\\\":\\\"12331\\\",\\\"kewarganegaraan_pemohon\\\":\\\"Indonesia\\\",\\\"status_pemohon\\\":\\\"Produsen\\\",\\\"nama_penghubung\\\":\\\"rakhabuana\\\",\\\"jabatan_penghubung\\\":\\\"manager\\\",\\\"hp_penghubung\\\":\\\"0895227904091\\\",\\\"email_penghubung\\\":\\\"rakhabuana@apps.ipb.ac.id\\\",\\\"nama_perusahaan\\\":\\\"PT CAHAYA BUANA\\\",\\\"badan_hukum\\\":\\\"PT\\\",\\\"alamat_kantor\\\":\\\"fsdffsd\\\",\\\"kota_kantor\\\":\\\"bogor\\\",\\\"provinsi_kantor\\\":\\\"jawa barat\\\",\\\"telp_kantor\\\":\\\"21314332\\\",\\\"email_kantor\\\":\\\"rakhabuana@apps.ipb.ac.id\\\",\\\"alamat_pabrik\\\":\\\"fsdffsd\\\",\\\"kota_pabrik\\\":\\\"bogor\\\",\\\"provinsi_pabrik\\\":\\\"jawa barat\\\",\\\"telp_pabrik\\\":\\\"342423\\\",\\\"email_pabrik\\\":\\\"rakhabuana@apps.ipb.ac.id\\\",\\\"bahasa_pabrik\\\":\\\"Indonesia\\\",\\\"penerjemah_pabrik\\\":\\\"Tersedia jika diperlukan\\\",\\\"jarak_pabrik\\\":\\\"12\\\",\\\"waktu_pabrik\\\":\\\"60 menit\\\",\\\"nama_importir\\\":\\\"abew\\\",\\\"api_importir\\\":\\\"1233214131\\\",\\\"alamat_importir\\\":\\\"fsdffsd\\\",\\\"nama_produk\\\":\\\"Pupuk NPK padat\\\",\\\"judul_sni\\\":\\\"pupuk\\\",\\\"no_sni\\\":\\\"11231321\\\",\\\"merek_produk\\\":\\\"Pupuk Teknika\\\",\\\"tipe_produk\\\":\\\"npk 123\\\",\\\"asal_pabrik\\\":\\\"Indonesia\\\",\\\"status_produk\\\":\\\"Produksi Sendiri\\\",\\\"kapasitas_produksi\\\":\\\"20\\\",\\\"jumlah_lini\\\":\\\"1\\\",\\\"standar_smm\\\":\\\"ISO 9001:2015\\\",\\\"nama_wmm\\\":null,\\\"telp_wmm\\\":null,\\\"hp_wmm\\\":null,\\\"email_wmm\\\":null,\\\"tahapan_pembubuhan_sni\\\":null,\\\"total_tk\\\":null,\\\"tk_produksi\\\":null,\\\"tk_mutu\\\":null,\\\"tk_staf\\\":null,\\\"tk_nonstaf\\\":null}\"',NULL,NULL,NULL,NULL,'2026-06-18 00:37:54','2026-06-18 00:38:31'),(7,10,'1',NULL,'Sertifikasi',NULL,'perbaikan',1,0,1,0,NULL,NULL,NULL,NULL,'\"{\\\"nama_pemohon\\\":\\\"Muhamad Rakha Buana\\\",\\\"jabatan_pemohon\\\":\\\"Direktur Utama\\\",\\\"alamat_pemohon\\\":\\\"fsdffsd\\\",\\\"telp_pemohon\\\":\\\"12331\\\",\\\"hp_pemohon\\\":\\\"12331\\\",\\\"kewarganegaraan_pemohon\\\":\\\"Indonesia\\\",\\\"nama_perusahaan\\\":\\\"PT CAHAYA BUANA\\\",\\\"nama_penghubung\\\":\\\"rakhabuana\\\",\\\"alamat_kantor\\\":\\\"fsdffsd\\\",\\\"alamat_pabrik\\\":\\\"fsdffsd\\\",\\\"nama_produk\\\":\\\"Pupuk NPK padat\\\",\\\"merek_produk\\\":\\\"Pupuk Teknika\\\",\\\"tipe_produk\\\":\\\"npk 123\\\",\\\"no_sni\\\":\\\"11231321\\\",\\\"kapasitas_produksi\\\":\\\"20\\\",\\\"standar_smm\\\":\\\"ISO 9001:2015\\\",\\\"total_tk\\\":\\\"100\\\",\\\"tk_produksi\\\":\\\"25\\\",\\\"tk_mutu\\\":\\\"25\\\",\\\"tk_staf\\\":\\\"25\\\",\\\"tk_nonstaf\\\":\\\"25\\\",\\\"lampiran\\\":[]}\"','Form_7.2-1_1781743128_7.docx',NULL,NULL,NULL,'2026-06-18 00:38:48','2026-06-18 00:41:15');
/*!40000 ALTER TABLE `pengajuans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resertifikasi_scope_changes`
--

DROP TABLE IF EXISTS `resertifikasi_scope_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resertifikasi_scope_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint(20) unsigned NOT NULL,
  `tipe_perubahan` enum('penambahan','pengurangan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_description_old` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_description_new` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alasan_perubahan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `surat_perubahan_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dokumen_pendukung_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('submitted','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resertifikasi_scope_changes_pengajuan_id_index` (`pengajuan_id`),
  KEY `resertifikasi_scope_changes_status_index` (`status`),
  CONSTRAINT `resertifikasi_scope_changes_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resertifikasi_scope_changes`
--

LOCK TABLES `resertifikasi_scope_changes` WRITE;
/*!40000 ALTER TABLE `resertifikasi_scope_changes` DISABLE KEYS */;
/*!40000 ALTER TABLE `resertifikasi_scope_changes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survailen_schedules`
--

DROP TABLE IF EXISTS `survailen_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `survailen_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` bigint(20) unsigned NOT NULL,
  `survailen_year` year(4) NOT NULL,
  `reminder_month` tinyint(3) unsigned NOT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  `deadline` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `status_dokumen` enum('menunggu','diterima','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `file_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_reminder_date` datetime DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survailen_schedules_pengajuan_id_survailen_year_unique` (`pengajuan_id`,`survailen_year`),
  KEY `survailen_schedules_pengajuan_id_index` (`pengajuan_id`),
  KEY `survailen_schedules_user_id_foreign` (`user_id`),
  CONSTRAINT `survailen_schedules_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survailen_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survailen_schedules`
--

LOCK TABLES `survailen_schedules` WRITE;
/*!40000 ALTER TABLE `survailen_schedules` DISABLE KEYS */;
INSERT INTO `survailen_schedules` VALUES (1,4,2026,6,1,'2026-06-16','sertifikat','menunggu',NULL,'2026-06-15 14:58:53','scheduled','2026-06-15 07:58:40','2026-06-15 07:58:53',10),(2,3,2026,6,1,'2026-06-17','sertifikat','menunggu',NULL,'2026-06-15 15:15:26','scheduled','2026-06-15 08:15:22','2026-06-15 08:15:26',10);
/*!40000 ALTER TABLE `survailen_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_penghubung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `sub_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_kerja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `no_telp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'PT Agro Lestari Nusantara','MUHAMAD RAKHA BUANA','client@gmail.com','2026-06-13 08:15:51','$2y$12$wMJQdAIek751Ruu/hOT8wOoQOwbK7EKsEIXdfPVf1unUwt5cgAAyi',NULL,'client',NULL,NULL,NULL,NULL,1,'08123456789','bogor',NULL,'2026-05-12 23:16:42','2026-05-18 22:54:56'),(4,'LSPro BRMP SDLP','Ketua LSPro','superadmin@lspro.id','2026-06-13 08:15:51','$2y$12$tmiCb4zps6d6yS3Xiz51meiBwnnDS2KNU48NkXHJpKAFYGamvHGoe',NULL,'superadmin',NULL,NULL,NULL,NULL,1,'-','-',NULL,'2026-06-11 06:18:35','2026-06-15 08:08:50'),(5,'Admin System','Ketua LSPro','admin@lspro.local','2026-06-13 08:15:51','$2y$12$qYxTfIRA69mmajuR.M2VGurzJpmFuKwzPtbqGs6fj/837xVjMpBje',NULL,'superadmin',NULL,NULL,NULL,NULL,0,'-',NULL,NULL,'2026-06-11 07:36:30','2026-06-15 02:19:55'),(6,'LSPro BRMP SDLP','Ketua LSPro','superadmin@lspro.local','2026-06-13 08:15:51','$2y$12$Vsls.erYEjRZFtnXOicSt.fflef8ZKgeSpU8jJa4xetxRbchU1.L6',NULL,'superadmin',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'2026-06-11 16:31:36','2026-06-15 08:20:03'),(7,'Divisi Tata Usaha','rakbun','tu@lspro.local','2026-06-13 08:15:51','$2y$12$e/pEv6sHJz2VSHLTNRYRcewk1EtWknG7ZGpkwTbZYvrhSzwzdYNuW',NULL,'admin','tatausaha',NULL,NULL,NULL,1,NULL,NULL,NULL,'2026-06-11 16:31:36','2026-06-15 01:32:27'),(8,'Divisi Layanan','ABEW','layanan@lspro.local','2026-06-13 08:15:51','$2y$12$e/pEv6sHJz2VSHLTNRYRcewk1EtWknG7ZGpkwTbZYvrhSzwzdYNuW',NULL,'admin','layanan',NULL,NULL,NULL,1,NULL,NULL,NULL,'2026-06-11 16:31:36','2026-06-15 01:27:49'),(9,'Divisi Audit','rakha','audit@lspro.local','2026-06-13 08:15:51','$2y$12$e/pEv6sHJz2VSHLTNRYRcewk1EtWknG7ZGpkwTbZYvrhSzwzdYNuW',NULL,'admin','audit',NULL,NULL,NULL,1,NULL,NULL,NULL,'2026-06-11 16:31:36','2026-06-15 01:43:41'),(10,'PT CAHAYA BUANA','rakha abew','client@lspro.local','2026-06-13 08:15:51','$2y$12$e/pEv6sHJz2VSHLTNRYRcewk1EtWknG7ZGpkwTbZYvrhSzwzdYNuW',NULL,'client',NULL,NULL,NULL,NULL,1,'08123456789','fsdffsd',NULL,'2026-06-11 16:31:36','2026-06-15 07:55:47');
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

-- Dump completed on 2026-06-18 13:24:34
