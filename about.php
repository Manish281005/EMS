<!DOCTYPE html>
<html>
<head>
    <title>About Us - Event Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        .about-header {
            background: linear-gradient(135deg,rgb(249, 207, 0),rgb(255, 0, 0));
            color: white;
            padding: 20px 20px;
            text-align: center;
        }

        .about-header h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .about-header p {
            font-size: 1.2rem;
            margin-bottom: 1wpx;
        }

        .about-content {
            padding: 50px 20px;
            text-align: center;
        }

        .about-content h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #343a40;
        }

        .about-content p {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.8;
        }

        .team-section {
            background-color: #f8f9fa;
            padding: 50px 20px;
        }

        .team-section h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: #343a40;
        }

        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .team-member h5 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: #343a40;
        }

        .team-member p {
            font-size: 1rem;
            color: #6c757d;
        }

        .back-button {
            margin-top: 30px;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- About Header -->
    <div class="about-header">
        <h1>About Event Management System</h1>
        <p>Empowering organizers and attendees with seamless event experiences.</p>
    </div>

    <!-- About Content -->
    <div class="about-content">
        <h2>Our Mission</h2>
        <p>
            At Event Management System, our mission is to simplify the process of organizing and attending events. 
            We provide a platform that bridges the gap between event organizers and participants, ensuring a smooth and enjoyable experience for everyone.
        </p>
    </div>

    <!-- Team Section -->
    <div class="team-section container">
        <h2>Meet Our Team</h2>
        <div class="row">
            <div class="col-md-4 team-member">
                <img src="assets/images/team1.jpg" alt="Team Member 1">
                <h5>John Doe</h5>
                <p>Founder & CEO</p>
            </div>
            <div class="col-md-4 team-member">
                <img src="assets/images/team2.jpg" alt="Team Member 2">
                <h5>Jane Smith</h5>
                <p>Lead Developer</p>
            </div>
            <div class="col-md-4 team-member">
                <img src="assets/images/team3.jpg" alt="Team Member 3">
                <h5>Emily Johnson</h5>
                <p>Marketing Manager</p>
            </div>
        </div>
    </div>

    <!-- Back to Home Button -->
    <div class="back-button">
        <a href="index.php">Back to Home</a>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Event Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>