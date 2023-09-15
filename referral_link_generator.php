<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the referral link generation form submission
    $target_url = $_POST['target_url'];

    // Get the affiliate's id and ref_code from the session
    $affiliate_id = isset($_SESSION['affiliate_id']) ? $_SESSION['affiliate_id'] : null;
    $ref_code = isset($_SESSION['ref_code']) ? $_SESSION['ref_code'] : null;

    // Check if the target URL already has query parameters
    if (strpos($target_url, '?') !== false) {
        // If the target URL already has query parameters, use '&' as a separator
        $referral_link = $target_url . '&affiliate_id=' . $affiliate_id . '&ref_code=' . $ref_code;
    } else {
        // If the target URL does not have query parameters, use '?' as a separator
        $referral_link = $target_url . '?affiliate_id=' . $affiliate_id . '&ref_code=' . $ref_code;
    }

    // Store any necessary data in the database to track referrals
    // You can implement this based on your specific requirements

    // Redirect the affiliate back to the referral link generator page with the generated link
    header("Location: referral_link_generator.php?link=" . urlencode($referral_link));
    exit();
}

// Define the variable $referral_link outside the "if" block with an empty value
$referral_link = '';

// Check if the "link" parameter is present in the URL (after redirection)
if (isset($_GET['link'])) {
    $referral_link = $_GET['link'];
}

// Retrieve the points count for the logged-in affiliate
$affiliate_points = 0;
if (isset($_SESSION['affiliate_id'])) {
    $affiliate_id = $_SESSION['affiliate_id'];

    // Query the database to get the points count for the affiliate
    $query = "SELECT affiliate_points FROM affiliates WHERE id = '$affiliate_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $affiliate_points = $row['affiliate_points'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Referral Link Generator</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom styles for the page */
        body {
            background-color: #f5f5f5;
        }
        .container {
            padding-top: 30px;
        }
        .form-container {
            max-width: 600px; /* Increased width */
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: #fff;
        }
        .generated-link-container {
            max-width: 600px; /* Increased width */
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: #fff;
        }
        .generated-link {
            word-break: break-all;
        }
        .back-link {
            margin-top: 10px;
        }
        .points {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        .btn-primary {
            background-color: #f1c40f;
            border-color: #f1c40f;
        }
        .btn-primary:hover {
            background-color: #e2b731;
            border-color: #e2b731;
        }
        .dark-orange-bg {
            background-color: #e67e22;
            color: #fff;
        }
        .dark-orange-bg:hover {
            background-color: #d35400;
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

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2"> <!-- Increased width -->
                <div class="form-container">
                    <h2 class="text-center">Referral Link Generator</h2>
                    <form method="POST" action="referral_link_generator.php">
                        <div class="form-group">
                            <label for="target_url">Target URL:</label>
                            <input type="url" class="form-control" id="target_url" name="target_url" required>
                        </div>
                        <!-- Add more input fields for additional parameters if needed -->

                        <button type="submit" class="btn btn-primary btn-block">Generate Referral Link</button>
                    </form>
                </div>
            </div>
        </div>

       <!-- Display the generated referral link to the affiliate -->
<?php if ($referral_link !== '') { ?>
    <div class="row">
        <div class="col-md-8 offset-md-2"> <!-- Increased width -->
            <div class="generated-link-container">
                <h3 class="text-center">Your Referral Link</h3>
                <div class="generated-link alert alert-success" role="alert">
                    <a href="<?php echo $referral_link; ?>" target="_blank"><?php echo $referral_link; ?></a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<!-- Go back to the Marketplace section -->
<div class="row">
    <div class="col-md-8 offset-md-2"> <!-- Increased width -->
        <p class="text-center back-link">Click <a href="../eCommerceSite-php/index.php" target="_blank">here</a> to go back to the Marketplace.</p>
    </div>
</div>


        <!-- Add social media sharing buttons or other sharing options here -->
        <div class="text-center">
            <button class="btn btn-primary" onclick="shareOnFacebook()">Share on Facebook</button>
            <button class="btn btn-primary" onclick="shareOnTwitter()">Share on Twitter</button>
            <!-- Add more sharing options if desired -->
        </div>

        <!-- Display the points count for the logged-in affiliate -->
        <div class="row">
            <div class="col-md-8 offset-md-2"> <!-- Increased width -->
                <div class="points text-center">
                    <?php if (isset($_SESSION['affiliate_id'])) { ?>
                        Points: <?php echo $affiliate_points; ?>
                    <?php } ?>
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

