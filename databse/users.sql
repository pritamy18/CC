-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2024 at 08:09 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blood_donation`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(8) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `dob` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `bloodType` varchar(3) NOT NULL,
  `lastdate` varchar(10) DEFAULT 'NIL',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullName`, `email`, `phone`, `dob`, `location`, `bloodType`, `lastdate`, `password`) VALUES
('USER0001', 'Rahul Sharma', 'rahul.sharma@gmail.com', '9876543210', '1990-01-01', 'Delhi', 'A+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0002', 'Sneha Patel', 'sneha.patel@gmail.com', '9876543211', '1992-02-02', 'Mumbai', 'A+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0003', 'Ajay Verma', 'ajay.verma@gmail.com', '9876543212', '1995-03-03', 'Bangalore', 'A+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0004', 'Neha Reddy', 'neha.reddy@gmail.com', '9876543213', '1991-04-04', 'Hyderabad', 'A+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0005', 'Vikram Singh', 'vikram.singh@gmail.com', '9876543214', '1989-05-05', 'Chennai', 'A+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0006', 'Pooja Gupta', 'pooja.gupta@gmail.com', '9876543215', '1993-06-06', 'Delhi', 'A-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0007', 'Karan Mehta', 'karan.mehta@gmail.com', '9876543216', '1994-07-07', 'Mumbai', 'A-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0008', 'Sita Joshi', 'sita.joshi@gmail.com', '9876543217', '1988-08-08', 'Bangalore', 'A-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0009', 'Amit Kumar', 'amit.kumar@gmail.com', '9876543218', '1992-09-09', 'Hyderabad', 'A-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0010', 'Riya Iyer', 'riya.iyer@gmail.com', '9876543219', '1990-10-10', 'Chennai', 'A-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0011', 'Sanjay Kapoor', 'sanjay.kapoor@gmail.com', '9876543220', '1987-11-11', 'Delhi', 'B+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0012', 'Anjali Bhat', 'anjali.bhat@gmail.com', '9876543221', '1993-12-12', 'Mumbai', 'B+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0013', 'Rohit Nair', 'rohit.nair@gmail.com', '9876543222', '1995-01-13', 'Bangalore', 'B+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0014', 'Divya Rani', 'divya.rani@gmail.com', '9876543223', '1989-02-14', 'Hyderabad', 'B+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0015', 'Kriti Sharma', 'kriti.sharma@gmail.com', '9876543224', '1991-03-15', 'Chennai', 'B+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0016', 'Arjun Choudhary', 'arjun.choudhary@gmail.com', '9876543225', '1986-04-16', 'Delhi', 'B-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0017', 'Lakshmi Nair', 'lakshmi.nair@gmail.com', '9876543226', '1995-05-17', 'Mumbai', 'B-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0018', 'Rahul Iyer', 'rahul.iyer@gmail.com', '9876543227', '1992-06-18', 'Bangalore', 'B-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0019', 'Anita Verma', 'anita.verma@gmail.com', '9876543228', '1990-07-19', 'Hyderabad', 'B-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0020', 'Vivek Sharma', 'vivek.sharma@gmail.com', '9876543229', '1989-08-20', 'Chennai', 'B-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0021', 'Neeraj Sethi', 'neeraj.sethi@gmail.com', '9876543230', '1988-04-16', 'Delhi', 'O+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0022', 'Tanvi Roy', 'tanvi.roy@gmail.com', '9876543231', '1995-06-18', 'Mumbai', 'O+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0023', 'Sumit Gupta', 'sumit.gupta@gmail.com', '9876543232', '1992-07-19', 'Bangalore', 'O+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0024', 'Prerna Singh', 'prerna.singh@gmail.com', '9876543233', '1991-08-20', 'Hyderabad', 'O+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0025', 'Karan Joshi', 'karan.joshi@gmail.com', '9876543234', '1989-09-21', 'Chennai', 'O+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0026', 'Aditi Malhotra', 'aditi.malhotra@gmail.com', '9876543235', '1987-05-24', 'Delhi', 'O-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0027', 'Vikrant Shetty', 'vikrant.shetty@gmail.com', '9876543236', '1990-03-15', 'Mumbai', 'O-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0028', 'Priti Mehta', 'priti.mehta@gmail.com', '9876543237', '1992-02-25', 'Bangalore', 'O-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0029', 'Suman Nair', 'suman.nair@gmail.com', '9876543238', '1991-01-18', 'Hyderabad', 'O-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0030', 'Anshul Sinha', 'anshul.sinha@gmail.com', '9876543239', '1989-11-30', 'Chennai', 'O-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0031', 'Ravi Kumar', 'ravi.kumar@gmail.com', '9876543240', '1990-12-31', 'Delhi', 'AB+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0032', 'Kajal Yadav', 'kajal.yadav@gmail.com', '9876543241', '1994-03-05', 'Mumbai', 'AB+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0033', 'Sameer Khan', 'sameer.khan@gmail.com', '9876543242', '1991-05-10', 'Bangalore', 'AB+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0034', 'Neelam Das', 'neelam.das@gmail.com', '9876543243', '1988-07-15', 'Hyderabad', 'AB+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0035', 'Aakash Gupta', 'aakash.gupta@gmail.com', '9876543244', '1986-08-20', 'Chennai', 'AB+', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0036', 'Tanya Singh', 'tanya.singh@gmail.com', '9876543245', '1992-09-25', 'Delhi', 'AB-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0037', 'Vikram Reddy', 'vikram.reddy@gmail.com', '9876543246', '1987-11-30', 'Mumbai', 'AB-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0038', 'Nisha Gupta', 'nisha.gupta@gmail.com', '9876543247', '1995-02-12', 'Bangalore', 'AB-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER0039', 'Rahul Mehta', 'rahul.mehta@gmail.com', '9876543248', '1990-03-22', 'Hyderabad', 'AB-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu'),
('USER9230', 'Priti Singh', 'priti.singh@gmail.com', '9876543249', '1988-04-05', 'Chennai', 'AB-', '2024-10-06', '$2y$10$g/Z0.DhKGw7rAuJI9oi4XueifjSLhhY4GltGQ1UnFbyhxvgE3kOTu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
