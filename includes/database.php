<?php
// includes/database.php - Reusable database functions

class DatabaseHelper
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Generic fetch functions
    public function fetchAll($table, $conditions = [], $orderBy = 'id ASC', $limit = null)
    {
        $sql = "SELECT * FROM $table";
        $params = [];
        $types = "";

        // Add WHERE conditions
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "$field = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        // Add ORDER BY
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // Add LIMIT
        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }

    public function fetchOne($table, $conditions = [])
    {
        $data = $this->fetchAll($table, $conditions, null, 1);
        return !empty($data) ? $data[0] : null;
    }

    public function fetchById($table, $id)
    {
        return $this->fetchOne($table, ['id' => $id]);
    }

    // Specific entity functions
    public function getStudents($filters = [])
    {
        $sql = "
            SELECT 
                s.*,
                u.username,
                u.email,
                u.status as account_status,
                d.department_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            JOIN department d ON s.department_id = d.id
        ";

        return $this->executeCustomQuery($sql, $filters);
    }

    public function getStudent($id)
    {
        $students = $this->getStudents(['s.id' => $id]);
        return !empty($students) ? $students[0] : null;
    }

    public function getStudentByUserId($user_id)
    {
        $students = $this->getStudents(['s.user_id' => $user_id]);
        return !empty($students) ? $students[0] : null;
    }

    public function getFaculty($filters = [])
    {
        $sql = "
            SELECT 
                f.*,
                u.username,
                u.email,
                u.status as account_status
            FROM faculty f
            JOIN users u ON f.user_id = u.id
        ";

        return $this->executeCustomQuery($sql, $filters);
    }

    public function getFacultyMember($id)
    {
        $faculty = $this->getFaculty(['f.id' => $id]);
        return !empty($faculty) ? $faculty[0] : null;
    }

    public function getFacultyByDepartment($department)
    {
        return $this->getFaculty(['f.department' => $department]);
    }

    public function getDepartments($active_only = true)
    {
        $conditions = $active_only ? ['status' => 'active'] : [];
        return $this->fetchAll('department', $conditions, 'department_name ASC');
    }

    public function getDepartment($id)
    {
        return $this->fetchById('department', $id);
    }

    public function getUsers($role = null)
    {
        $conditions = $role ? ['role' => $role] : [];
        return $this->fetchAll('users', $conditions, 'username ASC');
    }

    public function getFiles($filters = [])
    {
        $sql = "
            SELECT 
                f.*,
                d.department_name
            FROM files f
            LEFT JOIN department d ON f.department = d.department_name
        ";

        return $this->executeCustomQuery($sql, $filters);
    }

    public function getFilesByDepartment($department)
    {
        return $this->getFiles(['f.department' => $department]);
    }

    public function getFilesByStatus($status)
    {
        return $this->getFiles(['f.status' => $status]);
    }

    public function getPendingStudents($status = 'Pending')
    {
        $conditions = $status ? ['status' => $status] : [];
        return $this->fetchAll('pending_students', $conditions, 'registration_date DESC');
    }

    public function getNotifications($user_id = null, $faculty_id = null, $unread_only = false)
    {
        $conditions = [];
        if ($user_id) $conditions['user_id'] = $user_id;
        if ($faculty_id) $conditions['faculty_id'] = $faculty_id;
        if ($unread_only) $conditions['is_read'] = 0;

        return $this->fetchAll('notifications', $conditions, 'created_at DESC');
    }

    // Statistics functions
    public function getUserStats()
    {
        $stats = [];
        $stats['total_users'] = $this->count('users');
        $stats['total_students'] = $this->count('users', ['role' => 'student']);
        $stats['total_faculty'] = $this->count('users', ['role' => 'faculty']);
        $stats['total_admins'] = $this->count('users', ['role' => 'admin']);
        $stats['pending_students'] = $this->count('pending_students', ['status' => 'Pending']);

        return $stats;
    }

    public function getFileStats()
    {
        $stats = [];
        $stats['total_files'] = $this->count('files');
        $stats['published_files'] = $this->count('files', ['status' => 'Published']);
        $stats['pending_files'] = $this->count('files', ['status' => 'Pending']);
        $stats['rejected_files'] = $this->count('files', ['status' => 'Rejected']);

        return $stats;
    }

    // Utility functions
    public function count($table, $conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $params = [];
        $types = "";

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "$field = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();

        return $count;
    }

    public function exists($table, $conditions)
    {
        return $this->count($table, $conditions) > 0;
    }

    // Execute custom queries with filters
    private function executeCustomQuery($sql, $filters = [])
    {
        $params = [];
        $types = "";

        if (!empty($filters)) {
            $whereClause = [];
            foreach ($filters as $field => $value) {
                $whereClause[] = "$field = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }

    // Get parameter type for bind_param
    private function getParamType($value)
    {
        if (is_int($value)) return 'i';
        if (is_float($value)) return 'd';
        return 's'; // string by default
    }

    // Search functions
    public function search($table, $searchFields, $searchTerm, $conditions = [])
    {
        $sql = "SELECT * FROM $table";
        $params = [];
        $types = "";
        $whereClause = [];

        // Add search conditions
        if (!empty($searchFields) && !empty($searchTerm)) {
            $searchConditions = [];
            foreach ($searchFields as $field) {
                $searchConditions[] = "$field LIKE ?";
                $params[] = "%$searchTerm%";
                $types .= 's';
            }
            $whereClause[] = "(" . implode(" OR ", $searchConditions) . ")";
        }

        // Add additional conditions
        foreach ($conditions as $field => $value) {
            $whereClause[] = "$field = ?";
            $params[] = $value;
            $types .= $this->getParamType($value);
        }

        if (!empty($whereClause)) {
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }
}

// Initialize the helper (place this after your config/connection.php)
$db = new DatabaseHelper($conn);
