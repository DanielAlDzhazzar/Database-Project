-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 27, 2026 at 04:01 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banksys`
--

-- --------------------------------------------------------

--
-- Table structure for table `card`
--

CREATE TABLE `card` (
  `cardID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `cardHolder` varchar(50) NOT NULL,
  `cardNumber` varchar(20) DEFAULT NULL,
  `expiry` date DEFAULT NULL,
  `cvv` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `card`
--

INSERT INTO `card` (`cardID`, `customerID`, `cardHolder`, `cardNumber`, `expiry`, `cvv`) VALUES
(3, 2, 'Dani', '5544554455445544', '2039-01-01', '455');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pnum` int(13) NOT NULL,
  `email` varchar(50) NOT NULL,
  `passport` varchar(10) NOT NULL,
  `password` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerID`, `name`, `pnum`, `email`, `passport`, `password`) VALUES
(2, 'Daniel', 830813388, 'dan@gmail.com', 'GA123456', 'Gest1234'),
(3, 'John', 837573344, 'john@gmail.com', 'JN123456', 'John1234'),
(4, 'Pete', 830814456, 'pete@gmail.com', 'PE123456', 'Pete1234'),
(5, 'Josh', 812922222, 'josh@gmail.com', 'JH123456', 'Josh1234'),
(6, 'Danon', 983989546, 'danon@gmail.com', 'DN123456', 'Dano1234'),
(8, 'bob', 831331333, 'bob@gmail.com', 'BB123456', 'Bobb1234'),
(9, 'Kole', 931313344, 'kole@gmail.com', 'KL123456', 'Kile1234');

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `loanID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `interestRate` decimal(5,2) NOT NULL,
  `termMonths` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`loanID`, `customerID`, `amount`, `interestRate`, `termMonths`, `status`, `createdAt`) VALUES
(1, 2, '50000.00', '2.00', 24, 'active', '2026-04-27 01:19:28'),
(2, 2, '15000.00', '3.00', 12, 'active', '2026-04-27 01:21:51'),
(3, 2, '3000.00', '5.00', 6, 'paid', '2026-04-27 01:24:59'),
(4, 2, '41000.00', '3.00', 12, 'paid', '2026-04-27 02:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `paymentID` int(11) NOT NULL,
  `loanID` int(11) DEFAULT NULL,
  `amountPaid` decimal(10,2) DEFAULT NULL,
  `paidAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`paymentID`, `loanID`, `amountPaid`, `paidAt`) VALUES
(1, 3, '300.00', '2026-04-27 02:10:32'),
(2, 3, '200.00', '2026-04-27 02:12:17'),
(3, 3, '100.00', '2026-04-27 02:12:50'),
(4, 3, '150.00', '2026-04-27 02:13:14'),
(5, 3, '400.00', '2026-04-27 02:18:33'),
(6, 3, '2000.00', '2026-04-27 02:20:52'),
(7, 2, '450.00', '2026-04-27 02:23:26'),
(8, 2, '14999.00', '2026-04-27 02:44:07'),
(9, 4, '42230.00', '2026-04-27 02:53:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `card`
--
ALTER TABLE `card`
  ADD PRIMARY KEY (`cardID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `pnum` (`pnum`),
  ADD UNIQUE KEY `passport` (`passport`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`loanID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`paymentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `card`
--
ALTER TABLE `card`
  MODIFY `cardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `loanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
