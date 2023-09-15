<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php');

// Function to generate a unique referral code
function generateRefCode($name, $conn) {
    // Generate a random number between 1000 and 9999
    $random_number = rand(1000, 9999);

    // Remove any spaces from the name and convert it to lowercase
    $cleaned_name = strtolower(str_replace(' ', '', $name));

    // Combine the cleaned name and the random number to create the referral code
    $ref_code = 'REF' . $cleaned_name . $random_number;

    // Check if the generated referral code already exists in the database
    $check_query = "SELECT * FROM users WHERE ref_code = '$ref_code'";
    $check_result = mysqli_query($conn, $check_query);

    // If the referral code already exists, generate a new one recursively
    if (mysqli_num_rows($check_result) > 0) {
        return generateRefCode($name, $conn); // Recursively generate a new referral code
    }

    return $ref_code; // Return the unique referral code
}

$nameErr = $emailErr = $passwordErr = $countryErr = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the registration form submission
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $country = $_POST['country'];
    $role = 'affiliate'; // Set role to "affiliate" for affiliate users

    // Validate name field (required)
    if (empty($name)) {
        $nameErr = "Name is required";
    }

    // Validate email field (required and must be a valid email format)
    if (empty($email)) {
        $emailErr = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    // Validate password field (required and minimum length of 8 characters)
    if (empty($password)) {
        $passwordErr = "Password is required";
    } elseif (strlen($password) < 8) {
        $passwordErr = "Password must be at least 8 characters long";
    }

    // Validate country field (required)
    if (empty($country)) {
        $countryErr = "Country is required";
    }

    // If there are no errors, proceed with user registration
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($countryErr)) {
        // Generate a unique referral code
        $ref_code = generateRefCode($name, $conn);

        // Hash the password for storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the users table
        $user_query = "INSERT INTO users (Name, email, password, ref_code, user_code, role)
                       VALUES ('$name', '$email', '$hashed_password', '$ref_code', '', '$role')";

        if (mysqli_query($conn, $user_query)) {
            // Retrieve the newly inserted user's ID
            $user_id = mysqli_insert_id($conn);

            // Generate the user code using the user's ID
            $user_code = 'USER' . str_pad($user_id, 4, '0', STR_PAD_LEFT);

            // Update the user's entry with the generated user code
            $update_user_code_query = "UPDATE users SET user_code = '$user_code' WHERE id = '$user_id'";
            mysqli_query($conn, $update_user_code_query);

            // Insert data into the affiliates table
            $created_at = date('Y-m-d H:i:s');
            $affiliate_query = "INSERT INTO affiliates (affiliate_name, email, country, ref_code, created_at, affiliate_points)
                                VALUES ('$name', '$email', '$country', '$ref_code', '$created_at', 0)";

            if (mysqli_query($conn, $affiliate_query)) {
                // Registration successful, redirect to the desired page
                header("Location: reflogin.php");
                exit();
            } else {
                // Error inserting into the affiliates table
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            // Error inserting into the users table
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registration Form</title>
  <!-- Add Bootstrap CSS link -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <h2 class="text-center">Register</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" class="form-control" id="country" name="country" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Add Bootstrap JS and jQuery links (required for Bootstrap components) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
