<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the login form submission
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email and password match any record in the users table
    $user_query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $user_result = mysqli_query($conn, $user_query);

    // Check if the email and password match any record in the affiliates table
    $affiliate_query = "SELECT * FROM affiliates WHERE email = '$email'";
    $affiliate_result = mysqli_query($conn, $affiliate_query);

    if (mysqli_num_rows($user_result) > 0) {
        // User found in the users table, verify the password
        $user_row = mysqli_fetch_assoc($user_result);
        if (password_verify($password, $user_row['password'])) {
            // Password is correct, set up session variables
            $_SESSION['register'] = true;
            $_SESSION['user_id'] = $user_row['id'];
            $_SESSION['user_name'] = $user_row['Name'];
            $_SESSION['user_role'] = $user_row['role'];

            // Redirect to the dashboard or any other forward page for users
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password for user
            $login_error = "Incorrect email or password";
        }
    } elseif (mysqli_num_rows($affiliate_result) > 0) {
        // Affiliate found in the affiliates table, no password verification needed for affiliates
        $affiliate_row = mysqli_fetch_assoc($affiliate_result);
        $_SESSION['register'] = true;
        $_SESSION['affiliate_id'] = $affiliate_row['id'];
        $_SESSION['affiliate_name'] = $affiliate_row['affiliate_name'];
            $_SESSION['ref_code'] = $affiliate_row['ref_code']; 

        // Redirect to the dashboard or any other forward page for affiliates
        header("Location: index.php");
        exit();
    } else {
        // Email not found in either users or affiliates table
        $login_error = "Incorrect email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <!-- Add Bootstrap CSS link -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    /* Custom styles for the login form */
    .login-form-container {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <h2 class="text-center">Login</h2>
        <div class="login-form-container">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <?php if (isset($login_error)) { ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $login_error; ?>
              </div>
            <?php } ?>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </form>
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
