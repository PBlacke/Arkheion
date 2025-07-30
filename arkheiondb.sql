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
    IF NOT EXISTS `departments` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `department_code` VARCHAR(10) NOT NULL UNIQUE, -- e.g., 'CS', 'IT', 'ENG'
        `department_name` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `head_faculty_id` INT DEFAULT NULL, -- Department head
        `status` ENUM ('active', 'inactive') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_department_code (department_code),
        INDEX idx_status (status)
    );

CREATE TABLE
    IF NOT EXISTS `faculty` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL UNIQUE,
        `first_name` VARCHAR(100) NOT NULL, -- Made required
        `middle_name` VARCHAR(100) DEFAULT NULL,
        `last_name` VARCHAR(100) NOT NULL, -- Made required
        `suffix` VARCHAR(20) DEFAULT NULL,
        `birthdate` DATE DEFAULT NULL,
        `address` TEXT DEFAULT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `department_id` INT NOT NULL, -- Changed to reference departments.id
        `position` VARCHAR(100) DEFAULT NULL, -- e.g., 'Professor', 'Associate Professor'
        `specialization` TEXT DEFAULT NULL,
        `hire_date` DATE DEFAULT NULL,
        `status` ENUM ('active', 'inactive', 'on_leave') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE RESTRICT,
        INDEX idx_department (department_id),
        INDEX idx_status (status)
    );

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
        FOREIGN KEY (department_id) REFERENCES departments (id)
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
        FOREIGN KEY (department_id) REFERENCES departments (id)
    );

CREATE TABLE
    IF NOT EXISTS `curricula` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `curriculum_code` VARCHAR(20) NOT NULL, -- e.g., 'CS-2024', 'IT-2023'
        `curriculum_name` VARCHAR(255) NOT NULL,
        `department_id` INT NOT NULL,
        `academic_year` VARCHAR(9) NOT NULL, -- e.g., '2023-2024'
        `total_units` INT DEFAULT 0,
        `duration_years` DECIMAL(2, 1) DEFAULT 4.0,
        `status` ENUM ('draft', 'active', 'archived', 'deprecated') DEFAULT 'draft',
        `effective_date` DATE,
        `created_by` INT NOT NULL, -- Faculty who created it
        `approved_by` INT DEFAULT NULL, -- Admin/Faculty who approved
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE RESTRICT,
        FOREIGN KEY (created_by) REFERENCES faculty (id) ON DELETE RESTRICT,
        FOREIGN KEY (approved_by) REFERENCES faculty (id) ON DELETE SET NULL,
        UNIQUE KEY unique_curriculum (department_id, curriculum_code),
        INDEX idx_department (department_id),
        INDEX idx_academic_year (academic_year),
        INDEX idx_status (status)
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
    );

CREATE TABLE
    IF NOT EXISTS `notifications` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `recipient_id` INT NOT NULL, -- Who receives the notification
        `sender_id` INT DEFAULT NULL, -- Who sent/triggered the notification
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `type` ENUM (
            'file_upload',
            'file_approved',
            'file_rejected',
            'curriculum_update',
            'account_approved',
            'account_rejected',
            'system',
            'reminder',
            'announcement'
        ) NOT NULL,
        `priority` ENUM ('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
        `reference_table` VARCHAR(50) DEFAULT NULL, -- e.g., 'files', 'curricula'
        `reference_id` INT DEFAULT NULL,
        `is_read` BOOLEAN DEFAULT FALSE,
        `read_at` TIMESTAMP NULL DEFAULT NULL,
        `expires_at` TIMESTAMP NULL DEFAULT NULL, -- For temporary notifications
        `action_url` VARCHAR(500) DEFAULT NULL, -- Deep link to relevant page
        `metadata` JSON DEFAULT NULL, -- Additional notification data
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recipient_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE SET NULL,
        INDEX idx_recipient (recipient_id),
        INDEX idx_type (type),
        INDEX idx_is_read (is_read),
        INDEX idx_created_at (created_at),
        INDEX idx_expires_at (expires_at)
    );

CREATE TABLE
    IF NOT EXISTS `file_access_logs` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `file_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `action` ENUM ('view', 'download', 'share') NOT NULL,
        `ip_address` VARCHAR(45) DEFAULT NULL,
        `user_agent` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (file_id) REFERENCES files (id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        INDEX idx_file_user (file_id, user_id),
        INDEX idx_created_at (created_at)
    );

-- Insert default admin account
-- Password is 'admin123' (hashed using PHP's password_hash function)
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `role`, `status`)
VALUES
    (
        'admin',
        'admin@arkheion.local',
        '$2y$10$8ez9D3Rxo8534qBbQRvG7.lnGNlWmT7CaAJNQaenEdvm3W0G4uA5i',
        'admin',
        'active'
    );

-- Optional: Insert some default departments
INSERT IGNORE INTO `departments` (
    `department_code`,
    `department_name`,
    `description`,
    `status`
)
VALUES
    (
        'CS',
        'Computer Science',
        'Department of Computer Science focusing on software development, algorithms, and computing theory',
        'active'
    ),
    (
        'IT',
        'Information Technology',
        'Department of Information Technology specializing in systems administration and IT infrastructure',
        'active'
    ),
    (
        'ENG',
        'Engineering',
        'Department of Engineering covering various engineering disciplines',
        'active'
    ),
    (
        'BA',
        'Business Administration',
        'Department of Business Administration for management and business studies',
        'active'
    ),
    (
        'ED',
        'Education',
        'Department of Education for teacher preparation and educational studies',
        'active'
    );