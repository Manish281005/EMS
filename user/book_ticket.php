<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "<div class='alert alert-danger'>No event selected for ticket booking.</div>";
    exit();
}

$event_id = $_GET['event_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $quantity = $_POST['quantity'];

    // Check ticket availability
    $sql = "SELECT Ticket_Quantity FROM Ticket WHERE Ticket_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket = $result->fetch_assoc();

    if ($ticket && $ticket['Ticket_Quantity'] >= $quantity) {
        // Reduce ticket quantity
        $sql_update = "UPDATE Ticket SET Ticket_Quantity = Ticket_Quantity - ? WHERE Ticket_ID = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $quantity, $ticket_id);

        if ($stmt_update->execute()) {
            // Store booked ticket in session
            if (!isset($_SESSION['booked_tickets'])) {
                $_SESSION['booked_tickets'] = [];
            }
            $_SESSION['booked_tickets'][] = [
                'ticket_id' => $ticket_id,
                'quantity' => $quantity
            ];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error updating ticket quantity: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Sorry, only " . $ticket['Ticket_Quantity'] . " ticket(s) are available for this event.</div>";
    }
}

// Fetch tickets for the selected event
$sql_tickets = "SELECT Ticket.Ticket_ID, Ticket.Ticket_Name, Ticket.Ticket_Quantity, Ticket.Ticket_Price 
                FROM Ticket 
                WHERE Ticket.Event_ID = ? AND Ticket.Ticket_Quantity > 0";
$stmt_tickets = $conn->prepare($sql_tickets);
$stmt_tickets->bind_param("i", $event_id);
$stmt_tickets->execute();
$result_tickets = $stmt_tickets->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Book Ticket for Event</h2>
    <?php if ($result_tickets->num_rows > 0) { ?>
        <table class="table table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Ticket Name</th>
                    <th>Price</th>
                    <th>Available Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_tickets->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['Ticket_Name']; ?></td>
                        <td>$<?php echo $row['Ticket_Price']; ?></td>
                        <td><?php echo $row['Ticket_Quantity']; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="ticket_id" value="<?php echo $row['Ticket_ID']; ?>">
                                <div class="form-group">
                                    <label for="quantity">Quantity:</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $row['Ticket_Quantity']; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Book Ticket(s)</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info">No tickets available for this event.</div>
    <?php } ?>
</div>
</body>
</html>