CREATE TABLE `unsubscribe_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_agent` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subscriber_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unsubscribe_logs_message_id_foreign` (`message_id`),
  KEY `unsubscribe_logs_subscriber_id_foreign` (`subscriber_id`),
  CONSTRAINT `unsubscribe_logs_subscriber_id_foreign` FOREIGN KEY (`subscriber_id`) REFERENCES `subscribers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
