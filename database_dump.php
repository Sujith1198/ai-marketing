<?php

function get_ai_marketing_sql_dump() {
    return '-- AI Marketing Team Complete Production SQL Dump
-- Generated with AI Meetings & Product Scores

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `affiliate_clicks`;
CREATE TABLE `affiliate_clicks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tracking_code` varchar(64) NOT NULL,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `campaign_content_id` bigint(20) unsigned DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `utm_content` varchar(100) DEFAULT NULL,
  `device_type` varchar(30) DEFAULT NULL,
  `country` varchar(10) DEFAULT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_clicks_tracking_code_unique` (`tracking_code`),
  KEY `affiliate_clicks_campaign_id_foreign` (`campaign_id`),
  KEY `affiliate_clicks_product_id_foreign` (`product_id`),
  KEY `affiliate_clicks_campaign_content_id_foreign` (`campaign_content_id`),
  CONSTRAINT `affiliate_clicks_campaign_content_id_foreign` FOREIGN KEY (`campaign_content_id`) REFERENCES `campaign_contents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `affiliate_clicks_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `affiliate_clicks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `affiliate_networks`;
CREATE TABLE `affiliate_networks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `tracking_id` varchar(150) DEFAULT NULL,
  `affiliate_username` varchar(150) DEFAULT NULL,
  `portal_url` varchar(500) DEFAULT NULL,
  `credential_id` bigint(20) unsigned DEFAULT NULL,
  `capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`capabilities`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_networks_slug_unique` (`slug`),
  KEY `affiliate_networks_credential_id_foreign` (`credential_id`),
  CONSTRAINT `affiliate_networks_credential_id_foreign` FOREIGN KEY (`credential_id`) REFERENCES `api_credentials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `affiliate_networks` (`id`, `name`, `slug`, `driver`, `tracking_id`, `affiliate_username`, `portal_url`, `credential_id`, `capabilities`, `is_active`, `created_at`, `updated_at`) VALUES (\'1\', \'Amazon Associates\', \'amazon-associates\', \'amazon\', \'aimarketing-20\', \'amazon_affiliate_id\', \'https://affiliate-program.amazon.com\', NULL, \'[\\"product_search\\",\\"product_details\\",\\"affiliate_link_generation\\",\\"manual_import\\"]\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `affiliate_networks` (`id`, `name`, `slug`, `driver`, `tracking_id`, `affiliate_username`, `portal_url`, `credential_id`, `capabilities`, `is_active`, `created_at`, `updated_at`) VALUES (\'2\', \'Digistore24\', \'digistore24\', \'digistore24\', \'aimarketing\', \'digistore_affiliate_user\', \'https://www.digistore24.com\', NULL, \'[\\"product_search\\",\\"commission_data\\",\\"conversion_tracking\\",\\"manual_import\\"]\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `affiliate_networks` (`id`, `name`, `slug`, `driver`, `tracking_id`, `affiliate_username`, `portal_url`, `credential_id`, `capabilities`, `is_active`, `created_at`, `updated_at`) VALUES (\'3\', \'Hostinger Affiliate Program\', \'hostinger\', \'hostinger\', \'hostinger_ai\', \'hostinger_partner_user\', \'https://hpanel.hostinger.com/affiliate\', NULL, \'[\\"manual_product\\",\\"manual_affiliate_link\\",\\"manual_conversion\\"]\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `ai_agent_runs`;
CREATE TABLE `ai_agent_runs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ai_agent_id` bigint(20) unsigned NOT NULL,
  `ai_provider_id` bigint(20) unsigned DEFAULT NULL,
  `model_used` varchar(100) DEFAULT NULL,
  `prompt_reference` varchar(150) DEFAULT NULL,
  `input_hash` varchar(64) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'success\',
  `prompt_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `completion_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `estimated_cost` decimal(8,6) NOT NULL DEFAULT 0.000000,
  `response_summary` text DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_agent_runs_ai_agent_id_foreign` (`ai_agent_id`),
  KEY `ai_agent_runs_ai_provider_id_foreign` (`ai_provider_id`),
  CONSTRAINT `ai_agent_runs_ai_agent_id_foreign` FOREIGN KEY (`ai_agent_id`) REFERENCES `ai_agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_agent_runs_ai_provider_id_foreign` FOREIGN KEY (`ai_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'1\', \'2\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\', \'success\', \'87\', \'21\', \'0.000000\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'2\', \'3\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\', \'success\', \'125\', \'21\', \'0.000000\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'3\', \'4\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\', \'success\', \'155\', \'21\', \'0.000000\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'4\', \'5\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\', \'success\', \'176\', \'21\', \'0.000000\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'5\', \'6\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\', \'success\', \'215\', \'21\', \'0.000000\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'6\', \'7\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\', \'success\', \'236\', \'21\', \'0.000000\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'7\', \'2\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\', \'success\', \'87\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'8\', \'3\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\', \'success\', \'189\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'9\', \'4\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\', \'success\', \'284\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'10\', \'5\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\', \'success\', \'369\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'11\', \'6\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\', \'success\', \'472\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'12\', \'7\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\', \'success\', \'557\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'13\', \'2\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\', \'success\', \'87\', \'85\', \'0.000000\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited N\', NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'14\', \'3\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\', \'success\', \'189\', \'78\', \'0.000000\', \'### Target Audience & Pain Points\\n- **Demographics**: Startup Founders, CTOs, Agency Owners (Ages 25-45)\\n- **Pain Points**: High AWS/GCP cloud costs, complex server maintenance, lack of automated scaling.\\n- **Buyer Intent**: High commercial intent; search\', NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'15\', \'4\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\', \'success\', \'277\', \'74\', \'0.000000\', \'### Direct Response Ad Hooks & Headlines\\n- **Hook 1**: \\\'Stop Paying $500/mo for AWS — Host your AI App for 70% Less with Guaranteed 99.9% Uptime.\\\'\\n- **Email Subject**: \\\'How 450+ Tech Startups Scaled Their Cloud Infra in 2026\\\'\\n- **Call-To-Action**: \\\'Clai\', NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'16\', \'5\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\', \'success\', \'351\', \'75\', \'0.000000\', \'### Search Keyword Opportunities\\n- **Primary Keywords**: `best hostinger affiliate hosting`, `cheap ai server hosting for startups`\\n- **Long-Tail Focus**: `how to host python ai backend for cheap` (KD: 18, Search Vol: 4,200/mo)\\n- **Content Format**: In-de\', NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'17\', \'6\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\', \'success\', \'443\', \'88\', \'0.000000\', \'### Compliance & Disclosure Audit\\n- **FTC Disclosure Standard**: Requires clear top-of-page disclosure: *\\\'Affiliate Disclosure: We may earn a commission if you purchase through our links.\\\'*\\n- **Trademark Rules**: Do not bid on branded PPC search keywords.\', NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_agent_runs` (`id`, `ai_agent_id`, `ai_provider_id`, `model_used`, `prompt_reference`, `input_hash`, `started_at`, `completed_at`, `status`, `prompt_tokens`, `completion_tokens`, `estimated_cost`, `response_summary`, `error_message`, `created_at`, `updated_at`) VALUES (\'18\', \'7\', \'1\', \'gemini-1.5-flash\', NULL, NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\', \'success\', \'532\', \'69\', \'0.000000\', \'### Multi-Platform Content Distribution Plan\\n- **Instagram Reels / YouTube Shorts**: 30-sec speed test comparison video.\\n- **LinkedIn Carousel**: \\\'5 Cloud Hosting Mistakes Costing Startups $10k/Year\\\'.\\n- **Pinterest Pin**: Infographic on \\\'2026 SaaS Infrast\', NULL, \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');

DROP TABLE IF EXISTS `ai_agents`;
CREATE TABLE `ai_agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `system_prompt` text NOT NULL,
  `ai_provider_id` bigint(20) unsigned DEFAULT NULL,
  `model_override` varchar(100) DEFAULT NULL,
  `temperature` decimal(3,2) NOT NULL DEFAULT 0.70,
  `max_tokens` int(10) unsigned NOT NULL DEFAULT 2048,
  `priority` int(11) NOT NULL DEFAULT 10,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_agents_slug_unique` (`slug`),
  KEY `ai_agents_ai_provider_id_foreign` (`ai_provider_id`),
  CONSTRAINT `ai_agents_ai_provider_id_foreign` FOREIGN KEY (`ai_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'1\', \'Chief Marketing Officer Agent\', \'cmo-agent\', \'Chief Marketing Officer\', \'Synthesizes all marketing agent findings into actionable strategic recommendations.\', \'You are the Chief Marketing Officer (CMO). You lead the AI marketing team. Analyze inputs from Product Hunter, Copywriter, SEO, and Compliance agents to deliver high-converting strategic directives.\', \'1\', NULL, \'0.70\', \'2048\', \'1\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'2\', \'Product Hunter Agent\', \'product-hunter-agent\', \'Product Hunter\', \'Discovers high-demand, high-margin affiliate products across networks.\', \'You are a master Product Hunter. Evaluate product demand, market viability, commission structures, and buyer intent.\', \'1\', NULL, \'0.70\', \'2048\', \'2\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'3\', \'Market Research Agent\', \'market-research-agent\', \'Market Research Analyst\', \'Analyzes customer demographics, pain points, and competitor positioning.\', \'You are a Market Research Specialist. Map target audience segments, emotional pain points, and unique selling propositions.\', \'1\', NULL, \'0.70\', \'2048\', \'3\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'4\', \'Copywriter Agent\', \'copywriter-agent\', \'Direct Response Copywriter\', \'Crafts high-converting social media scripts, hooks, captions, and call-to-actions.\', \'You are a world-class Direct Response Copywriter. Create irresistible hooks, compelling problem-solution narratives, and subtle CTAs.\', \'1\', NULL, \'0.70\', \'2048\', \'4\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'5\', \'SEO Specialist Agent\', \'seo-specialist-agent\', \'SEO & Keyword Specialist\', \'Identifies low-competition, high-intent search keywords and Pinterest tags.\', \'You are an SEO & Pinterest Keyword Strategist. Discover high-converting search terms, hashtags, and pin titles.\', \'1\', NULL, \'0.70\', \'2048\', \'5\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'6\', \'Compliance Agent\', \'compliance-agent\', \'Affiliate Compliance Officer\', \'Ensures strict compliance with FTC disclosures, trademark guidelines, and ad policies.\', \'You are the Compliance Officer. Inspect marketing material for FTC affiliate disclosures, misleading health/financial claims, and platform policy violations.\', \'1\', NULL, \'0.70\', \'2048\', \'6\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_agents` (`id`, `name`, `slug`, `role`, `description`, `system_prompt`, `ai_provider_id`, `model_override`, `temperature`, `max_tokens`, `priority`, `is_enabled`, `created_at`, `updated_at`) VALUES (\'7\', \'Social Media Strategist Agent\', \'social-media-strategist-agent\', \'Social Media Director\', \'Optimizes posting schedules, visual formats, and short-form video hooks.\', \'You are the Social Media Strategist. Tailor content styles specifically for Instagram Reels, Facebook, Pinterest Pins, and YouTube Shorts.\', \'1\', NULL, \'0.70\', \'2048\', \'7\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `ai_providers`;
CREATE TABLE `ai_providers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `api_endpoint` varchar(255) DEFAULT NULL,
  `credential_id` bigint(20) unsigned DEFAULT NULL,
  `default_model` varchar(100) NOT NULL,
  `fallback_provider_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_providers_slug_unique` (`slug`),
  KEY `ai_providers_credential_id_foreign` (`credential_id`),
  CONSTRAINT `ai_providers_credential_id_foreign` FOREIGN KEY (`credential_id`) REFERENCES `api_credentials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_providers` (`id`, `name`, `slug`, `driver`, `api_endpoint`, `credential_id`, `default_model`, `fallback_provider_id`, `is_active`, `is_primary`, `settings`, `created_at`, `updated_at`) VALUES (\'1\', \'Google Gemini\', \'google-gemini\', \'gemini\', NULL, NULL, \'gemini-1.5-flash\', \'2\', \'1\', \'1\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_providers` (`id`, `name`, `slug`, `driver`, `api_endpoint`, `credential_id`, `default_model`, `fallback_provider_id`, `is_active`, `is_primary`, `settings`, `created_at`, `updated_at`) VALUES (\'2\', \'Groq (Ultra-Fast Llama)\', \'groq\', \'groq\', NULL, NULL, \'llama-3.3-70b-versatile\', NULL, \'1\', \'0\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `ai_providers` (`id`, `name`, `slug`, `driver`, `api_endpoint`, `credential_id`, `default_model`, `fallback_provider_id`, `is_active`, `is_primary`, `settings`, `created_at`, `updated_at`) VALUES (\'3\', \'OpenRouter Multi-Model\', \'openrouter\', \'openrouter\', NULL, NULL, \'meta-llama/llama-3.1-70b-instruct:free\', NULL, \'1\', \'0\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `ai_team_meetings`;
CREATE TABLE `ai_team_meetings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `user_query` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT \'completed\',
  `cmo_summary` text DEFAULT NULL,
  `final_recommendation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`final_recommendation`)),
  `confidence_score` int(10) unsigned NOT NULL DEFAULT 0,
  `recommended_action` varchar(100) DEFAULT NULL,
  `user_decision` varchar(50) NOT NULL DEFAULT \'pending\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_team_meetings` (`id`, `title`, `user_query`, `status`, `cmo_summary`, `final_recommendation`, `confidence_score`, `recommended_action`, `user_decision`, `created_at`, `updated_at`) VALUES (\'1\', \'AI Strategy Meeting: Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Aff\', \'Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Affiliate Campaign targeting Tech Startups with 70% recurring commission.\', \'completed\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', \'{\\"raw\\":\\"[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\\"}\', \'85\', \'CREATE_CAMPAIGN\', \'pending\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_meetings` (`id`, `title`, `user_query`, `status`, `cmo_summary`, `final_recommendation`, `confidence_score`, `recommended_action`, `user_decision`, `created_at`, `updated_at`) VALUES (\'2\', \'AI Strategy Meeting: Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Aff\', \'Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Affiliate Campaign targeting Tech Startups with 70% recurring commission.\', \'completed\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', \'{\\"raw\\":\\"### Product Analysis & Discovery\\\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\\\n- **Commission Target**: 70% Recurring Monthly Payout\\\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24\\\\/7 priority support for tech startups.\\"}\', \'85\', \'CREATE_CAMPAIGN\', \'pending\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_meetings` (`id`, `title`, `user_query`, `status`, `cmo_summary`, `final_recommendation`, `confidence_score`, `recommended_action`, `user_decision`, `created_at`, `updated_at`) VALUES (\'3\', \'AI Strategy Meeting: Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Aff\', \'Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Affiliate Campaign targeting Tech Startups with 70% recurring commission.\', \'completed\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', \'{\\"raw\\":\\"### Product Analysis & Discovery\\\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\\\n- **Commission Target**: 70% Recurring Monthly Payout\\\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24\\\\/7 priority support for tech startups.\\"}\', \'85\', \'CREATE_CAMPAIGN\', \'pending\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');

DROP TABLE IF EXISTS `ai_team_messages`;
CREATE TABLE `ai_team_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ai_team_meeting_id` bigint(20) unsigned NOT NULL,
  `ai_agent_id` bigint(20) unsigned DEFAULT NULL,
  `sender_type` varchar(20) NOT NULL,
  `agent_role` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `structured_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`structured_payload`)),
  `execution_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_team_messages_ai_team_meeting_id_foreign` (`ai_team_meeting_id`),
  KEY `ai_team_messages_ai_agent_id_foreign` (`ai_agent_id`),
  CONSTRAINT `ai_team_messages_ai_agent_id_foreign` FOREIGN KEY (`ai_agent_id`) REFERENCES `ai_agents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_team_messages_ai_team_meeting_id_foreign` FOREIGN KEY (`ai_team_meeting_id`) REFERENCES `ai_team_meetings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', NULL, \'user\', NULL, \'Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Affiliate Campaign targeting Tech Startups with 70% recurring commission.\', NULL, \'1\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'2\', \'1\', \'2\', \'agent\', \'Product Hunter\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'2\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'3\', \'1\', \'3\', \'agent\', \'Market Research Analyst\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'3\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'4\', \'1\', \'4\', \'agent\', \'Direct Response Copywriter\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'4\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'5\', \'1\', \'5\', \'agent\', \'SEO & Keyword Specialist\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'5\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'6\', \'1\', \'6\', \'agent\', \'Affiliate Compliance Officer\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'6\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'7\', \'1\', \'7\', \'agent\', \'Social Media Director\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', NULL, \'7\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'8\', \'1\', \'1\', \'agent\', \'Chief Marketing Officer\', \'[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\', \'{\\"raw\\":\\"[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)\\"}\', \'8\', \'2026-08-29 07:47:41\', \'2026-08-29 07:47:41\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'9\', \'2\', NULL, \'user\', NULL, \'Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Affiliate Campaign targeting Tech Startups with 70% recurring commission.\', NULL, \'1\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'10\', \'2\', \'2\', \'agent\', \'Product Hunter\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'2\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'11\', \'2\', \'3\', \'agent\', \'Market Research Analyst\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'3\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'12\', \'2\', \'4\', \'agent\', \'Direct Response Copywriter\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'4\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'13\', \'2\', \'5\', \'agent\', \'SEO & Keyword Specialist\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'5\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'14\', \'2\', \'6\', \'agent\', \'Affiliate Compliance Officer\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'6\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'15\', \'2\', \'7\', \'agent\', \'Social Media Director\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'7\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'16\', \'2\', \'1\', \'agent\', \'Chief Marketing Officer\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', \'{\\"raw\\":\\"### Product Analysis & Discovery\\\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\\\n- **Commission Target**: 70% Recurring Monthly Payout\\\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24\\\\/7 priority support for tech startups.\\"}\', \'8\', \'2026-08-29 07:49:35\', \'2026-08-29 07:49:35\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'17\', \'3\', NULL, \'user\', NULL, \'Design a complete Q4 High-Ticket AI Hosting & Cloud SaaS Affiliate Campaign targeting Tech Startups with 70% recurring commission.\', NULL, \'1\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'18\', \'3\', \'2\', \'agent\', \'Product Hunter\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', NULL, \'2\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'19\', \'3\', \'3\', \'agent\', \'Market Research Analyst\', \'### Target Audience & Pain Points\\n- **Demographics**: Startup Founders, CTOs, Agency Owners (Ages 25-45)\\n- **Pain Points**: High AWS/GCP cloud costs, complex server maintenance, lack of automated scaling.\\n- **Buyer Intent**: High commercial intent; searching for \\\'Cost-effective scalable AI hosting for startups\\\'.\', NULL, \'3\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'20\', \'3\', \'4\', \'agent\', \'Direct Response Copywriter\', \'### Direct Response Ad Hooks & Headlines\\n- **Hook 1**: \\\'Stop Paying $500/mo for AWS — Host your AI App for 70% Less with Guaranteed 99.9% Uptime.\\\'\\n- **Email Subject**: \\\'How 450+ Tech Startups Scaled Their Cloud Infra in 2026\\\'\\n- **Call-To-Action**: \\\'Claim 70% Exclusive Founder Discount Today ->\\\'\', NULL, \'4\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'21\', \'3\', \'5\', \'agent\', \'SEO & Keyword Specialist\', \'### Search Keyword Opportunities\\n- **Primary Keywords**: `best hostinger affiliate hosting`, `cheap ai server hosting for startups`\\n- **Long-Tail Focus**: `how to host python ai backend for cheap` (KD: 18, Search Vol: 4,200/mo)\\n- **Content Format**: In-depth comparison review & benchmark speed test.\', NULL, \'5\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'22\', \'3\', \'6\', \'agent\', \'Affiliate Compliance Officer\', \'### Compliance & Disclosure Audit\\n- **FTC Disclosure Standard**: Requires clear top-of-page disclosure: *\\\'Affiliate Disclosure: We may earn a commission if you purchase through our links.\\\'*\\n- **Trademark Rules**: Do not bid on branded PPC search keywords.\\n- **Claims Audit**: Ensure \\\'70% recurring commission\\\' is explicitly verified in affiliate terms.\', NULL, \'6\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'23\', \'3\', \'7\', \'agent\', \'Social Media Director\', \'### Multi-Platform Content Distribution Plan\\n- **Instagram Reels / YouTube Shorts**: 30-sec speed test comparison video.\\n- **LinkedIn Carousel**: \\\'5 Cloud Hosting Mistakes Costing Startups $10k/Year\\\'.\\n- **Pinterest Pin**: Infographic on \\\'2026 SaaS Infrastructure Cost Benchmark\\\'.\', NULL, \'7\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');
INSERT INTO `ai_team_messages` (`id`, `ai_team_meeting_id`, `ai_agent_id`, `sender_type`, `agent_role`, `content`, `structured_payload`, `execution_order`, `created_at`, `updated_at`) VALUES (\'24\', \'3\', \'1\', \'agent\', \'Chief Marketing Officer\', \'### Product Analysis & Discovery\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\n- **Commission Target**: 70% Recurring Monthly Payout\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.\', \'{\\"raw\\":\\"### Product Analysis & Discovery\\\\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\\\\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\\\\n- **Commission Target**: 70% Recurring Monthly Payout\\\\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24\\\\/7 priority support for tech startups.\\"}\', \'8\', \'2026-08-29 07:50:18\', \'2026-08-29 07:50:18\');

DROP TABLE IF EXISTS `analytics_snapshots`;
CREATE TABLE `analytics_snapshots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `snapshot_date` date NOT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `impressions` int(10) unsigned NOT NULL DEFAULT 0,
  `clicks` int(10) unsigned NOT NULL DEFAULT 0,
  `conversions` int(10) unsigned NOT NULL DEFAULT 0,
  `revenue` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ctr` decimal(5,2) NOT NULL DEFAULT 0.00,
  `conversion_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `analytics_snapshots_campaign_id_foreign` (`campaign_id`),
  KEY `analytics_snapshots_product_id_foreign` (`product_id`),
  CONSTRAINT `analytics_snapshots_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `analytics_snapshots_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `api_credentials`;
CREATE TABLE `api_credentials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(100) NOT NULL,
  `label` varchar(150) NOT NULL,
  `masked_value` varchar(100) NOT NULL,
  `encrypted_payload` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'active\',
  `last_tested_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `approvals`;
CREATE TABLE `approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approvable_type` varchar(100) NOT NULL,
  `approvable_id` bigint(20) unsigned NOT NULL,
  `approval_type` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'pending\',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `ai_confidence` int(10) unsigned NOT NULL DEFAULT 0,
  `risk_level` varchar(30) NOT NULL DEFAULT \'safe\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_reviewed_by_user_id_foreign` (`reviewed_by_user_id`),
  KEY `approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  CONSTRAINT `approvals_reviewed_by_user_id_foreign` FOREIGN KEY (`reviewed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `campaign_contents`;
CREATE TABLE `campaign_contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `platform` varchar(50) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body_text` text NOT NULL,
  `hook` text DEFAULT NULL,
  `call_to_action` text DEFAULT NULL,
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hashtags`)),
  `script` text DEFAULT NULL,
  `visual_concept` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'pending_approval\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_contents_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `campaign_contents_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `campaign_contents` (`id`, `campaign_id`, `platform`, `content_type`, `title`, `body_text`, `hook`, `call_to_action`, `hashtags`, `script`, `visual_concept`, `status`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', \'instagram\', \'reel\', \'Build a Website with AI in 60 Seconds\', \'Did you know you can build a complete WordPress website in under 60 seconds with AI?\\n\\nHostinger includes a free AI website builder, free domain, and SSL for just $2.99/month.\\n\\nComment \\\'HOST\\\' or click the link in bio to grab the 80% off discount!\', \'Stop paying web designers $1,000 when AI does it in 60 seconds.\', \'Click link in bio to get 80% OFF + free domain!\', \'[\\"#webdesign\\",\\"#aitools\\",\\"#hostinger\\",\\"#sidehustle2026\\"]\', \'Visual: Screen recording of Hostinger AI builder typing prompt \\\'Coffee shop website\\\'.\\nVoiceover: Watch AI build this entire site in 15 seconds. Domain included!\', \'Side-by-side video comparing manual coding vs AI website generator.\', \'pending_approval\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `campaign_strategies`;
CREATE TABLE `campaign_strategies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `customer_persona` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_persona`)),
  `emotional_motivations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`emotional_motivations`)),
  `awareness_stage` varchar(100) DEFAULT NULL,
  `content_pillars` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content_pillars`)),
  `primary_hooks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`primary_hooks`)),
  `secondary_hooks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`secondary_hooks`)),
  `cta_strategy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cta_strategy`)),
  `platform_strategy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`platform_strategy`)),
  `seo_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seo_keywords`)),
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hashtags`)),
  `objections_handling` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`objections_handling`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_strategies_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `campaign_strategies_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `campaign_strategies` (`id`, `campaign_id`, `customer_persona`, `emotional_motivations`, `awareness_stage`, `content_pillars`, `primary_hooks`, `secondary_hooks`, `cta_strategy`, `platform_strategy`, `seo_keywords`, `hashtags`, `objections_handling`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', \'{\\"age\\":\\"22-45\\",\\"interests\\":[\\"Side Hustles\\",\\"Web Design\\",\\"WordPress\\",\\"AI Tools\\"]}\', NULL, \'Problem Aware\', \'[\\"AI Tools Demo\\",\\"Hosting Cost Comparison\\",\\"Website Speed Benchmark\\"]\', \'[\\"I built a full business website in 3 minutes using AI (and it cost $2.99)\\",\\"Stop paying Webflow $29\\\\/mo when Hostinger gives you AI + Hosting for $2.99\\"]\', NULL, \'[\\"Use link in bio to claim 80% discount + free domain.\\"]\', NULL, NULL, \'[\\"#webdesign\\",\\"#sidehustle\\",\\"#wordpress\\",\\"#hostinger\\",\\"#aitools\\"]\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `affiliate_network_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `goal` varchar(150) DEFAULT NULL,
  `target_audience` text DEFAULT NULL,
  `marketing_angle` text DEFAULT NULL,
  `platforms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`platforms`)),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT \'draft\',
  `ai_strategy_summary` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaigns_slug_unique` (`slug`),
  KEY `campaigns_product_id_foreign` (`product_id`),
  KEY `campaigns_affiliate_network_id_foreign` (`affiliate_network_id`),
  CONSTRAINT `campaigns_affiliate_network_id_foreign` FOREIGN KEY (`affiliate_network_id`) REFERENCES `affiliate_networks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaigns_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `campaigns` (`id`, `product_id`, `affiliate_network_id`, `name`, `slug`, `goal`, `target_audience`, `marketing_angle`, `platforms`, `start_date`, `end_date`, `budget`, `status`, `ai_strategy_summary`, `notes`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', \'3\', \'Hostinger AI Website Builder Launch\', \'hostinger-ai-website-builder-launch\', \'Drive 100 new hosting signups via short-form video & Pinterest Pins.\', \'Aspiring entrepreneurs, freelancers, bloggers.\', \'Stop paying $50/mo for web hosting. Build a site in 5 mins with AI for $2.99/mo.\', \'[\\"instagram\\",\\"pinterest\\",\\"youtube\\"]\', \'2026-08-29\', \'2026-09-28\', \'0.00\', \'pending_approval\', \'Focus on short-form screen recordings showing site generation in under 60 seconds.\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `compliance_reviews`;
CREATE TABLE `compliance_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reviewable_type` varchar(100) NOT NULL,
  `reviewable_id` bigint(20) unsigned NOT NULL,
  `compliance_score` int(10) unsigned NOT NULL DEFAULT 100,
  `risk_level` varchar(30) NOT NULL DEFAULT \'safe\',
  `affiliate_disclosure_present` tinyint(1) NOT NULL DEFAULT 1,
  `issues_detected` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`issues_detected`)),
  `ai_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_reviews_reviewable_type_reviewable_id_index` (`reviewable_type`,`reviewable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `conversions`;
CREATE TABLE `conversions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_network_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `affiliate_click_id` bigint(20) unsigned DEFAULT NULL,
  `external_order_id` varchar(150) DEFAULT NULL,
  `conversion_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT \'USD\',
  `status` varchar(30) NOT NULL DEFAULT \'approved\',
  `converted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `conversion_source` varchar(30) NOT NULL DEFAULT \'api\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversions_affiliate_network_id_foreign` (`affiliate_network_id`),
  KEY `conversions_product_id_foreign` (`product_id`),
  KEY `conversions_campaign_id_foreign` (`campaign_id`),
  KEY `conversions_affiliate_click_id_foreign` (`affiliate_click_id`),
  CONSTRAINT `conversions_affiliate_click_id_foreign` FOREIGN KEY (`affiliate_click_id`) REFERENCES `affiliate_clicks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `conversions_affiliate_network_id_foreign` FOREIGN KEY (`affiliate_network_id`) REFERENCES `affiliate_networks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversions_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `conversions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `creative_prompts`;
CREATE TABLE `creative_prompts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `campaign_content_id` bigint(20) unsigned DEFAULT NULL,
  `platform` varchar(50) NOT NULL,
  `prompt_type` varchar(50) NOT NULL DEFAULT \'image\',
  `aspect_ratio` varchar(10) NOT NULL DEFAULT \'9:16\',
  `visual_style` varchar(100) DEFAULT NULL,
  `prompt_text` text NOT NULL,
  `suggested_text_overlay` varchar(255) DEFAULT NULL,
  `negative_prompt` text DEFAULT NULL,
  `recommended_tool` varchar(100) NOT NULL DEFAULT \'Midjourney / Flux / DALL-E\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `creative_prompts_campaign_id_foreign` (`campaign_id`),
  KEY `creative_prompts_campaign_content_id_foreign` (`campaign_content_id`),
  CONSTRAINT `creative_prompts_campaign_content_id_foreign` FOREIGN KEY (`campaign_content_id`) REFERENCES `campaign_contents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `creative_prompts_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `creative_prompts` (`id`, `campaign_id`, `campaign_content_id`, `platform`, `prompt_type`, `aspect_ratio`, `visual_style`, `prompt_text`, `suggested_text_overlay`, `negative_prompt`, `recommended_tool`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', \'1\', \'instagram\', \'image\', \'9:16\', \'Modern SaaS UI Mockup\', \'High tech workspace laptop screen displaying a sleek dark-mode AI website generator UI, vibrant purple and blue glow, futuristic desktop setup, 8k render.\', \'Build Websites 10x Faster with AI ($2.99/mo)\', NULL, \'Flux.1 / Midjourney v6\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `failed_jobs`;
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

DROP TABLE IF EXISTS `job_batches`;
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

DROP TABLE IF EXISTS `jobs`;
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

DROP TABLE IF EXISTS `marketing_memory`;
CREATE TABLE `marketing_memory` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `key_insight` varchar(255) NOT NULL,
  `insight_details` text NOT NULL,
  `confidence_level` int(10) unsigned NOT NULL DEFAULT 80,
  `source_campaign_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketing_memory_source_campaign_id_foreign` (`source_campaign_id`),
  CONSTRAINT `marketing_memory_source_campaign_id_foreign` FOREIGN KEY (`source_campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `marketing_memory` (`id`, `category`, `key_insight`, `insight_details`, `confidence_level`, `source_campaign_id`, `created_at`, `updated_at`) VALUES (\'1\', \'winning_hook\', \'Short-form screen recordings of AI website building generate 3x higher CTR than static graphics.\', \'Audience responds strongly to real-time timer overlays showing website creation speed.\', \'92\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `media_assets`;
CREATE TABLE `media_assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `media_type` varchar(30) NOT NULL DEFAULT \'image\',
  `platform` varchar(50) DEFAULT NULL,
  `aspect_ratio` varchar(10) DEFAULT NULL,
  `source` varchar(30) NOT NULL DEFAULT \'manual\',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_assets_campaign_id_foreign` (`campaign_id`),
  KEY `media_assets_product_id_foreign` (`product_id`),
  CONSTRAINT `media_assets_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `media_assets_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'1\', \'0001_01_01_000000_create_users_table\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'2\', \'0001_01_01_000001_create_cache_table\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'3\', \'0001_01_01_000002_create_jobs_table\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'4\', \'2026_01_01_000001_create_system_core_tables\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'5\', \'2026_01_01_000002_create_ai_engine_tables\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'6\', \'2026_01_01_000003_create_affiliate_and_product_tables\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'7\', \'2026_01_01_000004_create_campaign_and_content_tables\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'8\', \'2026_01_01_000005_create_social_and_scheduler_tables\', \'1\');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (\'9\', \'2026_01_01_000006_create_analytics_and_memory_tables\', \'1\');

DROP TABLE IF EXISTS `optimization_recommendations`;
CREATE TABLE `optimization_recommendations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `recommendation_type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'pending\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `optimization_recommendations_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `optimization_recommendations_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `product_analyses`;
CREATE TABLE `product_analyses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `market_demand` text DEFAULT NULL,
  `target_audience` text DEFAULT NULL,
  `pain_points` text DEFAULT NULL,
  `buyer_intent` text DEFAULT NULL,
  `problem_solved` text DEFAULT NULL,
  `emotional_triggers` text DEFAULT NULL,
  `competition_analysis` text DEFAULT NULL,
  `product_differentiation` text DEFAULT NULL,
  `pricing_attractiveness` text DEFAULT NULL,
  `commission_attractiveness` text DEFAULT NULL,
  `content_potential` text DEFAULT NULL,
  `viral_potential` text DEFAULT NULL,
  `seo_opportunity` text DEFAULT NULL,
  `social_media_fit` text DEFAULT NULL,
  `risk_factors` text DEFAULT NULL,
  `compliance_concerns` text DEFAULT NULL,
  `raw_ai_output` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_ai_output`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_analyses_product_id_foreign` (`product_id`),
  CONSTRAINT `product_analyses_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_analyses` (`id`, `product_id`, `market_demand`, `target_audience`, `pain_points`, `buyer_intent`, `problem_solved`, `emotional_triggers`, `competition_analysis`, `product_differentiation`, `pricing_attractiveness`, `commission_attractiveness`, `content_potential`, `viral_potential`, `seo_opportunity`, `social_media_fit`, `risk_factors`, `compliance_concerns`, `raw_ai_output`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', \'High global search volume for affordable WordPress hosting & website builders.\', \'Freelancers, small business owners, affiliate marketers, and web developers.\', \'High hosting costs at SiteGround/Bluehost, complicated setup, slow site load times.\', \'High intent buyers looking for renewal discounts and free domain bundle deals.\', \'Provides lightning-fast LiteSpeed web hosting at 80% lower cost.\', \'Frustration with expensive hosting renewals, desire to start online business easily.\', \'Moderate competition on YouTube & Blogs; high potential on Instagram Reels & Pinterest.\', NULL, NULL, NULL, \'Excellent for quick video tutorials, cost comparison infographics, and speed tests.\', \'High viral potential around \\"How to build a website in 10 minutes with AI\\".\', \'Low keyword difficulty for niche long-tail terms like \\"cheapest hosting with free SSL 2026\\".\', \'Ideal for Pinterest infographics and YouTube Shorts speed benchmarks.\', \'Standard refund window policies apply.\', \'Must disclose affiliate relationship clearly in captions.\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `product_scores`;
CREATE TABLE `product_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `demand_score` int(10) unsigned NOT NULL DEFAULT 0,
  `buyer_intent_score` int(10) unsigned NOT NULL DEFAULT 0,
  `competition_score` int(10) unsigned NOT NULL DEFAULT 0,
  `commission_score` int(10) unsigned NOT NULL DEFAULT 0,
  `content_potential_score` int(10) unsigned NOT NULL DEFAULT 0,
  `viral_potential_score` int(10) unsigned NOT NULL DEFAULT 0,
  `seo_potential_score` int(10) unsigned NOT NULL DEFAULT 0,
  `trust_score` int(10) unsigned NOT NULL DEFAULT 0,
  `social_fit_score` int(10) unsigned NOT NULL DEFAULT 0,
  `conversion_potential_score` int(10) unsigned NOT NULL DEFAULT 0,
  `risk_score` int(10) unsigned NOT NULL DEFAULT 0,
  `overall_opportunity_score` int(10) unsigned NOT NULL DEFAULT 0,
  `recommendation` varchar(50) NOT NULL DEFAULT \'TEST\',
  `score_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`score_breakdown`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_scores_product_id_foreign` (`product_id`),
  CONSTRAINT `product_scores_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_scores` (`id`, `product_id`, `demand_score`, `buyer_intent_score`, `competition_score`, `commission_score`, `content_potential_score`, `viral_potential_score`, `seo_potential_score`, `trust_score`, `social_fit_score`, `conversion_potential_score`, `risk_score`, `overall_opportunity_score`, `recommendation`, `score_breakdown`, `created_at`, `updated_at`) VALUES (\'1\', \'1\', \'90\', \'88\', \'45\', \'92\', \'88\', \'82\', \'85\', \'94\', \'90\', \'87\', \'10\', \'88\', \'STRONG_PROMOTE\', \'{\\"weighted_base\\":88.5,\\"penalties\\":0}\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_network_id` bigint(20) unsigned NOT NULL,
  `external_product_id` varchar(150) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `product_url` varchar(500) NOT NULL,
  `affiliate_url` varchar(500) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT \'USD\',
  `commission_type` varchar(30) NOT NULL DEFAULT \'percentage\',
  `commission_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'draft\',
  `source` varchar(30) NOT NULL DEFAULT \'manual\',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_affiliate_network_id_foreign` (`affiliate_network_id`),
  CONSTRAINT `products_affiliate_network_id_foreign` FOREIGN KEY (`affiliate_network_id`) REFERENCES `affiliate_networks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `affiliate_network_id`, `external_product_id`, `name`, `slug`, `category`, `brand`, `description`, `product_url`, `affiliate_url`, `image_url`, `price`, `currency`, `commission_type`, `commission_value`, `commission_notes`, `status`, `source`, `metadata`, `created_at`, `updated_at`) VALUES (\'1\', \'3\', \'HST-PREM-01\', \'Hostinger Premium Web Hosting\', \'hostinger-premium-web-hosting\', \'Web Hosting & SaaS\', \'Hostinger\', \'Fast, secure, and affordable WordPress web hosting with free domain, SSL, and AI website builder.\', \'https://www.hostinger.com/web-hosting\', \'https://www.hostinger.com/web-hosting?referral=aimarketing\', \'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&q=80\', \'2.99\', \'USD\', \'percentage\', \'60.00\', \'60% baseline commission per customer subscription.\', \'active\', \'manual\', NULL, \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `prompt_templates`;
CREATE TABLE `prompt_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ai_agent_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `current_version` int(10) unsigned NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prompt_templates_slug_unique` (`slug`),
  KEY `prompt_templates_ai_agent_id_foreign` (`ai_agent_id`),
  CONSTRAINT `prompt_templates_ai_agent_id_foreign` FOREIGN KEY (`ai_agent_id`) REFERENCES `ai_agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prompt_versions`;
CREATE TABLE `prompt_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prompt_template_id` bigint(20) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `prompt_text` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'active\',
  `created_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prompt_versions_prompt_template_id_foreign` (`prompt_template_id`),
  KEY `prompt_versions_created_by_user_id_foreign` (`created_by_user_id`),
  CONSTRAINT `prompt_versions_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `prompt_versions_prompt_template_id_foreign` FOREIGN KEY (`prompt_template_id`) REFERENCES `prompt_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `published_posts`;
CREATE TABLE `published_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scheduled_post_id` bigint(20) unsigned NOT NULL,
  `platform` varchar(50) NOT NULL,
  `external_post_id` varchar(255) NOT NULL,
  `post_url` varchar(500) DEFAULT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metrics`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `published_posts_scheduled_post_id_foreign` (`scheduled_post_id`),
  CONSTRAINT `published_posts_scheduled_post_id_foreign` FOREIGN KEY (`scheduled_post_id`) REFERENCES `scheduled_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `scheduled_posts`;
CREATE TABLE `scheduled_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `campaign_content_id` bigint(20) unsigned NOT NULL,
  `social_account_id` bigint(20) unsigned DEFAULT NULL,
  `media_asset_id` bigint(20) unsigned DEFAULT NULL,
  `platform` varchar(50) NOT NULL,
  `scheduled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `timezone` varchar(50) NOT NULL DEFAULT \'UTC\',
  `status` varchar(30) NOT NULL DEFAULT \'scheduled\',
  `attempts` int(10) unsigned NOT NULL DEFAULT 0,
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `external_post_id` varchar(255) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scheduled_posts_campaign_id_foreign` (`campaign_id`),
  KEY `scheduled_posts_campaign_content_id_foreign` (`campaign_content_id`),
  KEY `scheduled_posts_social_account_id_foreign` (`social_account_id`),
  KEY `scheduled_posts_media_asset_id_foreign` (`media_asset_id`),
  CONSTRAINT `scheduled_posts_campaign_content_id_foreign` FOREIGN KEY (`campaign_content_id`) REFERENCES `campaign_contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scheduled_posts_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scheduled_posts_media_asset_id_foreign` FOREIGN KEY (`media_asset_id`) REFERENCES `media_assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_posts_social_account_id_foreign` FOREIGN KEY (`social_account_id`) REFERENCES `social_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
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

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES (\'1395R0IYO0dd0k7C3QzjwLXJBOHXtcLXcGIxpBEN\', \'1\', \'127.0.0.1\', \'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36\', \'YTo1OntzOjY6Il90b2tlbiI7czo0MDoieGpOM0hzQjE3Q3NDS21LN2MzOWYyRnBMeUlseW5ndW9HelpVSU55NSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWktdGVhbS9jaGF0IjtzOjU6InJvdXRlIjtzOjEyOiJhaS10ZWFtLmNoYXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=\', \'1787990321\');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES (\'gV5qJKUFwEZxe043t5UDOr7ACvx7dabvKcREoVw4\', \'1\', \'127.0.0.1\', \'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36\', \'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOUY4ODhLSzlDajJUY2pDUTIxamFqQmloUzRmWXZVVzM4STlabDZFNyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==\', \'1787985007\');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES (\'rNVVxANvvWIBgCu7r0WI4QqD5YdmdbMT3uzkqWuE\', NULL, \'127.0.0.1\', \'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.19041.6456\', \'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFJHY3hMVkFaMWRuWHp3NW8xRUpvdFdRNjRqcEFOVXdtYWxIc1laYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==\', \'1787984026\');

DROP TABLE IF EXISTS `social_accounts`;
CREATE TABLE `social_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `social_platform_id` bigint(20) unsigned NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_id` varchar(150) DEFAULT NULL,
  `credential_id` bigint(20) unsigned DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT \'connected\',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_accounts_social_platform_id_foreign` (`social_platform_id`),
  KEY `social_accounts_credential_id_foreign` (`credential_id`),
  CONSTRAINT `social_accounts_credential_id_foreign` FOREIGN KEY (`credential_id`) REFERENCES `api_credentials` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_accounts_social_platform_id_foreign` FOREIGN KEY (`social_platform_id`) REFERENCES `social_platforms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `social_platforms`;
CREATE TABLE `social_platforms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `oauth_supported` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_platforms_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `social_platforms` (`id`, `name`, `slug`, `oauth_supported`, `is_active`, `created_at`, `updated_at`) VALUES (\'1\', \'Instagram\', \'instagram\', \'1\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `social_platforms` (`id`, `name`, `slug`, `oauth_supported`, `is_active`, `created_at`, `updated_at`) VALUES (\'2\', \'Facebook\', \'facebook\', \'1\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `social_platforms` (`id`, `name`, `slug`, `oauth_supported`, `is_active`, `created_at`, `updated_at`) VALUES (\'3\', \'Pinterest\', \'pinterest\', \'1\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `social_platforms` (`id`, `name`, `slug`, `oauth_supported`, `is_active`, `created_at`, `updated_at`) VALUES (\'4\', \'YouTube\', \'youtube\', \'1\', \'1\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT \'string\',
  `group_name` varchar(50) NOT NULL DEFAULT \'general\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group_name`, `created_at`, `updated_at`) VALUES (\'1\', \'app_name\', \'AI Marketing Team\', \'string\', \'general\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group_name`, `created_at`, `updated_at`) VALUES (\'2\', \'require_human_approval\', \'true\', \'string\', \'security\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group_name`, `created_at`, `updated_at`) VALUES (\'3\', \'default_currency\', \'USD\', \'string\', \'general\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group_name`, `created_at`, `updated_at`) VALUES (\'4\', \'default_timezone\', \'UTC\', \'string\', \'general\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');
INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group_name`, `created_at`, `updated_at`) VALUES (\'5\', \'default_disclosure\', \'Disclosure: This post contains affiliate links. We may earn a commission if you make a purchase through these links at no extra cost to you.\', \'string\', \'compliance\', \'2026-08-29 06:13:25\', \'2026-08-29 06:13:25\');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT \'ceo\',
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `two_factor_secret`, `two_factor_enabled`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES (\'1\', \'CEO Admin\', \'ceo@aimarketing.test\', NULL, \'$2y$12$SQxupTqIQlncnKUg9rmVDesLn7N4y6gICszLF/2uMbbsACdjQX3FG\', \'ceo\', NULL, \'0\', \'1\', NULL, \'2026-08-29 06:13:24\', \'2026-08-29 06:13:24\');

SET FOREIGN_KEY_CHECKS=1;
';
}
