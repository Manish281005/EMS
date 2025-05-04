<?php
session_start();
include '../db.php';

if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $organiser_id = $_SESSION['organiser_id'];

    // Ensure the event belongs to the logged-in organiser
    $sql = "DELETE FROM Events WHERE Event_ID = ? AND Organiser_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $event_id, $organiser_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?message=Event deleted successfully");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting event: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid event ID.</div>";
}
?>