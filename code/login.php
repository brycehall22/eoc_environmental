<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="eoc.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="eoc.js"></script>
    <title>EOC Environmental</title>
</head>
<body>
    <?php include 'connection.php'; ?>
    <?php
        // Function to securely hash passwords
        function hashPassword($password) {
            return password_hash($password, PASSWORD_BCRYPT);
        }

        // Function to verify user credentials and get user role
        function verifyUser($conn, $username, $password) {
            $stmt = $conn->prepare("SELECT UserID, Password, Role FROM Users WHERE UserName = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($userID, $hashedPassword, $userRole);
                $stmt->fetch();

                // Verify the password
                if (password_verify($password, $hashedPassword)) {
                    return array('UserID' => $userID, 'UserRole' => $userRole);
                }
            }

            return false;
        }

        // Function to update user password
        function updatePassword($conn, $username, $newPassword) {
            $hashedPassword = hashPassword($newPassword);
            $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE UserName = ?");
            $stmt->bind_param("ss", $hashedPassword, $username);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        }

        // Handle login
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            
            $userData = verifyUser($conn, $username, $password);

            if ($userData !== false) {
                $userID = $userData['UserID'];
                $userRole = $userData['UserRole'];
        
                // Redirect to office.php with both user ID and role
                header("Location: office.php?userID=$userID&role=$userRole");
                exit();
            } else {
                $loginError = "Invalid username or password.";
            }
        }

        // Handle password change
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["changePassword"])) {
            $username = $_POST["changeUsername"];
            $newPassword = $_POST["newPassword"];
            $confirmPassword = $_POST["confirmPassword"];

            if ($newPassword === $confirmPassword) {
                if (updatePassword($conn, $username, $newPassword)) {
                    $passwordChangeSuccess = "Password updated successfully. You can now login with the new password.";
                } else {
                    $passwordChangeError = "Failed to update password. Please try again.";
                }
            } else {
                $passwordChangeError = "Passwords do not match. Please enter them again.";
            }
        }
    ?>
    <header>
        <!-- Top Navigation Menu -->
        <div class="topnav">
            <a href="eoc.php" class="logolink">
                <img src="logo.png" alt="Logo" class="logo">
            </a>
            <!-- Navigation links (hidden by default) -->
            <div id="myLinks">
                <a href="clients.php">Clients</a>
                <a href="equipment.php">Equipment</a>
                <a href="job.php">Jobs</a>
            </div>
            <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
            <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
            </a>
        </div>
    </header>
    <main>
        <?php
            if (isset($loginError)) {
                echo "<p>$loginError</p>";
            }

            if (isset($passwordChangeSuccess)) {
                echo "<p>$passwordChangeSuccess</p>";
            } elseif (isset($passwordChangeError)) {
                echo "<p>$passwordChangeError</p>";
            }
        ?>

        <!-- Login form -->
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" name="login" value="Login">
        </form>

        <!-- Change password form -->
        <form method="post" action="">
            <label for="changeUsername">Username for Password Change:</label>
            <input type="text" id="changeUsername" name="changeUsername" required><br>

            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required><br>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required><br>

            <input type="submit" name="changePassword" value="Change Password">
        </form>
    </main>
</body>
</html>