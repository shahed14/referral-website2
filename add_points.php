<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['affiliate_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: reflogin.php");
    exit();
}

// Include the database connection file
include('db_connection.php');

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
                mysqli_query($conn, $update_points_query);

                // Update the current affiliate's points to reflect the new points count
                $points += 10;
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <!-- Add the navbar code here -->

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="text-center">Apply Referral Link to Add Points</h2>
                <form method="POST" action="add_points.php">
                    <div class="form-group">
                        <label for="referral_link">Referral Link:</label>
                        <input type="text" class="form-control" id="referral_link" name="referral_link" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Apply Referral Link</button>
                </form>
            </div>
        </div>

        <!-- Display success or error messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="alert alert-success mt-3" role="alert">
                        Points added successfully!
                    </div>
                </div>
            </div>
        <?php } elseif (isset($_GET['error']) && $_GET['error'] == 1) { ?>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="alert alert-danger mt-3" role="alert">
                        Invalid referral link. Please check the link and try again.
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Add Bootstrap JS and jQuery links (required for Bootstrap components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
