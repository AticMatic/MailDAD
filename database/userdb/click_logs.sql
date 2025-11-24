CREATE TABLE `click_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `tracking_log_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `click_logs_message_id_foreign` (`message_id`),
  KEY `click_logs_customer_id_foreign` (`customer_id`),
  KEY `click_logs_tracking_log_id_foreign` (`tracking_log_id`),
  CONSTRAINT `click_logs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `click_logs_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `tracking_logs` (`message_id`) ON DELETE CASCADE,
  CONSTRAINT `click_logs_tracking_log_id_foreign` FOREIGN KEY (`tracking_log_id`) REFERENCES `tracking_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=533 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
