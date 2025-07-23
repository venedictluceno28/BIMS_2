-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 10, 2025 at 09:26 AM
-- Server version: 10.4.33-MariaDB-log
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bims`
--

-- --------------------------------------------------------

--
-- Table structure for table `resident_address`
--

CREATE TABLE `resident_address` (
  `address_ID` int(11) UNSIGNED NOT NULL COMMENT 'Primary Key',
  `res_ID` int(11) UNSIGNED DEFAULT NULL,
  `address_Unit_Room_Floor_num` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address building room/floor',
  `address_BuildingName` varchar(50) DEFAULT NULL COMMENT 'residence address  building name',
  `address_Lot_No` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address  lot no',
  `address_Block_No` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address block no',
  `address_Phase_No` varchar(5) DEFAULT NULL COMMENT 'residence address phase no/letter',
  `address_House_No` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address  house no',
  `address_Street_Name` varchar(100) DEFAULT NULL COMMENT 'residence address strees name',
  `address_Subdivision` varchar(100) DEFAULT NULL COMMENT 'residence address subdivision name',
  `country_ID` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address country_ID foreign key',
  `region_ID` int(11) UNSIGNED DEFAULT NULL,
  `province_ID` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address province_ID foreign key',
  `citymun_ID` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address cities_ID foreign key',
  `brgy_ID` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address barangay_ID foreign key',
  `purok_ID` int(11) UNSIGNED DEFAULT NULL,
  `addressType_ID` int(11) UNSIGNED DEFAULT NULL COMMENT 'residence address Type_ID foreign key'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resident_address`
--
ALTER TABLE `resident_address`
  ADD PRIMARY KEY (`address_ID`),
  ADD UNIQUE KEY `UK_resident_address_res_ID` (`res_ID`),
  ADD KEY `country_ID` (`country_ID`),
  ADD KEY `region_ID` (`region_ID`),
  ADD KEY `province_ID` (`province_ID`),
  ADD KEY `citymun_ID` (`citymun_ID`),
  ADD KEY `brgy_ID` (`brgy_ID`),
  ADD KEY `purok_ID` (`purok_ID`),
  ADD KEY `addressType_ID` (`addressType_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
