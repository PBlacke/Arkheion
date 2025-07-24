USE arkheion;

CREATE TABLE
    IF NOT EXISTS `users` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM ('admin', 'faculty', 'student') NOT NULL,
        `status` ENUM ('active', 'inactive', 'pending') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE
    IF NOT EXISTS `faculty` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL UNIQUE,
        `first_name` VARCHAR(100) DEFAULT NULL,
        `middle_name` VARCHAR(100) DEFAULT NULL,
        `last_name` VARCHAR(100) DEFAULT NULL,
        `suffix` VARCHAR(20) DEFAULT NULL,
        `birthdate` DATE DEFAULT NULL,
        `address` TEXT DEFAULT NULL,
        `department` VARCHAR(255) NOT NULL,
        `employee_id` VARCHAR(255) NOT NULL UNIQUE,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
    IF NOT EXISTS `students` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL UNIQUE,
        `first_name` VARCHAR(50) NOT NULL,
        `middle_name` VARCHAR(50),
        `last_name` VARCHAR(50) NOT NULL,
        `suffix` VARCHAR(10),
        `date_of_birth` DATE NOT NULL,
        `address` TEXT NOT NULL,
        `educational_attainment` ENUM (
            'Elementary',
            'High School',
            'Senior High School',
            'College',
            'Masters',
            'Doctorate'
        ) NOT NULL,
        `department_id` INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (department_id) REFERENCES department (id)
    );

CREATE TABLE
    IF NOT EXISTS `pending_students` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `first_name` VARCHAR(50) NOT NULL,
        `middle_name` VARCHAR(50),
        `last_name` VARCHAR(50) NOT NULL,
        `suffix` VARCHAR(10),
        `date_of_birth` DATE NOT NULL,
        `address` TEXT NOT NULL,
        `educational_attainment` ENUM (
            'Elementary',
            'High School',
            'Senior High School',
            'College',
            'Masters',
            'Doctorate'
        ) NOT NULL,
        `department_id` INT NOT NULL,
        `status` ENUM ('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES department (id)
    );

CREATE TABLE
    IF NOT EXISTS `curriculum` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `department` varchar(255) NOT NULL,
        `curriculum` varchar(255) DEFAULT NULL,
        `status` varchar(255) DEFAULT 'default_value',
        `status2` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 51 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
    IF NOT EXISTS `department` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `department_name` varchar(255) NOT NULL,
        `status` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    );

CREATE TABLE
    IF NOT EXISTS `files` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `filename` varchar(255) NOT NULL,
        `file_path` varchar(255) NOT NULL,
        `uploader` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `department` varchar(100) NOT NULL,
        `status` enum ('Pending', 'Published', 'Rejected', 'Unpublish') NOT NULL DEFAULT 'Pending',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `year` varchar(10) DEFAULT NULL,
        `curriculum` varchar(100) DEFAULT NULL,
        `image` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
    IF NOT EXISTS `notifications` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `faculty_id` int (11) DEFAULT NULL,
        `user_id` int (11) DEFAULT NULL,
        `message` text NOT NULL,
        `type` varchar(50) NOT NULL,
        `reference_id` int (11) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT current_timestamp(),
        `is_read` tinyint (1) DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `faculty_id` (`faculty_id`),
        KEY `user_id` (`user_id`),
        CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
        CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
    );