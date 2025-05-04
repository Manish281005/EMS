<?php
session_start();
include '../db.php';

if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_details = $_POST['event_details'];
    $event_address = $_POST['event_address'];
    $event_city = $_POST['event_city'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $organiser_id = $_SESSION['organiser_id'];

    $sql = "INSERT INTO Events (Event_Name, Event_Details, Event_Address, Event_City, Event_Date, Event_Time, Organiser_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $event_name, $event_details, $event_address, $event_city, $event_date, $event_time, $organiser_id);

    if ($stmt->execute()) {
        echo "Event created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Create Event</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="event_name">Event Name:</label>
            <input type="text" class="form-control" id="event_name" name="event_name" required>
        </div>
        <div class="form-group">
            <label for="event_details">Event Details:</label>
            <textarea class="form-control" id="event_details" name="event_details" required></textarea>
        </div>
        <div class="form-group">
            <label for="event_address">Event Address:</label>
            <input type="text" class="form-control" id="event_address" name="event_address" required>
        </div>
        <div class="form-group">
            <label for="event_city">Event City:</label>
            <input type="text" class="form-control" id="event_city" name="event_city" required>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date:</label>
            <input type="date" class="form-control" id="event_date" name="event_date" required>
        </div>
        <div class="form-group">
            <label for="event_time">Event Time:</label>
            <input type="time" class="form-control" id="event_time" name="event_time" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Event</button>
    </form>
</div>
</body>
</html>