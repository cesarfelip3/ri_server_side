-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 27, 2014 at 04:57 PM
-- Server version: 5.5.37
-- PHP Version: 5.4.4-14+deb7u9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `riapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `alert`
--

DROP TABLE IF EXISTS `todo`;
CREATE TABLE IF NOT EXISTS `todo` (
  `todo_id` int(11) NOT NULL AUTO_INCREMENT,
  `todo_uuid` varchar(255) NOT NULL DEFAULT '',
  `person_uuid` varchar(255) NOT NULL DEFAULT '',
  `user_uuid` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `user_info` varchar(255) NOT NULL DEFAULT '',
  `alarm` int(11) NOT NULL DEFAULT '0',
  `latency_start` int(11) NOT NULL DEFAULT '0',
  `latency_end` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`todo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_uuid` varchar(255) NOT NULL DEFAULT '',
  `person_uuid` varchar(255) NOT NULL DEFAULT '',
  `user_uuid` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `user_info` varchar(255) NOT NULL DEFAULT '',
  `alarm` int(11) NOT NULL DEFAULT '0',
  `latency_start` int(11) NOT NULL DEFAULT '0',
  `latency_end` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `person_id` int(20) NOT NULL AUTO_INCREMENT,
  `person_uuid` varchar(255) NOT NULL DEFAULT '',
  `user_uuid` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(32) NOT NULL DEFAULT '',
  `approved` int(11) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `modified_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `file`;
CREATE TABLE IF NOT EXISTS `file` (
  `file_id` int(20) NOT NULL AUTO_INCREMENT,
  `file_uuid` varchar(255) NOT NULL DEFAULT '',
  `user_uuid` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `mime` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `status` varchar(32) NOT NULL DEFAULT '',
  `approved` int(11) NOT NULL DEFAULT '0',
  `thumbnails` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `modified_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_uuid` varchar(255) NOT NULL DEFAULT '',
  `dev_token` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `firstname` varchar(255) NOT NULL DEFAULT '',
  `lastname` varchar(255) NOT NULL DEFAULT '',
  `fullname` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `longtitude` varchar(255) NOT NULL DEFAULT '',
  `latitude` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `create_date` int(20) NOT NULL DEFAULT '0',
  `modified_date` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
