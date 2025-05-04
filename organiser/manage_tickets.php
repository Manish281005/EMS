<?php
session_start();
include '../db.php';

if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "<div class='alert alert-danger'>No event selected for ticket management.</div>";
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
    echo "<div class='alert alert-danger'>Event not found or you do not have permission to manage tickets for this event.</div>";
    exit();
}

// Fetch tickets for the event
$sql_tickets = "SELECT * FROM Ticket WHERE Event_ID = ?";
$stmt_tickets = $conn->prepare($sql_tickets);
$stmt_tickets->bind_param("i", $event_id);
$stmt_tickets->execute();
$result_tickets = $stmt_tickets->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Tickets</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Tickets for Event: <?php echo $event['Event_Name']; ?></h2>
    <div class="text-right mb-3">
        <a href="create_ticket.php?event_id=<?php echo $event_id; ?>" class="btn btn-success">+ Add Ticket</a>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($result_tickets->num_rows > 0) { ?>
        <table class="table table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Ticket Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ticket = $result_tickets->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $ticket['Ticket_Name']; ?></td>
                        <td>$<?php echo $ticket['Ticket_Price']; ?></td>
                        <td><?php echo $ticket['Ticket_Quantity']; ?></td>
                        <td>
                            <a href="edit_ticket.php?ticket_id=<?php echo $ticket['Ticket_ID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_ticket.php?ticket_id=<?php echo $ticket['Ticket_ID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info text-center">No tickets found for this event. Click "Add Ticket" to create one.</div>
    <?php } ?>
</div>
</body>
</html>