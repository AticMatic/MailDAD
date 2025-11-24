CREATE TABLE `reply_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `from_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_date` timestamp NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reply_logs_message_id_foreign` (`message_id`),
  CONSTRAINT `reply_logs_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `tracking_logs` (`message_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
