-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 13, 2021 at 09:42 AM
-- Server version: 5.7.32
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `dbnu3test`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inventory`
--

CREATE TABLE `tbl_inventory` (
  `handle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'the assumption is the handle is unique per item',
  `location` varchar(85) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '85 can fit Taumatawhakatangihangakoauauotamateaturipukakapikimaungahoronukupokaiwhenuakitanatahu',
  `amount` float UNSIGNED NOT NULL DEFAULT '0' COMMENT 'to be checked why the values are in float'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'unsigned int should be enough for 200+ current products it has enough room for other new products',
  `prod_id` bigint(20) UNSIGNED NOT NULL,
  `prod_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prod_body_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `prod_vendor` varchar(85) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'this should have been a foreign key for an item in a different table if we expect a huge list of different vendors',
  `prod_type` varchar(85) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'this should have been a foreign key for an item in a different table if we expect a huge list of different types',
  `prod_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prod_handle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prod_published_scope` enum('web','mobile','offline','testing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'testing',
  `prod_tags` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `prod_img_id` bigint(20) NOT NULL COMMENT 'if we may have multiple images per product it must be in a different table but for now seems to have one image per product from the data',
  `prod_img_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prod_img_updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prod_img_width` smallint(5) UNSIGNED NOT NULL,
  `prod_img_height` smallint(5) UNSIGNED NOT NULL,
  `prod_img_src` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_inventory`
--
ALTER TABLE `tbl_inventory`
  ADD PRIMARY KEY (`handle`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id_index` (`prod_id`) USING HASH,
  ADD KEY `handle_foreign_key_index` (`prod_handle`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'unsigned int should be enough for 200+ current products it has enough room for other new products', AUTO_INCREMENT=83;
