<?php
session_start();
include '../db.php';

if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['ticket_id'])) {
    echo "<div class='alert alert-danger'>No ticket selected for editing.</div>";
    exit();
}

$ticket_id = $_GET['ticket_id'];
$organiser_id = $_SESSION['organiser_id'];

// Fetch ticket details and ensure it belongs to an event created by the logged-in organiser
$sql_ticket = "SELECT Ticket.*, Events.Event_Name, Events.Event_ID 
               FROM Ticket 
               INNER JOIN Events ON Ticket.Event_ID = Events.Event_ID 
               WHERE Ticket.Ticket_ID = ? AND Events.Organiser_ID = ?";
$stmt_ticket = $conn->prepare($sql_ticket);
$stmt_ticket->bind_param("ii", $ticket_id, $organiser_id);
$stmt_ticket->execute();
$result_ticket = $stmt_ticket->get_result();
$ticket = $result_ticket->fetch_assoc();

if (!$ticket) {
    echo "<div class='alert alert-danger'>Ticket not found or you do not have permission to edit this ticket.</div>";
    exit();
}

$event_id = $ticket['Event_ID']; // Get the associated event ID
$event_name = $ticket['Event_Name']; // Get the associated event name

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_name = $_POST['ticket_name'];
    $ticket_price = $_POST['ticket_price'];
    $ticket_quantity = $_POST['ticket_quantity'];

    // Update ticket details
    $sql_update = "UPDATE Ticket SET Ticket_Name = ?, Ticket_Price = ?, Ticket_Quantity = ? WHERE Ticket_ID = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sdii", $ticket_name, $ticket_price, $ticket_quantity, $ticket_id);

    if ($stmt_update->execute()) {
        header("Location: manage_tickets.php?event_id=$event_id&message=Ticket updated successfully");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating ticket: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Ticket</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Ticket for Event: <?php echo $event_name; ?></h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="ticket_name">Ticket Name:</label>
            <input type="text" class="form-control" id="ticket_name" name="ticket_name" value="<?php echo $ticket['Ticket_Name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="ticket_price">Ticket Price:</label>
            <input type="number" class="form-control" id="ticket_price" name="ticket_price" value="<?php echo $ticket['Ticket_Price']; ?>" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="ticket_quantity">Ticket Quantity:</label>
            <input type="number" class="form-control" id="ticket_quantity" name="ticket_quantity" value="<?php echo $ticket['Ticket_Quantity']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Ticket</button>
        <a href="manage_tickets.php?event_id=<?php echo $event_id; ?>" class="btn btn-secondary">Back to Manage Tickets</a>
    </form>
</div>
</body>
</html>