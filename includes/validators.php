<?php
// includes/validators.php - Simplified validation classes

class FormValidator
{
    private $errors = [];
    private $data = [];

    public function __construct($postData)
    {
        $this->data = $postData;
    }

    // Simple validation methods that can be chained
    public function validateEmail($field, $message = null)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = $message ?: "Please enter a valid email address";
        }
        return $this;
    }

    public function validateUsername($field, $message = null)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value)) {
            if (strlen($value) < 3) {
                $this->errors[] = $message ?: "Username must be at least 3 characters long";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
                $this->errors[] = $message ?: "Username can only contain letters, numbers, and underscores";
            }
        }
        return $this;
    }

    public function validatePassword($field, $message = null)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value)) {
            if (strlen($value) < 8) {
                $this->errors[] = $message ?: "Password must be at least 8 characters long";
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $value)) {
                $this->errors[] = $message ?: "Password must contain at least one lowercase letter, one uppercase letter, and one number";
            }
        }
        return $this;
    }

    public function validateName($field, $fieldName = null)
    {
        $value = $this->data[$field] ?? '';
        $fieldName = $fieldName ?: ucfirst(str_replace('_', ' ', $field));

        if (!empty($value)) {
            if (strlen($value) < 2 || strlen($value) > 50) {
                $this->errors[] = "$fieldName must be between 2 and 50 characters";
            } elseif (!preg_match('/^[a-zA-Z\s\'-\.]+$/', $value)) {
                $this->errors[] = "$fieldName can only contain letters, spaces, hyphens, apostrophes, and periods";
            }
        }
        return $this;
    }

    public function validateDate($field, $fieldName = null)
    {
        $value = $this->data[$field] ?? '';
        $fieldName = $fieldName ?: ucfirst(str_replace('_', ' ', $field));

        if (!empty($value)) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $value);
            if (!$dateObj || $dateObj->format('Y-m-d') !== $value) {
                $this->errors[] = "Please enter a valid $fieldName";
            } else {
                $today = new DateTime();
                $age = $today->diff($dateObj)->y;

                if ($dateObj > $today) {
                    $this->errors[] = "$fieldName cannot be in the future";
                } elseif ($age > 100 || $age < 18) {
                    $this->errors[] = "$fieldName must be valid (18-100 years old)";
                }
            }
        }
        return $this;
    }

    public function validatePhone($field, $message = null)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value)) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $value);
            if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
                $this->errors[] = $message ?: "Please enter a valid phone number";
            }
        }
        return $this;
    }

    public function validateLength($field, $min, $max, $fieldName = null)
    {
        $value = $this->data[$field] ?? '';
        $fieldName = $fieldName ?: ucfirst(str_replace('_', ' ', $field));

        if (!empty($value)) {
            $length = strlen($value);
            if ($length < $min || $length > $max) {
                $this->errors[] = "$fieldName must be between $min and $max characters";
            }
        }
        return $this;
    }

    public function validateNumeric($field, $fieldName = null)
    {
        $value = $this->data[$field] ?? '';
        $fieldName = $fieldName ?: ucfirst(str_replace('_', ' ', $field));

        if (!empty($value) && !is_numeric($value)) {
            $this->errors[] = "$fieldName must be a valid selection";
        }
        return $this;
    }

    public function validateStudentId($field, $message = null)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value)) {
            // Adjust pattern based on your student ID format
            if (!preg_match('/^\d{4}-\d{4}$/', $value)) {
                $this->errors[] = $message ?: "Student ID must be in format YYYY-NNNN";
            }
        }
        return $this;
    }

    // Custom validation with callback
    public function validateCustom($field, $callback, $message)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value) && !$callback($value)) {
            $this->errors[] = $message;
        }
        return $this;
    }

    // Check if validation passed
    public function isValid()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsAsString()
    {
        return implode('<br>', $this->errors);
    }

    public function getSanitizedData()
    {
        $sanitized = [];
        foreach ($this->data as $key => $value) {
            $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }

    // Get single sanitized field
    public function getSanitized($field)
    {
        $value = $this->data[$field] ?? '';
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}

class DatabaseValidator
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function isEmailUnique($email, $excludeUserId = null)
    {
        $conditions = ['email' => $email];
        if ($excludeUserId) {
            $users = $this->db->fetchAll('users', $conditions);
            foreach ($users as $user) {
                if ($user['id'] != $excludeUserId) {
                    return false;
                }
            }
        } else {
            return !$this->db->exists('users', $conditions);
        }
        return true;
    }

    public function isUsernameUnique($username, $excludeUserId = null)
    {
        $conditions = ['username' => $username];
        if ($excludeUserId) {
            $users = $this->db->fetchAll('users', $conditions);
            foreach ($users as $user) {
                if ($user['id'] != $excludeUserId) {
                    return false;
                }
            }
        } else {
            return !$this->db->exists('users', $conditions);
        }
        return true;
    }

    public function isStudentIdUnique($studentId, $excludeStudentId = null)
    {
        $conditions = ['student_id' => $studentId];
        if ($excludeStudentId) {
            $students = $this->db->fetchAll('students', $conditions);
            foreach ($students as $student) {
                if ($student['id'] != $excludeStudentId) {
                    return false;
                }
            }
        } else {
            return !$this->db->exists('students', $conditions);
        }
        return true;
    }

    public function departmentExists($departmentId)
    {
        return $this->db->exists('departments', ['id' => $departmentId, 'status' => 'active']);
    }

    public function userExists($userId)
    {
        return $this->db->exists('users', ['id' => $userId, 'status' => 'active']);
    }
}

class SecurityValidator
{
    public static function validateCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function generateCSRFToken()
    {
        return $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300)
    {
        $key = "rate_limit_" . $identifier;
        $attempts = $_SESSION[$key] ?? [];

        // Clean old attempts
        $attempts = array_filter($attempts, function ($time) use ($timeWindow) {
            return (time() - $time) < $timeWindow;
        });

        if (count($attempts) >= $maxAttempts) {
            return false;
        }

        $attempts[] = time();
        $_SESSION[$key] = $attempts;

        return true;
    }
}
