<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Fetch all events with organiser names
$sql_events = "SELECT Events.Event_ID, Events.Event_Name, Events.Event_Details, Events.Event_Address, 
                      Events.Event_City, Events.Event_Time, Organiser.Organiser_Name 
               FROM Events 
               INNER JOIN Organiser ON Events.Organiser_ID = Organiser.Organiser_ID";
$result_events = $conn->query($sql_events);

// Fetch booked tickets from session
$booked_tickets = isset($_SESSION['booked_tickets']) ? $_SESSION['booked_tickets'] : [];
$booked_tickets_data = [];

// Ensure $booked_tickets is an array
if (!is_array($booked_tickets)) {
    $booked_tickets = [];
}

if (!empty($booked_tickets)) {
    $ticket_ids = array_column($booked_tickets, 'ticket_id');
    $placeholders = implode(',', array_fill(0, count($ticket_ids), '?'));
    $sql_booked_tickets = "SELECT Ticket.Ticket_ID, Ticket.Ticket_Name, Ticket.Ticket_Price, 
                                  Events.Event_Name, Events.Event_Time, Organiser.Organiser_Name 
                           FROM Ticket 
                           INNER JOIN Events ON Ticket.Event_ID = Events.Event_ID 
                           INNER JOIN Organiser ON Events.Organiser_ID = Organiser.Organiser_ID 
                           WHERE Ticket.Ticket_ID IN ($placeholders)";
    $stmt_booked_tickets = $conn->prepare($sql_booked_tickets);

    if ($stmt_booked_tickets) {
        // Dynamically bind parameters based on the number of booked tickets
        $types = str_repeat('i', count($ticket_ids)); // 'i' for integer
        $stmt_booked_tickets->bind_param($types, ...$ticket_ids);
        $stmt_booked_tickets->execute();
        $booked_tickets_data = $stmt_booked_tickets->get_result()->fetch_all(MYSQLI_ASSOC);

        // Add the quantity of tickets booked to the data
        foreach ($booked_tickets_data as &$ticket) {
            foreach ($booked_tickets as $booked) {
                if (is_array($booked) && isset($booked['ticket_id'], $booked['quantity']) && $ticket['Ticket_ID'] == $booked['ticket_id']) {
                    $ticket['quantity'] = $booked['quantity'];
                    break;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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

        .navbar-brand img {
            width: 40px; /* Adjust the size of the logo */
            margin-right: 10px; /* Add spacing between the logo and text */
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-download, .btn-view {
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 14px;
        }

        .btn-download {
            background-color: #28a745;
            color: white;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
            margin-bottom: 15px;
        }

        .btn-view:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">
        <img src="../assets/images/logo.png" alt="Logo"> Event Management
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center">Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
    <p class="text-center text-muted">Browse and book tickets for upcoming events</p>

    <!-- Available Events Section -->   
    <h2 class="mt-5">Available Events</h2>
    <?php if ($result_events->num_rows > 0) { ?>
        <table class="table table-hover table-bordered mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>Event Name</th>
                    <th>Details</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Date & Time</th>
                    <th>Organiser</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_events->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['Event_Name']; ?></td>
                        <td><?php echo $row['Event_Details']; ?></td>
                        <td><?php echo $row['Event_Address']; ?></td>
                        <td><?php echo $row['Event_City']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($row['Event_Time'])); ?></td>
                        <td><?php echo $row['Organiser_Name']; ?></td>
                        <td>
                            <a href="book_ticket.php?event_id=<?php echo $row['Event_ID']; ?>" class="btn btn-primary btn-sm">Book Ticket</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info text-center mt-4">No events available at the moment. Please check back later.</div>
    <?php } ?>

    <!-- Booked Tickets Section -->
    <h2 class="mt-5">Your Booked Tickets</h2>
    <div class="row mt-4">
        <?php if (!empty($booked_tickets_data)) { ?>
            <?php foreach ($booked_tickets_data as $row) { ?>
                <div class="col-md-4">
                    <div class="card" id="ticket-<?php echo $row['Ticket_ID']; ?>">
                        <h5 class="card-title"><?php echo $row['Ticket_Name']; ?></h5>
                        <p><strong>Ticket ID:</strong> <?php echo $row['Ticket_ID']; ?></p>
                        <p><strong>Event:</strong> <?php echo $row['Event_Name']; ?></p>
                        <p><strong>Organiser:</strong> <?php echo $row['Organiser_Name']; ?></p>
                        <p><strong>Date & Time:</strong> <?php echo date('Y-m-d H:i:s', strtotime($row['Event_Time'])); ?></p>
                        <p><strong>Price:</strong> $<?php echo $row['Ticket_Price']; ?></p>
                        <p><strong>Tickets Booked:</strong> <?php echo $row['quantity']; ?></p>
                        <button class="btn btn-view" data-toggle="modal" data-target="#viewTicketModal<?php echo $row['Ticket_ID']; ?>">View Ticket</button>
                        <button class="btn btn-download" onclick="downloadTicketAsImage(<?php echo $row['Ticket_ID']; ?>)">Download Ticket</button>
                    </div>
                </div>

                <!-- Modal for Viewing Ticket -->
                <div class="modal fade" id="viewTicketModal<?php echo $row['Ticket_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewTicketModalLabel<?php echo $row['Ticket_ID']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewTicketModalLabel<?php echo $row['Ticket_ID']; ?>">Ticket Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h5 class="text-center"><?php echo $row['Ticket_Name']; ?></h5>
                                <p><strong>Ticket ID:</strong> <?php echo $row['Ticket_ID']; ?></p>
                                <p><strong>Event:</strong> <?php echo $row['Event_Name']; ?></p>
                                <p><strong>Organiser:</strong> <?php echo $row['Organiser_Name']; ?></p>
                                <p><strong>Date & Time:</strong> <?php echo date('Y-m-d H:i:s', strtotime($row['Event_Time'])); ?></p>
                                <p><strong>Price:</strong> $<?php echo $row['Ticket_Price']; ?></p>
                                <p><strong>Tickets Booked:</strong> <?php echo $row['quantity']; ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-info text-center w-100">You have not booked any tickets yet.</div>
        <?php } ?>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Event Management System. All Rights Reserved.</p>
</footer>

<script>
    function downloadTicketAsImage(ticketId) {
        const ticketElement = document.getElementById(`ticket-${ticketId}`);
        html2canvas(ticketElement).then(canvas => {
            const link = document.createElement('a');
            link.download = `Ticket_${ticketId}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }
</script>
</body>
</html>