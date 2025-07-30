<?php
// includes/validators.php - Reusable validation classes

class FormValidator
{
    private $errors = [];
    private $data = [];

    public function __construct($postData)
    {
        $this->data = $postData;
    }

    // Generic validation method - more flexible approach
    public function validate($rules)
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $method = $rule['method'];
                $params = $rule['params'] ?? [];

                if (method_exists($this, $method)) {
                    call_user_func_array([$this, $method], array_merge([$field], $params));
                }
            }
        }

        return empty($this->errors);
    }

    // Convenience method for common validation patterns
    public function validateFields($fieldRules)
    {
        $this->errors = [];

        foreach ($fieldRules as $field => $rules) {
            if (isset($rules['required']) && $rules['required']) {
                $message = $rules['required_message'] ?? ucfirst(str_replace('_', ' ', $field)) . ' is required';
                $this->validateRequired($field, $message);
            }

            if (isset($rules['type'])) {
                switch ($rules['type']) {
                    case 'email':
                        $this->validateEmail($field);
                        break;
                    case 'username':
                        $this->validateUsername($field);
                        break;
                    case 'password':
                        $this->validatePassword($field);
                        break;
                    case 'name':
                        $this->validateName($field, $rules['label'] ?? ucfirst(str_replace('_', ' ', $field)), $rules['required'] ?? true);
                        break;
                    case 'date':
                        $this->validateDate($field);
                        break;
                    case 'phone':
                        $this->validatePhone($field);
                        break;
                    case 'numeric':
                        $this->validateNumeric($field, $rules['label'] ?? ucfirst(str_replace('_', ' ', $field)));
                        break;
                }
            }

            if (isset($rules['length'])) {
                $min = $rules['length']['min'] ?? 0;
                $max = $rules['length']['max'] ?? 255;
                $required = $rules['required'] ?? true;
                $label = $rules['label'] ?? ucfirst(str_replace('_', ' ', $field));
                $this->validateLength($field, $label, $min, $max, $required);
            }

            if (isset($rules['custom']) && is_callable($rules['custom'])) {
                $result = $rules['custom']($this->data[$field] ?? '');
                if ($result !== true) {
                    $this->errors[] = $result;
                }
            }
        }

        return empty($this->errors);
    }

    // Common validation methods
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
            $sanitized[$key] = $this->sanitize($value);
        }
        return $sanitized;
    }

    // Private validation methods
    private function validateRequired($field, $message)
    {
        if (empty(trim($this->data[$field] ?? ''))) {
            $this->errors[] = $message;
            return false;
        }
        return true;
    }

    private function validateEmail($field)
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Please enter a valid email address";
            return false;
        }
        return true;
    }

    private function validateUsername($field)
    {
        $username = $this->data[$field] ?? '';
        if (!empty($username)) {
            if (strlen($username) < 3) {
                $this->errors[] = "Username must be at least 3 characters long";
                return false;
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $this->errors[] = "Username can only contain letters, numbers, and underscores";
                return false;
            }
        }
        return true;
    }

    private function validatePassword($field)
    {
        $password = $this->data[$field] ?? '';
        if (!empty($password)) {
            if (strlen($password) < 8) {
                $this->errors[] = "Password must be at least 8 characters long";
                return false;
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
                $this->errors[] = "Password must contain at least one lowercase letter, one uppercase letter, and one number";
                return false;
            }
        }
        return true;
    }

    private function validateName($field, $fieldName, $required = true)
    {
        $value = $this->data[$field] ?? '';
        if (!$required && empty($value)) {
            return true;
        }

        if (!empty($value)) {
            if (strlen($value) < 2 || strlen($value) > 50) {
                $this->errors[] = "$fieldName must be between 2 and 50 characters";
                return false;
            }
            if (!preg_match('/^[a-zA-Z\s\'-\.]+$/', $value)) {
                $this->errors[] = "$fieldName can only contain letters, spaces, hyphens, apostrophes, and periods";
                return false;
            }
        }
        return true;
    }

    private function validateDate($field)
    {
        $date = $this->data[$field] ?? '';
        if (!empty($date)) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
                $this->errors[] = "Please enter a valid birthdate";
                return false;
            }

            $today = new DateTime();
            $age = $today->diff($dateObj)->y;

            if ($dateObj > $today) {
                $this->errors[] = "Birthdate cannot be in the future";
                return false;
            }

            if ($age > 100 || $age < 18) {
                $this->errors[] = "Please enter a valid birthdate (must be 18-100 years old)";
                return false;
            }
        }
        return true;
    }

    private function validatePhone($field)
    {
        $phone = $this->data[$field] ?? '';
        if (!empty($phone)) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
                $this->errors[] = "Please enter a valid phone number";
                return false;
            }
        }
        return true;
    }

    private function validateNumeric($field, $fieldName)
    {
        $value = $this->data[$field] ?? '';
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[] = "$fieldName must be a valid selection";
            return false;
        }
        return true;
    }

    private function validateLength($field, $fieldName, $min, $max, $required = true)
    {
        $value = $this->data[$field] ?? '';
        if (!$required && empty($value)) {
            return true;
        }

        if (!empty($value)) {
            $length = strlen($value);
            if ($length < $min || $length > $max) {
                $this->errors[] = "$fieldName must be between $min and $max characters";
                return false;
            }
        }
        return true;
    }

    private function validateStudentId($field)
    {
        $studentId = $this->data[$field] ?? '';
        if (!empty($studentId)) {
            // Adjust pattern based on your student ID format
            if (!preg_match('/^\d{4}-\d{4}$/', $studentId)) {
                $this->errors[] = "Student ID must be in format YYYY-NNNN";
                return false;
            }
        }
        return true;
    }

    private function sanitize($value)
    {
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

    public function validateUniqueEmail($email, $excludeUserId = null)
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

    public function validateUniqueUsername($username, $excludeUserId = null)
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

    public function validateUniqueStudentId($studentId, $excludeStudentId = null)
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

    public function validateDepartmentExists($departmentId)
    {
        return $this->db->exists('departments', ['id' => $departmentId, 'status' => 'active']);
    }

    public function validateUserExists($userId)
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
        // Simple rate limiting implementation
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
