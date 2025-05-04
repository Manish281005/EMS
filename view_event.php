<?php
include 'db.php';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch event details
    $sql_event = "SELECT * FROM Events WHERE Event_ID = ?";
    $stmt_event = $conn->prepare($sql_event);
    $stmt_event->bind_param("i", $event_id);
    $stmt_event->execute();
    $event = $stmt_event->get_result()->fetch_assoc();

    // Fetch ticket details for the event
    $sql_tickets = "SELECT * FROM Ticket WHERE Event_ID = ?";
    $stmt_tickets = $conn->prepare($sql_tickets);
    $stmt_tickets->bind_param("i", $event_id);
    $stmt_tickets->execute();
    $tickets = $stmt_tickets->get_result();
} else {
    echo "Event not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2><?php echo $event['Event_Name']; ?></h2>
    <p><?php echo $event['Event_Details']; ?></p>
    <p><strong>Date & Time:</strong> <?php echo $event['Event_Time']; ?></p>
    <p><strong>Location:</strong> <?php echo $event['Event_Address'] . ', ' . $event['Event_City']; ?></p>

    <h3>Available Tickets</h3>
    <?php if ($tickets->num_rows > 0) { ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Price</th>
                    <th>Available Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ticket = $tickets->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $ticket['Ticket_ID']; ?></td>
                        <td>$<?php echo $ticket['Ticket_Price']; ?></td>
                        <td><?php echo $ticket['Ticket_Quantity']; ?></td>
                        <td>
                            <a href="user/book_ticket.php?event_id=<?php echo $event['Event_ID']; ?>" class="btn btn-primary">Book Ticket</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No tickets available for this event.</p>
    <?php } ?>
</div>
</body>
</html>