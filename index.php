<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['register']) && $_SESSION['register'] === true) {
  // User is logged in, get the username from session
  $welcome_username = $_SESSION['affiliate_name'];
} else {
  // User is not logged in or session expired, show a generic welcome message
  $welcome_username = "Guest";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Referral System</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  
  <style>
    body {
            background-color: #f8f9fa;
        }
    /* Custom orange color variations */
    .orange-bg {
      background-color: #FFA500; /* Orange */
    }
    .dark-orange-bg {
      background-color: #FF8C00; /* Dark Orange */
    }
    .light-orange-bg {
      background-color: #FFD700; /* Light Orange */
    }
    .orange-text {
      color: #FFA500; /* Orange */
    }
    .dark-orange-text {
      color: #FF8C00; /* Dark Orange */
    }
    .light-orange-text {
      color: #FFD700; /* Light Orange */
    }

    .navbar{
            clip-path: polygon(0 0, 100% 0, 100% 80%, 0% 100%); /* Diagonal edges */
                  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow */
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
    <div class="jumbotron orange-bg">
      <h1 class="display-4 dark-text">Welcome to the Referral Program, <?php echo $welcome_username; ?>! </h1>
      <p class="lead">Join our affiliate program and earn rewards for referring others.</p>
      <hr class="my-4">
      <p>Here's how it works:</p>
      <ul>
        <li>Sign up for an account on our website.</li>
        <li>Generate your unique referral link.</li>
        <li>Share the link with your friends and followers.</li>
        <li>Earn rewards for each successful referral.</li>
      </ul>
      <a class="btn btn-primary dark-orange-bg" href="refregister.php" role="button">Join Now</a>
    </div>
  </div>

  <!-- Add Bootstrap JS and jQuery links (required for Bootstrap components) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
