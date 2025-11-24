CREATE TABLE `feedback_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `runtime_message_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feedback_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw_feedback_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `tracking_log_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_logs_customer_id_foreign` (`customer_id`),
  KEY `feedback_logs_tracking_log_id_foreign` (`tracking_log_id`),
  CONSTRAINT `feedback_logs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `feedback_logs_tracking_log_id_foreign` FOREIGN KEY (`tracking_log_id`) REFERENCES `tracking_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
