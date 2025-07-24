<?php
// auth.php - Central authentication functions

function authenticateUser($conn, $username, $password)
{
    // First check if user exists in pending_students
    $stmt = $conn->prepare("SELECT * FROM pending_students WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['status'] === 'Pending') {
            return ['success' => false, 'message' => 'Your registration is still pending approval.'];
        } else if ($row['status'] === 'Rejected') {
            return ['success' => false, 'message' => 'Your registration has been rejected. Please contact your department for more information.'];
        }
    }
    $stmt->close();

    // Check in main users table
    $stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();

        // Check if account is active
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account is not active. Please contact support.'];
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            return [
                'success' => true,
                'user' => $user,
                'message' => 'Login successful'
            ];
        } else {
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }
}

function getUserProfile($conn, $user_id, $role)
{
    switch ($role) {
        case 'student':
            $stmt = $conn->prepare("
                SELECT s.*, d.department_name, u.email, u.username 
                FROM students s 
                JOIN department d ON s.department_id = d.id 
                JOIN users u ON s.user_id = u.id 
                WHERE s.user_id = ?
            ");
            break;

        case 'faculty':
            $stmt = $conn->prepare("
                SELECT f.*, u.email, u.username 
                FROM faculty f 
                JOIN users u ON f.user_id = u.id 
                WHERE f.user_id = ?
            ");
            break;

        case 'admin':
            $stmt = $conn->prepare("
                SELECT u.id, u.username, u.email, u.role, u.created_at 
                FROM users u 
                WHERE u.id = ?
            ");
            break;

        default:
            return null;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();

    return $profile;
}

function redirectToDashboard($role)
{
    switch ($role) {
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
        case 'faculty':
            header("Location: faculty_dashboard.php");
            break;
        case 'student':
            header("Location: student_dashboard.php");
            break;
        default:
            header("Location: index.php");
    }
    exit();
}

function createUserSession($user, $profile = null)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['last_activity'] = time();

    // Store additional profile info if available
    if ($profile) {
        $_SESSION['profile'] = $profile;
    }
}

function requireRole($allowed_roles)
{
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: unauthorized.php");
        exit();
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function checkSessionTimeout($timeout = 1800)
{ // 30 minutes default
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

function logout()
{
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
