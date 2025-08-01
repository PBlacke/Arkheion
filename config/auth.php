<?php
// auth.php - Hybrid version that accepts both mysqli connection and DatabaseHelper

function authenticateUser($connection, $username, $password)
{
    // Check if we received a DatabaseHelper instance or raw mysqli connection
    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        // Create DatabaseHelper from mysqli connection
        $db = new DatabaseHelper($connection);
    }

    // First check if user exists in pending_students
    $pendingStudent = $db->fetchOne('pending_students', ['username' => $username]);

    if ($pendingStudent) {
        if ($pendingStudent['status'] === 'Pending') {
            return ['success' => false, 'message' => 'Your registration is still pending approval.'];
        } else if ($pendingStudent['status'] === 'Rejected') {
            return ['success' => false, 'message' => 'Your registration has been rejected. Please contact your department for more information.'];
        }
    }

    // Check in main users table
    $user = $db->fetchOne('users', ['username' => $username]);

    if ($user) {
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
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }
}

function getUserProfile($connection, $user_id, $role)
{
    // Check if we received a DatabaseHelper instance or raw mysqli connection
    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        // Create DatabaseHelper from mysqli connection
        $db = new DatabaseHelper($connection);
    }

    switch ($role) {
        case 'student':
            return $db->getStudentByUserId($user_id);

        case 'faculty':
            $faculty = $db->getFaculty(['f.user_id' => $user_id]);
            return !empty($faculty) ? $faculty[0] : null;

        case 'admin':
            return $db->fetchById('users', $user_id);

        default:
            return null;
    }
}

function redirectToDashboard($role)
{
    switch ($role) {
        case 'admin':
            header("Location: ../admin/dashboard.php");
            break;
        case 'faculty':
            header("Location: ../faculty/dashboard.php");
            break;
        case 'student':
            header("Location: ../student/dashboard.php.php");
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
        header("Location: /Arkheion/index.php");
        exit();
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Helper function to get the currently logged-in user's data
function getLoggedInUser($connection)
{
    if (!isLoggedIn()) {
        return null;
    }

    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        $db = new DatabaseHelper($connection);
    }

    $user_id = $_SESSION['user_id'];
    $user = $db->fetchById('users', $user_id);

    // Verify user is still active
    if (!$user || $user['status'] !== 'active') {
        // If user is no longer active, clear session
        logout();
        return null;
    }

    return $user;
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

// Helper function to validate user exists and is active
function validateUser($connection, $user_id)
{
    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        $db = new DatabaseHelper($connection);
    }

    $user = $db->fetchById('users', $user_id);
    return $user && $user['status'] === 'active';
}

// Helper function to check if username exists (useful for registration)
function usernameExists($connection, $username)
{
    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        $db = new DatabaseHelper($connection);
    }

    return $db->exists('users', ['username' => $username]) ||
        $db->exists('pending_students', ['username' => $username]);
}

// Helper function to check if email exists (useful for registration)
function emailExists($connection, $email)
{
    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        $db = new DatabaseHelper($connection);
    }

    return $db->exists('users', ['email' => $email]) ||
        $db->exists('pending_students', ['email' => $email]);
}

// Helper function to get user by email
function getUserByEmail($connection, $email)
{
    if ($connection instanceof DatabaseHelper) {
        $db = $connection;
    } else {
        $db = new DatabaseHelper($connection);
    }

    return $db->fetchOne('users', ['email' => $email]);
}

// Helper function to update user last login timestamp
function updateLastLogin($connection, $user_id)
{
    // For this function, we need the raw mysqli connection
    if ($connection instanceof DatabaseHelper) {
        // If we got DatabaseHelper, we need access to the raw connection
        // This is a limitation - you might want to add a getConnection() method to DatabaseHelper
        global $conn;
        $mysqli = $conn;
    } else {
        $mysqli = $connection;
    }

    $stmt = $mysqli->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
