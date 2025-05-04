<?php
session_start();
include '../db.php';

if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "<div class='alert alert-danger'>No event selected for ticket creation.</div>";
    exit();
}

$event_id = $_GET['event_id'];
$organiser_id = $_SESSION['organiser_id'];

// Ensure the event belongs to the logged-in organiser
$sql_event = "SELECT * FROM Events WHERE Event_ID = ? AND Organiser_ID = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("ii", $event_id, $organiser_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();

if (!$event) {
    echo "<div class='alert alert-danger'>Event not found or you do not have permission to create tickets for this event.</div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_name = $_POST['ticket_name'];
    $ticket_price = $_POST['ticket_price'];
    $ticket_quantity = $_POST['ticket_quantity'];

    // Insert ticket into the database
    $sql_insert = "INSERT INTO Ticket (Event_ID, Ticket_Name, Ticket_Price, Ticket_Quantity) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("isdi", $event_id, $ticket_name, $ticket_price, $ticket_quantity);

    if ($stmt_insert->execute()) {
        header("Location: manage_tickets.php?event_id=$event_id&message=Ticket created successfully");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error creating ticket: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Ticket</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Create Ticket for Event: <?php echo $event['Event_Name']; ?></h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="ticket_name">Ticket Name:</label>
            <input type="text" class="form-control" id="ticket_name" name="ticket_name" required>
        </div>
        <div class="form-group">
            <label for="ticket_price">Ticket Price:</label>
            <input type="number" class="form-control" id="ticket_price" name="ticket_price" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="ticket_quantity">Ticket Quantity:</label>
            <input type="number" class="form-control" id="ticket_quantity" name="ticket_quantity" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Ticket</button>
        <a href="manage_tickets.php?event_id=<?php echo $event_id; ?>" class="btn btn-secondary">Back to Manage Tickets</a>
    </form>
</div>
</body>
</html>