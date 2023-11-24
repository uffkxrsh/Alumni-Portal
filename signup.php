<?php
session_start();
include "db_conn.php";

if (isset($_POST['signupButton'])) {
    // Function to validate input data
    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Get form input data
    $name = validate($_POST['nameInput']);
    $rollNumber = validate($_POST['rollNumberInput']);
    $password = validate($_POST['passwordInput']);
    $confirmPassword = validate($_POST['confirmPasswordInput']);

    // Validate form data
    if (empty($name) || empty($rollNumber) || empty($password) || empty($confirmPassword)) {
        header("Location: signup.php?error=Please fill out all fields");
        exit();
    }

    // Validate roll number length
    if (strlen($rollNumber) !== 5) {
        header("Location: signup.php?error=Roll Number must be 5 characters");
        exit();
    }

    // Validate password match
    if ($password !== $confirmPassword) {
        header("Location: signup.php?error=Password does not match");
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    $sql = "INSERT INTO users (name, roll_number, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bind_param("sss", $name, $rollNumber, $hashedPassword);

    // Execute the query
    $stmt->execute();

    // Check for success
    if ($stmt->affected_rows > 0) {
        header("Location: signup.php?success=Registration successful!");
    } else {
        header("Location: signup.php?error=Registration failed");
    }

    // Close the statement
    $stmt->close();
    
    // Close the database connection
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup</title>
  <link rel="stylesheet" href="CSS/style.css" />
</head>

<body>

  <main id="signup-parent">
    <div class="login_container">

      <!-- THE SIGNUP PAGE CONTAINER -->
      <div class="login_panel">

        <!-- THE INPUT FORM -->
        <div>

          <!-- BACK TO LOGIN PAGE LINK -->
          <div class="back-link">
            <a href="index.php">
              <img class="back-logo" src="Resources/back.png" alt="Back Logo" />
              Back to Login
            </a>
          </div>

          <form action="signup.php" method="post">
            <!-- NAME -->
            <div class="text_input">
              <label>Name:</label>
              <input type="text" name="nameInput" id="nameInput" />
            </div>

            <!-- ROLL NUMBER -->
            <div class="text_input">
              <label>Roll Number:</label>
              <input type="text" name="rollNumberInput" id="rollNumberInput" />
              <div id="rollNumberError" class="error-message"></div>
            </div>

            <!-- BRANCH AND YEAR -->
            <div class="text_input">
              <label class="branch-year" id="branchYearLabel">
                Branch: <span id="branchSpan"></span> &nbsp; Year:
                <span id="yearSpan"></span>
              </label>
            </div>

            <!-- PASSWORD -->
            <div class="text_input">
              <label>Password:</label>
              <input type="password" name="passwordInput" id="passwordInput" />
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="text_input">
              <label>Confirm Password:</label>
              <input type="password" name="confirmPasswordInput" id="confirmPasswordInput" />
              <div id="passwordError" class="error-message"></div>
            </div>

            <!-- SIGNUP BTN -->
            <div class="log_sign_btn">
              <input type="submit" name="signupButton" id="signupButton" value="Sign Up"></input>
            </div>
          </form>
        </div>

      </div>

      <!-- GENERAL INFO -->
      <div class="login_clg">
        <div class="clg-container">
          <span>Chandigarh College of Engineering & Technology</span>
          <img class="clg-logo" src="Resources/ccetLogoBlack.png" alt="College Logo" />
        </div>
        <h1>Alumni Portal</h1>
        <h3>Lorem Ipsum</h3>
      </div>

    </div>
  </main>

  <script>
    const nameInput = document.getElementById("nameInput");
    const rollNumberInput = document.getElementById("rollNumberInput");
    const branchSpan = document.getElementById("branchSpan");
    const yearSpan = document.getElementById("yearSpan");
    const branchYearLabel = document.getElementById("branchYearLabel");
    const passwordInput = document.getElementById("passwordInput");
    const confirmPasswordInput = document.getElementById(
      "confirmPasswordInput"
    );
    const rollNumberError = document.getElementById("rollNumberError");
    const passwordError = document.getElementById("passwordError");
    const signupButton = document.getElementById("signupButton");

    signupButton.addEventListener("click", handleRegistration);
    rollNumberInput.addEventListener("input", handleRollNumberChange);

    function handleRegistration() {
      const name = nameInput.value;
      const rollNumber = rollNumberInput.value;
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      if (
        name === "" ||
        rollNumber === "" ||
        password === "" ||
        confirmPassword === ""
      ) {
        alert("Please fill out all fields.");
      } else if (rollNumber.length !== 5) {
        rollNumberError.textContent = "Roll Number must be 5 characters.";
        rollNumberError.style.display = "block";
        rollNumberError.style.color = "red";
      } else if (password !== confirmPassword) {
        passwordError.textContent = "Password is not the same";
        passwordError.style.display = "block";
      } else {
        rollNumberError.style.display = "none"; // Hide the error message
        passwordError.style.display = "none"; // Hide the error message
        alert("Registration successful!");
      }
    }

    function handleRollNumberChange() {
      const rollNumber = rollNumberInput.value;

      if (rollNumber.length === 5) {
        rollNumberError.style.display = "none"; // Hide the error message
        const yearPrefix = rollNumber.substring(0, 2);
        const branchCode = rollNumber.substring(2, 3);

        switch (branchCode) {
          case "1":
            setBranchAndYear("Mech", `20${yearPrefix}`);
            break;
          case "2":
            setBranchAndYear("Civil", `20${yearPrefix}`);
            break;
          case "3":
            setBranchAndYear("CSE", `20${yearPrefix}`);
            break;
            a
          case "5":
            setBranchAndYear("ECE", `20${yearPrefix}`);
            break;
          default:
            setBranchAndYear("Unknown Branch", "");
            break;
        }
      } else {
        rollNumberError.textContent = "Roll Number must be 5 characters.";
        rollNumberError.style.display = "block";
        setBranchAndYear("", "");
      }
    }

    function setBranchAndYear(branch, year) {
      branchSpan.textContent = branch;
      yearSpan.textContent = year;
      branchYearLabel.style.display = branch || year ? "block" : "block";
    }
  </script>
</body>

</html>

