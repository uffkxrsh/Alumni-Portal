<?php
session_start();
include "./php/db_conn.php";

$error ='';
if (isset($_POST['rollNumberInput']) && isset($_POST['passwordInput'])) {

  function validate($data)
  {
    return htmlspecialchars(stripslashes(trim($data)));
  }

  /**
   * Validating the roll number
   * Checks if the length of the roll number is 5.  
   * Extracts individual components (YY, X, RR) from the roll number.
   * Validates that YY, X, and RR are integer digits.
   * Validates that X is 1, 2, 3, or 5 (valid stream codes).
   * Validates the range of RR (serial number).
   */

  // function validateRollNumber($rollNumber)
  // {
  //   // Validate the length of the roll number
  //   if (strlen($rollNumber) !== 5) {
  //     return false;
  //   }

  //   // Extract individual components from the roll number
  //   $yy = substr($rollNumber, 0, 2);
  //   $x = substr($rollNumber, 2, 1);
  //   $rr = substr($rollNumber, 3);

  //   // Validate YY, X, and RR as integer digits
  //   if (!ctype_digit($yy) || !ctype_digit($x) || !ctype_digit($rr)) {
  //     return false;
  //   }

  //   // Validate the admission year (YY) range from 2002 to the current year
  //   $currentYear = date("y");
  //   if ($yy < 2 || $yy > $currentYear) {
  //     return false;
  //   }

  //   // Validate stream code (X) is 1, 2, 3, or 5
  //   $validStreamCodes = ['1', '2', '3', '5'];
  //   if (!in_array($x, $validStreamCodes)) {
  //     return false;
  //   }

  //   // Validate the range of RR (serial number)
  //   if ($rr < 1 || $rr > 99) {
  //     return false;
  //   }

  //   // If all validations pass, return true
  //   return true;
  // }

  $rollNumber = validate($_POST['rollNumberInput']);
  // if (!validateRollNumber($rollNumber)) {
  //   echo "invalid roll number";
  // }
  $password = validate($_POST['passwordInput']);

  // Validate other conditions if needed

  if (empty($rollNumber) || empty($password)) {
    $error = "<br>khaali hain variables";
  }

  // Retrieve hashed password from the database based on the provided roll number
  $sql = "SELECT roll_number, password FROM users WHERE roll_number = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $rollNumber);
  $stmt->execute();

  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  $error="";

  // Check if the roll number exists in the database
  if ($user) {
    // Verify the password
    if (password_verify($password, $user['password'])) {
      // Password is correct, set session variables
      $_SESSION['rollNumber'] = $user['roll_number'];
      // Redirect to the user's dashboard or any other page
      header("Location: ./php/welcome.php");
    } else {
      // Password is incorrect
      $error = "<br>password nahi yaad rehta ";
    }
  } else if(!$user){
    // Roll number not found in the database
    $error = "kon hain tu bhai?";
  }
}

// Close the database connection
$conn->close();

?>

<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
  <link rel="stylesheet" type="text/css" href="./CSS/style.css" />
  <link rel="stylesheet" type="text/css" href="./CSS/index.css" />
  <link rel="stylesheet" type="text/css" href="./CSS/main.css" />
  <link rel="stylesheet" type="text/css" href="./CSS/profile.css" />
</head>

<body>
  <main id="login-parent">

    <!-- LOGIN PAGE CONTAINER -->
    <div class="login_container">
      <div class="login_panel">
        <div>

          <form action="./index.php" method="post">
            <!-- ROLL NUMBER -->
            <div class="text_input">
              <label>Roll Number:</label>
              <input type="text" id="rollNumberInput" name="rollNumberInput" />
            </div>

            <!-- PASSWORD -->
            <div class="text_input">
              <label>Password:</label>
              <input type="password" id="passwordInput" name="passwordInput" />
              <div class="forgot-password-link">
                <span id="forgotPasswordLink" onclick="showForgotPassword()">Forgot Password</span>
              </div>
            </div>

            <!-- LOGIN & SIGNUP BUTTON -->
            <div class="log_sign_btn">
              <!-- LOGIN BTN -->
              <input class="log_btn" type="submit" id="loginButton" value="Log In"></input>

              <!-- SIGN UP BTN -->
              <a href="./php/signup.php">
                <div class="signup_btn">
                  <input type="button" class="sign_btn" value="Sign Up"></input>
                </div>
              </a>
            </div>
        </div>
        </form>

        <!-- FORGOT PASSWORD LINK -->
        <div id="forgotPasswordSection">
          <div class="text_input">
            <h2>Forgot Password</h2><br>
            <div>
              <label style="font-size: medium; padding-top: 5px">Enter Your Email:</label>
              <input type="email" id="forgotPasswordEmail" name="emailInput" />
            </div>
            <div class="forgotPassword_btn">
              <br>
              <input type="submit" id="submitForgotPassword" value="Submit"></input>
              <input id="cancelForgotPassword" type="button" onclick="hideForgotPassword()" value="Cancel"></input>
            </div>
          </div>
        </div>
      </div>

      <!-- GENERAL INFO -->
      <div class="login_clg">
        <div class="clg-container">
          <span>Chandigarh College of Engineering & Technology</span>
          <img class="clg-logo" src="./Resources/ccetLogoBlack.png" />
        </div>
        <h1>Alumni Portal</h1>
        <h3>Lorem Ipsum</h3>
      </div>
    </div>
  </main>

  <script>
    const rollNumberInput = document.getElementById("rollNumberInput");
    const passwordInput = document.getElementById("passwordInput");
    const loginButton = document.getElementById("loginButton");
    const rollNumberSpan = document.getElementById("rollNumber");
    const forgotPasswordLink = document.getElementById("forgotPasswordLink");
    const forgotPasswordSection = document.getElementById(
      "forgotPasswordSection"
    );
    const forgotPasswordEmailInput = document.getElementById(
      "forgotPasswordEmail"
    );
    const submitForgotPasswordButton = document.getElementById(
      "submitForgotPassword"
    );
    const cancelForgotPasswordButton = document.getElementById(
      "cancelForgotPassword"
    );

    loginButton.addEventListener("click", handleLogin);
    forgotPasswordLink.addEventListener("click", showForgotPassword);
    submitForgotPasswordButton.addEventListener(
      "click",
      handleForgotPassword
    );
    cancelForgotPasswordButton.addEventListener("click", hideForgotPassword);

    function showForgotPassword() {
      forgotPasswordSection.style.display = "block";
    }

    function hideForgotPassword() {
      forgotPasswordSection.style.display = "none";
      forgotPasswordEmailInput.value = "";
    }

    function handleForgotPassword() {
      const email = forgotPasswordEmailInput.value;
      if (email) {
        // Implement your forgot password logic here
        alert(
          "Forgot password functionality not implemented in this example."
        );
        hideForgotPassword();
      } else {
        alert("Please enter your email.");
      }
    }
  </script>
</body>

</html>

<?php
if($error){
  echo $error;
}
?>