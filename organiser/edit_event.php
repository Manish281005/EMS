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

    // Fetch event details
    $sql = "SELECT * FROM Events WHERE Event_ID = ? AND Organiser_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $event_id, $organiser_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if (!$event) {
        echo "<div class='alert alert-danger'>Event not found or you do not have permission to edit this event.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>Invalid event ID.</div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_details = $_POST['event_details'];
    $event_address = $_POST['event_address'];
    $event_city = $_POST['event_city'];
    $event_time = $_POST['event_time'];

    // Update event details
    $sql_update = "UPDATE Events SET Event_Name = ?, Event_Details = ?, Event_Address = ?, Event_City = ?, Event_Time = ? WHERE Event_ID = ? AND Organiser_ID = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssii", $event_name, $event_details, $event_address, $event_city, $event_time, $event_id, $organiser_id);

    if ($stmt_update->execute()) {
        header("Location: dashboard.php?message=Event updated successfully");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating event: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Event</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="event_name">Event Name:</label>
            <input type="text" class="form-control" id="event_name" name="event_name" value="<?php echo $event['Event_Name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="event_details">Event Details:</label>
            <textarea class="form-control" id="event_details" name="event_details" required><?php echo $event['Event_Details']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="event_address">Event Address:</label>
            <input type="text" class="form-control" id="event_address" name="event_address" value="<?php echo $event['Event_Address']; ?>" required>
        </div>
        <div class="form-group">
            <label for="event_city">Event City:</label>
            <input type="text" class="form-control" id="event_city" name="event_city" value="<?php echo $event['Event_City']; ?>" required>
        </div>
        <div class="form-group">
            <label for="event_time">Event Date & Time:</label>
            <input type="datetime-local" class="form-control" id="event_time" name="event_time" value="<?php echo date('Y-m-d\TH:i', strtotime($event['Event_Time'])); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>