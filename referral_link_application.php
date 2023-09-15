<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php');

// Check if the user is logged in as an affiliate
if (!isset($_SESSION['affiliate_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: reflogin.php");
    exit();
}

// Initialize the variable to store the affiliate points
$points = 0;

// Handle the referral link application form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the referral link form data is submitted
    if (isset($_POST['referral_link'])) {
        $referral_link = $_POST['referral_link'];

        // Parse the referral link to extract affiliate_id and ref_code parameters
        $parsed_url = parse_url($referral_link);
        parse_str($parsed_url['query'], $query_params);

        // Check if the affiliate_id and ref_code parameters exist in the URL
        if (isset($query_params['affiliate_id']) && isset($query_params['ref_code'])) {
            $referrer_affiliate_id = $query_params['affiliate_id'];
            $ref_code = $query_params['ref_code'];

            // Retrieve the affiliate's id based on the ref_code
            $affiliate_query = "SELECT id FROM affiliates WHERE ref_code = '$ref_code'";
            $affiliate_result = mysqli_query($conn, $affiliate_query);

            if ($affiliate_result && mysqli_num_rows($affiliate_result) > 0) {
                // If the affiliate with the given ref_code exists, update their points by 10
                $affiliate_row = mysqli_fetch_assoc($affiliate_result);
                $referrer_id = $affiliate_row['id'];

                // Increment the affiliate's points by 10 in the database
                $update_points_query = "UPDATE affiliates SET affiliate_points = affiliate_points + 10 WHERE id = '$referrer_id'";

                if (mysqli_query($conn, $update_points_query)) {
                    // Points added successfully, update the current affiliate's points
                    $_SESSION['points_added'] = true;

                    // Update the points variable with the latest points count
                    $points_query = "SELECT affiliate_points FROM affiliates WHERE id = '$referrer_id'";
                    $points_result = mysqli_query($conn, $points_query);

                    if ($points_result && mysqli_num_rows($points_result) > 0) {
                        $points_row = mysqli_fetch_assoc($points_result);
                        $points = $points_row['affiliate_points'];
                    }
                } else {
                    // Error updating points
                    echo "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Query the database to get the points count for the affiliate
$affiliate_id = $_SESSION['affiliate_id'];
$query = "SELECT affiliate_points FROM affiliates WHERE id = '$affiliate_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $points = $row['affiliate_points'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Points from Referral Link</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom styles for the page */
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fd7e14;
        }
        .nav-item {
            margin-right: 10px;
        }
        .dark-orange-bg {
            background-color: #fd7e14;
            color: #ffffff;
        }
        .mt-4 {
            margin-top: 30px;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: #ffffff;
        }
        .btn-primary {
            background-color: #fd7e14;
            border-color: orange;
        }
        .btn-primary:hover {
            background-color: #e05e02;
            border-color: #e05e02;
        }
        .points {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            color: #fd7e14;
        }
        .alert-success {
            background-color: #dff0d8;
            border-color: #d0e9c6;
            color: #3c763d;
        }
        .text-center {
            text-align: center;
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
</nav><br>
<br>



    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <?php if (isset($_SESSION['points_added']) && $_SESSION['points_added']) { ?>
                    <!-- Display a message confirming points added -->
                    <div class="alert alert-success" role="alert">
                        Points added successfully! You have earned 10 points.
                    </div>
                    <?php unset($_SESSION['points_added']); // Clear the session variable after displaying the message ?>
                <?php } ?>

                <h2 class="text-center">Apply Referral Link to Add Points</h2>
                <br>
                <br>
                
                <form method="POST" action="referral_link_application.php">
                    <div class="form-group">
                        <label for="referral_link">Referral Link:</label>
                        <input type="text" class="form-control" id="referral_link" name="referral_link" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Apply Referral Link</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Display the points count for the logged-in affiliate -->
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="points text-center">
                Points: <?php echo $points; ?>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS and jQuery links (required for Bootstrap components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
