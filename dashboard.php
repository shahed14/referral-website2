<?php
// Include the database connection file
session_start();
include("db_connection.php");

// Retrieve the affiliate name from the session
$affiliateName = $_SESSION['affiliate_name'];

// Fetch the affiliate ID from the database
$query = "SELECT id FROM affiliates WHERE affiliate_name = '$affiliateName'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $affiliateId = $row['id'];
} else {
    
echo "wrong"; 
   exit();
}

// Fetch account overview data
$query = "SELECT COUNT(*) AS total_clicks, 
                 SUM(earnings) AS total_earnings
          FROM clicks
          WHERE affiliate_id = $affiliateId";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $clicks = $row['total_clicks'];
    $earnings = $row['total_earnings'];
} else {
    $clicks = 0;
    $earnings = 0.00;
}

// Fetch payment history
$query = "SELECT * FROM payments WHERE affiliate_id = $affiliateId";
$result = $conn->query($query);
$paymentHistory = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $paymentHistory[] = array('payment_date' => $row['payment_date'], 'payment_amount' => $row['payment_amount']);
    }
}


// Fetch support resources
$query = "SELECT name, link FROM support_resources";
$result = $conn->query($query);
$supportResources = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $supportResources[$row['name']] = $row['link'];
    }
}

// Fetch notifications
$query = "SELECT message FROM notifications WHERE affiliate_id = $affiliateId";
$result = $conn->query($query);
$notifications = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row['message'];
    }
}

// Close the database connection
$conn->close();
?>

<!-- Example HTML structure for the dashboard using Bootstrap -->
<!DOCTYPE html>
<html>
<head>
    <title>Affiliate Dashboard</title>
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
        .orange-bg {
            background-color: #fd7e14;
            color: #ffffff;
            padding: 20px;
            border-radius: 5px;
        }
        .dark-text {
            color: #333333;
        }
        .jumbotron {
            margin-bottom: 30px;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
        }
        .table {
            margin-bottom: 30px;
        }
        .list-group-item {
            border: 1px solid #dee2e6;
            border-radius: 0;
        }
        .list-group-item:first-child {
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .list-group-item:last-child {
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
    </style>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <div class="jumbotron orange-bg">
      <h1 class="display-4 dark-text">Welcome to your dashboard, <?php echo $affiliateName; ?>! </h1>
      <p class="lead">thanks for joinung our affiliate program.</p>
      <hr class="my-4">
         </div>
  </div>
    <div class="container">

        <!-- Account Overview -->
        <h2 class="mt-4">Account Overview</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Clicks</h5>
                        <p class="card-text"><?php echo $clicks; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Earnings</h5>
                        <p class="card-text">$<?php echo $earnings; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <h2 class="mt-4">Payment Information</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paymentHistory as $payment): ?>
                    <tr>
                        <td><?php echo $payment['payment_date']; ?></td>
                        <td>$<?php echo $payment['payment_amount']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Affiliate Support and Resources -->
        <h2 class="mt-4">Affiliate Support and Resources</h2>
        <div class="list-group">
            <?php foreach ($supportResources as $resourceName => $resourceLink): ?>
                <a href="<?php echo $resourceLink; ?>" class="list-group-item"><?php echo $resourceName; ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Notifications -->
<h2 class="mt-4">Notifications</h2>
<ul class="list-group">
    <?php foreach ($notifications as $notification): ?>
        <li class="list-group-item"><?php echo $notification; ?></li>
    <?php endforeach; ?>
</ul>

    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
