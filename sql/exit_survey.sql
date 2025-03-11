-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2025 at 08:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exit_survey`
--

-- --------------------------------------------------------

--
-- Table structure for table `accomplished_surveys`
--

CREATE TABLE `accomplished_surveys` (
  `user_id` int(10) NOT NULL,
  `survey_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `accomplished_surveys`
--

INSERT INTO `accomplished_surveys` (`user_id`, `survey_id`) VALUES
(103, 1804478097),
(104, 411072877),
(104, 2008138845),
(105, 218995551);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Program Learning Outcomes'),
(2, 'Curriculum Relevance'),
(3, 'Faculty Effectiveness'),
(4, 'Facilities and Resources'),
(5, 'Career Preparedness');

-- --------------------------------------------------------

--
-- Table structure for table `choices`
--

CREATE TABLE `choices` (
  `choice_id` int(10) NOT NULL,
  `choice` varchar(255) NOT NULL,
  `question_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `choices`
--

INSERT INTO `choices` (`choice_id`, `choice`, `question_id`) VALUES
(47305820, 'Moderately relevant', 1425599393),
(67929749, 'Git', 801941593),
(146929278, 'Poor', 2074096003),
(237504740, 'Fair', 2074096003),
(240477328, 'False', 449675313),
(446233548, 'Not relevant', 1425599393),
(552020126, 'Slightly relevant', 1425599393),
(554506182, 'Software Developer', 2137391232),
(672864798, 'Excellent', 2074096003),
(673633884, 'Accountant', 2137391232),
(721156968, 'Excellent', 1529694912),
(802967399, 'Very unsatisfied', 1122897997),
(806627966, 'Highly relevant', 1425599393),
(880333990, 'Satisfied', 1122897997),
(1162573194, 'Automation', 801941593),
(1233575620, 'Unsatisfied', 1122897997),
(1315093465, 'CI/CD', 801941593),
(1318464449, 'IT Help Desk', 2137391232),
(1352271351, 'False', 2050887102),
(1400534173, 'Good', 1529694912),
(1404775931, 'Fair', 1529694912),
(1508891011, 'Testing', 801941593),
(1548683070, 'Neutral', 1122897997),
(1607654933, 'Good', 2074096003),
(1634780162, 'True', 449675313),
(1878504522, 'Scientist', 2137391232),
(1946527536, 'Poor', 1529694912),
(2108572340, 'Very satisfied', 1122897997),
(2144156900, 'True', 2050887102);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_code` int(5) NOT NULL,
  `program_id` int(10) NOT NULL,
  `class_number` varchar(6) NOT NULL,
  `class_description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_code`, `program_id`, `class_number`, `class_description`) VALUES
(9372, 1, 'CS311', 'Applications Development'),
(9373, 1, 'CS312', 'Web Systems Development'),
(9400, 2, 'IT40', 'IT Solutions'),
(9401, 2, 'IT41', 'IT Solutions Lab'),
(9444, 2, 'IT111', 'Introduction to Information Technology'),
(9446, 2, 'IT113', 'Discrete Mathematics');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `email` varchar(255) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`email`, `attempts`, `last_attempt`) VALUES
('s4@email.com', 0, '2024-12-12 08:11:54'),
('student@email.com', 0, '2024-12-11 19:23:23');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(10) NOT NULL,
  `program_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_description`) VALUES
(1, 'Computer Science'),
(2, 'Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(10) NOT NULL,
  `survey_id` int(10) DEFAULT NULL,
  `category_id` int(10) NOT NULL,
  `question` text NOT NULL,
  `question_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `survey_id`, `category_id`, `question`, `question_type`) VALUES
(65051397, 218995551, 3, '[9400] [9401] What are your suggestions for this course?', 'short_answer'),
(449675313, 472495921, 4, '[9444] The faculty is very accommodating.', 'boolean'),
(776438674, 1314873646, 1, '[ALL] IT Enrolled |  Describe IT in  your own words.', 'short_answer'),
(801555012, 411072877, 1, '[9373] CS - 1Q | What did you learn?', 'short_answer'),
(801941593, 1804478097, 1, '[9372]  What is the importance of Applications Development?', 'multiple_choice'),
(803938406, 1314873646, 2, '[ALL] IT Enrolled | What do  you think about the relevance of this course?', 'short_answer'),
(1122897997, 2008138845, 3, '[ALL] CS Enrolled | How satisfied are your with this course?', 'satisfaction'),
(1425599393, 218995551, 1, '[9400] [9401] How relevant are the topics discussed in your desired career?', 'relevance'),
(1454126945, 472495921, 1, '[9444] What is your favorite subject?', 'short_answer'),
(1529694912, 1804478097, 3, '[9372]  How do you rate the faculty in this course?', 'quality'),
(1680403160, 1314873646, 5, '[ALL] IT Enrolled | Why should a company hire you?', 'short_answer'),
(1707375924, 1804478097, 5, '[9372] What is your name?', 'short_answer'),
(1936021295, 2008138845, 2, '[ALL] CS Enrolled | How relevant is the course for your future?', 'short_answer'),
(2050887102, 472495921, 5, '[9444] The color of this webpage is white.', 'boolean'),
(2074096003, 218995551, 4, '[9400] [9401] How excellent are the facilities used in class?', 'quality'),
(2137391232, 2008138845, 5, '[ALL] CS Enrolled | What is your desired professional path?', 'multiple_choice');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `user_id` int(10) NOT NULL,
  `question_id` int(10) NOT NULL,
  `choice_id` int(10) DEFAULT NULL,
  `short_answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`user_id`, `question_id`, `choice_id`, `short_answer`) VALUES
(103, 801941593, 1162573194, ''),
(103, 1529694912, 1400534173, ''),
(103, 1707375924, NULL, 'Hans'),
(104, 801555012, NULL, 'I learned alot'),
(104, 1122897997, 802967399, ''),
(104, 1936021295, NULL, 'It is very relevant'),
(104, 2137391232, 554506182, ''),
(105, 65051397, NULL, 'I dont have any'),
(105, 1425599393, 47305820, ''),
(105, 2074096003, 146929278, '');

-- --------------------------------------------------------

--
-- Table structure for table `restricted_surveys`
--

CREATE TABLE `restricted_surveys` (
  `survey_id` int(10) NOT NULL,
  `class_code` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restricted_surveys`
--

INSERT INTO `restricted_surveys` (`survey_id`, `class_code`) VALUES
(218995551, 9400),
(218995551, 9401),
(411072877, 9373),
(472495921, 9444),
(1804478097, 9372);

-- --------------------------------------------------------

--
-- Table structure for table `surveys`
--

CREATE TABLE `surveys` (
  `survey_name` varchar(255) NOT NULL,
  `survey_id` int(10) NOT NULL,
  `program_id` int(10) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  `is_closed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `surveys`
--

INSERT INTO `surveys` (`survey_name`, `survey_id`, `program_id`, `semester`, `academic_year`, `is_published`, `is_closed`) VALUES
('[9400] [9401]', 218995551, 2, 'Second Semester', '2026-2027', 1, 0),
('[9373] CS - 1Q', 411072877, 1, 'Second Semester', '2025-2026', 1, 0),
('[93444] IT Enrolled', 472495921, 2, 'Second Semester', '2027-2028', 1, 0),
('[ALL] IT Enrolled', 1314873646, 2, 'Second Semester', '2025-2026', 1, 0),
('[9372] CS Enrolled', 1804478097, 1, 'First Semester', '2027-2028', 1, 0),
('[ALL] CS Enrolled', 2008138845, 1, 'First Semester', '2024-2025', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_type`, `email`, `password`, `first_name`, `last_name`) VALUES
(101, 'student', 's1@email.com', 's1', 'Juan', 'Italia'),
(102, 'student', 's2@email.com', 's2', 'Too', 'Itogon'),
(103, 'student', 's3@email.com', 's3', 'Treese', 'Compadre'),
(104, 'student', 's4@email.com', 's4', 'Fourendo', 'Comare'),
(105, 'student', 's5@email.com', 's5', '9400', '9401'),
(901, 'admin', 'a1@email.com', 'a1', 'Uno', 'Adam'),
(902, 'admin', 'a2@email.com', 'a2', 'Dosia', 'Admina');

-- --------------------------------------------------------

--
-- Table structure for table `user_classes`
--

CREATE TABLE `user_classes` (
  `user_id` int(10) NOT NULL,
  `class_code` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_classes`
--

INSERT INTO `user_classes` (`user_id`, `class_code`) VALUES
(101, 9444),
(102, 9446),
(103, 9372),
(104, 9373),
(105, 9400),
(105, 9401);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accomplished_surveys`
--
ALTER TABLE `accomplished_surveys`
  ADD PRIMARY KEY (`user_id`,`survey_id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `choices`
--
ALTER TABLE `choices`
  ADD PRIMARY KEY (`choice_id`),
  ADD KEY `choices_ibfk_1` (`question_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_code`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `questions_ibfk_2` (`category_id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`user_id`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `restricted_surveys`
--
ALTER TABLE `restricted_surveys`
  ADD PRIMARY KEY (`survey_id`,`class_code`),
  ADD KEY `class_code` (`class_code`);

--
-- Indexes for table `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`survey_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_classes`
--
ALTER TABLE `user_classes`
  ADD PRIMARY KEY (`user_id`,`class_code`),
  ADD KEY `user_classes_ibfk_2` (`class_code`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accomplished_surveys`
--
ALTER TABLE `accomplished_surveys`
  ADD CONSTRAINT `accomplished_surveys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `accomplished_surveys_ibfk_2` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`survey_id`);

--
-- Constraints for table `choices`
--
ALTER TABLE `choices`
  ADD CONSTRAINT `choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `restricted_surveys`
--
ALTER TABLE `restricted_surveys`
  ADD CONSTRAINT `restricted_surveys_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`survey_id`),
  ADD CONSTRAINT `restricted_surveys_ibfk_2` FOREIGN KEY (`class_code`) REFERENCES `classes` (`class_code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `surveys`
--
ALTER TABLE `surveys`
  ADD CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`);

--
-- Constraints for table `user_classes`
--
ALTER TABLE `user_classes`
  ADD CONSTRAINT `user_classes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_classes_ibfk_2` FOREIGN KEY (`class_code`) REFERENCES `classes` (`class_code`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
