<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['affiliate_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: reflogin.php");
    exit();
}

// Retrieve the affiliate's ID from the session
$affiliate_id = $_SESSION['affiliate_id'];
$affiliate_name = $_SESSION['affiliate_name'];

// Function to calculate the total points earned by the affiliate
function calculateTotalPoints($conn, $affiliate_id) {
    $query = "SELECT SUM(affiliate_points) AS total_points FROM affiliates WHERE id = '$affiliate_id'";
    $result = mysqli_query($conn, $query);
    
    // Check if the query was executed successfully
    if (!$result) {
        // Print the error message and SQL query for debugging purposes
        echo "Error executing query: " . mysqli_error($conn) . "<br>";
        echo "Query: " . $query;
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    return $row['total_points'];
}

// Query to get the referral link data for the affiliate
$query = "SELECT id, referral_link, clicks, signups FROM referral_statistics WHERE affiliate_id = '$affiliate_id'";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if (!$result) {
    // Print the error message and SQL query for debugging purposes
    echo "Error executing query: " . mysqli_error($conn) . "<br>";
    echo "Query: " . $query;
    exit();
}

// Array to store referral link data
$referral_links = array();

// Process the result and store data in the array
while ($row = mysqli_fetch_assoc($result)) {
    $referral_links[] = $row;
}

// Function to calculate the conversion rate
function calculateConversionRate($clicks, $signups) {
    return ($clicks > 0) ? round(($signups / $clicks) * 100, 2) : 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Referral Statistics</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom CSS for the Referral Statistics Page */
        body {
            background-color: #f5f5f5;
        }
        .jumbotron {
            background-color: orange;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .jumbotron h1 {
            color: #fff;
        }
        .jumbotron p {
            color: #fff;
        }
        .jumbotron ul {
            color: #fff;
            list-style-type: square;
            padding-left: 20px;
        }
        .jumbotron hr {
            border-top: 1px solid #fff;
        }
        .dark-text {
            color: #2c3e50;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #f1c40f;
            color: #fff;
            border: none;
            font-weight: bold;
        }
        .table tbody td {
            background-color: #fff;
        }
    </style>
</head>
<body>
 <!-- Main Navbar -->
<nav style="background-color: white;" class="navbar navbar-expand-lg navbar-light">
  <a class="navbar-brand" href="index.php">
    <img src="logo.png" alt="Logo" height="70" width="100">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <!-- "Affiliate" link with dark orange background -->
      <li class="nav-item">
        <a class="nav-link" style="color: darkorange; font-weight: bold; font-size: 16px;" href="index.php">Affiliate</a>
      </li>

      <!-- Check if the user is logged in -->
      <?php if (isset($_SESSION['register']) && $_SESSION['register'] === true) { ?>

        <!-- Show the "Referral Link Generator" link -->
        <li class="nav-item">
          <a class="nav-link "style="color: darkorange; font-weight: bold; font-size: 16px;" href="referral_link_generator.php">Referral Link Generator</a>
        </li>

        <!-- Show the logout link when the user is logged in -->
        <li class="nav-item">
          <a class="nav-link " style="color: darkorange; font-weight: bold; font-size: 16px;" href="logout.php">Logout</a>
        </li>

      <?php } else { ?>

        <!-- Show the login and register links when the user is not logged in -->
        <li class="nav-item">
          <a class="nav-link" style="color: darkorange; font-weight: bold; font-size: 16px;" href="reflogin.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" style="color: darkorange; font-weight: bold; font-size: 16px;" href="refregister.php">Register</a>
        </li>

      <?php } ?>

      <!-- Add more links to other pages related to the referral system -->
      <li class="nav-item">
        <a class="nav-link" style="color: darkorange; font-weight: bold; font-size: 16px;" href="referral_staticties.php">Referral Statistics</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color: darkorange; font-weight: bold; font-size: 16px;" href="referral_link_application.php">Referral Link Application</a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link " style="color: darkorange; font-weight: bold; font-size: 16px;" href="dashboard.php">Dashboard</a>
      </li>
    </ul>
  </div>
</nav>
 

    <div class="container mt-4">
        <div class="jumbotron">
            <h1 class="display-4 dark-text">Welcome to the Referral Statistics Page, <?php echo $affiliate_name; ?>!</h1>
            <p class="lead">Track your performance as an affiliate and monitor the results of your referral links.</p>
            <hr class="my-4">
            <p>Here's how you can use this page:</p>
            <ul>
                <li>View the total points you've earned for successful referrals.</li>
                <li>See the performance of each referral link, including the number of clicks and sign-ups.</li>
                <li>Check your conversion rates to optimize your referral strategy.</li>
            </ul>
            <p class="mt-3"><strong>Total Points Earned: <?php echo calculateTotalPoints($conn, $affiliate_id); ?></strong></p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center dark-text">Referral Statistics</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Referral Link</th>
                                <th>Clicks</th>
                                <th>Sign-Ups</th>
                                <th>Conversion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referral_links as $referral_link) { ?>
                                <tr>
                                    <td><?php echo $referral_link['referral_link']; ?></td>
                                    <td><?php echo $referral_link['clicks']; ?></td>
                                    <td><?php echo $referral_link['signups']; ?></td>
                                    <td><?php echo calculateConversionRate($referral_link['clicks'], $referral_link['signups']); ?>%</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS and jQuery links (required for Bootstrap components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
