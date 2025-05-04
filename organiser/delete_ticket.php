<?php
session_start();
include '../db.php';

if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['ticket_id'])) {
    echo "<div class='alert alert-danger'>No ticket selected for deletion.</div>";
    exit();
}

$ticket_id = $_GET['ticket_id'];
$organiser_id = $_SESSION['organiser_id'];

// Fetch the event ID associated with the ticket to validate ownership
$sql_ticket = "SELECT Ticket.Event_ID, Events.Organiser_ID 
               FROM Ticket 
               INNER JOIN Events ON Ticket.Event_ID = Events.Event_ID 
               WHERE Ticket.Ticket_ID = ?";
$stmt_ticket = $conn->prepare($sql_ticket);
$stmt_ticket->bind_param("i", $ticket_id);
$stmt_ticket->execute();
$result_ticket = $stmt_ticket->get_result();
$ticket = $result_ticket->fetch_assoc();

if (!$ticket || $ticket['Organiser_ID'] != $organiser_id) {
    echo "<div class='alert alert-danger'>Ticket not found or you do not have permission to delete this ticket.</div>";
    exit();
}

$event_id = $ticket['Event_ID']; // Get the associated event ID

// Delete the ticket
$sql_delete = "DELETE FROM Ticket WHERE Ticket_ID = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $ticket_id);

if ($stmt_delete->execute()) {
    header("Location: manage_tickets.php?event_id=$event_id&message=Ticket deleted successfully");
    exit();
} else {
    echo "<div class='alert alert-danger'>Error deleting ticket: " . $conn->error . "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Ticket</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Are you sure you want to delete this ticket?</h2>
    <form method="POST" action="">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="manage_tickets.php?event_id=<?php echo $event_id; ?>" class="btn btn-secondary">Back to Manage Tickets</a>
    </form>
</div>
</body>
</html>