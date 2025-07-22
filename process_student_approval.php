<?php
session_start();
require 'connection.php';

// Check if user is logged in and is faculty
if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $action = $_POST['action'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get student information
        $get_student = $conn->prepare("SELECT * FROM pending_students WHERE id = ? AND status = 'Pending'");
        $get_student->bind_param("i", $student_id);
        $get_student->execute();
        $student = $get_student->get_result()->fetch_assoc();

        if (!$student) {
            throw new Exception('Student not found or already processed');
        }

        if ($action === 'approve') {
            // Insert into students table
            $insert = $conn->prepare("INSERT INTO students (username, password, email, first_name, middle_name, last_name, suffix, date_of_birth, address, educational_attainment, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("sssssssssss", 
                $student['username'],
                $student['password'],
                $student['email'],
                $student['first_name'],
                $student['middle_name'],
                $student['last_name'],
                $student['suffix'],
                $student['date_of_birth'],
                $student['address'],
                $student['educational_attainment'],
                $student['department']
            );
            $insert->execute();

            // Update status in pending_students
            $update = $conn->prepare("UPDATE pending_students SET status = 'Approved' WHERE id = ?");
            $update->bind_param("i", $student_id);
            $update->execute();

            // Send notification to student
            $notify = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'registration_approved')");
            $message = "Your registration has been approved. You can now login to your account.";
            $new_student_id = $insert->insert_id;
            $notify->bind_param("is", $new_student_id, $message);
            $notify->execute();

        } else if ($action === 'reject') {
            // Update status in pending_students
            $update = $conn->prepare("UPDATE pending_students SET status = 'Rejected' WHERE id = ?");
            $update->bind_param("i", $student_id);
            $update->execute();

            // Add rejection notification (optional)
            $notify = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'registration_rejected')");
            $message = "Your registration has been rejected. Please contact the department for more information.";
            $notify->bind_param("is", $student_id, $message);
            $notify->execute();
        } else {
            throw new Exception('Invalid action');
        }

        // Commit transaction
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Action completed successfully']);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    // Close statements
    if (isset($get_student)) $get_student->close();
    if (isset($insert)) $insert->close();
    if (isset($update)) $update->close();
    if (isset($notify)) $notify->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();