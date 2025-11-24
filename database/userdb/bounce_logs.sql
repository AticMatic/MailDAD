CREATE TABLE `bounce_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `runtime_message_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bounce_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status_code` text COLLATE utf8mb4_unicode_ci,
  `customer_id` int unsigned DEFAULT NULL,
  `tracking_log_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bounce_logs_customer_id_foreign` (`customer_id`),
  KEY `bounce_logs_tracking_log_id_foreign` (`tracking_log_id`),
  KEY `bounce_logs_message_id_bounce_type_index` (`message_id`,`bounce_type`),
  CONSTRAINT `bounce_logs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bounce_logs_tracking_log_id_foreign` FOREIGN KEY (`tracking_log_id`) REFERENCES `tracking_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
