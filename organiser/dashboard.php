<?php
session_start();
if (!isset($_SESSION['organiser_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$organiser_id = $_SESSION['organiser_id'];

// Fetch all events created by the organiser
$sql_events = "SELECT Event_ID, Event_Name, Event_Time, Event_City 
               FROM Events 
               WHERE Organiser_ID = ?";
$stmt_events = $conn->prepare($sql_events);
$stmt_events->bind_param("i", $organiser_id);
$stmt_events->execute();
$result_events = $stmt_events->get_result();

// Fetch analysis data for each event
$event_analysis = [];
while ($event = $result_events->fetch_assoc()) {
    $event_id = $event['Event_ID'];

    // Fetch total tickets and tickets sold for the event
    $sql_analysis = "SELECT 
                        SUM(Ticket_Quantity) AS total_tickets, 
                        COUNT(Ticket_ID) AS tickets_sold, 
                        SUM(Ticket_Price) AS total_revenue 
                     FROM Ticket 
                     WHERE Event_ID = ?";
    $stmt_analysis = $conn->prepare($sql_analysis);
    $stmt_analysis->bind_param("i", $event_id);
    $stmt_analysis->execute();
    $result_analysis = $stmt_analysis->get_result()->fetch_assoc();

    $total_tickets = $result_analysis['total_tickets'] ?? 0;
    $tickets_sold = $result_analysis['tickets_sold'] ?? 0;
    $total_revenue = $result_analysis['total_revenue'] ?? 0.00;
    $remaining_tickets = $total_tickets - $tickets_sold;
    $percentage_sold = $total_tickets > 0 ? ($tickets_sold / $total_tickets) * 100 : 0;

    $event_analysis[] = [
        'event_id' => $event_id,
        'event_name' => $event['Event_Name'],
        'event_time' => $event['Event_Time'],
        'event_city' => $event['Event_City'],
        'total_tickets' => $total_tickets,
        'tickets_sold' => $tickets_sold,
        'remaining_tickets' => $remaining_tickets,
        'total_revenue' => $total_revenue,
        'percentage_sold' => $percentage_sold,
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organiser Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1; /* Pushes the footer to the bottom */
        }

        footer {
            background-color: #343a40; /* Dark background for footer */
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h5 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .progress {
            height: 20px;
            border-radius: 10px;
        }

        .progress-bar {
            font-size: 0.9rem;
            line-height: 20px;
        }

        .revenue {
            color: #28a745;
            font-weight: bold;
        }

        .tickets {
            color: #007bff;
            font-weight: bold;
            
        }

        .action-buttons .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Event Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="create_event.php">Create Event</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5 content">
    <h1 class="text-center">Organiser Dashboard</h1>
    <p class="text-center text-muted">Manage your events and view their performance</p>

    <div class="row mt-4">
        <?php if (!empty($event_analysis)) { ?>
            <?php foreach ($event_analysis as $event) { ?>
                <div class="col-md-6">
                    <div class="card">
                        <h5><?php echo $event['event_name']; ?></h5>
                        <p><strong>Event Date & Time:</strong> <?php echo date('Y-m-d H:i:s', strtotime($event['event_time'])); ?></p>
                        <p><strong>City:</strong> <?php echo $event['event_city']; ?></p>
                        <p><strong>Total Tickets:</strong> <?php echo $event['total_tickets']; ?></p>
                        <p><strong>Tickets Sold:</strong> <span class="tickets"><?php echo $event['tickets_sold']; ?></span></p>
                        <p><strong>Remaining Tickets:</strong> <?php echo $event['remaining_tickets']; ?></p>
                        <p><strong>Total Revenue:</strong> <span class="revenue">$<?php echo number_format($event['total_revenue'], 2); ?></span></p>
                        <p><strong>Percentage Sold:</strong> <?php echo round($event['percentage_sold'], 2); ?>%</p>

                        <!-- Progress Bar -->
                        <div class="progress mt-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $event['percentage_sold']; ?>%;" aria-valuenow="<?php echo $event['percentage_sold']; ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo round($event['percentage_sold'], 2); ?>%
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons mt-3">
                            <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_event.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                            <a href="manage_tickets.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-primary btn-sm">Manage Tickets</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-info text-center w-100">You have not created any events yet. Click "Create Event" to get started.</div>
        <?php } ?>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Event Management System. All Rights Reserved.</p>
</footer>
</body>
</html>