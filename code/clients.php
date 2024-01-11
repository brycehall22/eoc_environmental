<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="eoc.css">
    <link rel="stylesheet" href="clients.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="eoc.js"></script>
    <title>EOC Environmental</title>
</head>
<body>
    <?php include 'connection.php'; ?>
    <header>
    <!-- Top Navigation Menu -->
    <div class="topnav">
        <a href="eoc.php" class="logolink">
            <img src="logo.png" alt="Logo" class="logo">
        </a>
        <!-- Navigation links (hidden by default) -->
        <div id="myLinks">
            <a href="job.php">Jobs</a>
            <a href="login.php">Offices</a>
            <a href="equipment.php">Equipment</a>
        </div>
        <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
        <i class="fa fa-bars"></i>
        </a>
    </div>
    </header>
    <main>
        <?php
            // Function to sanitize input
            function sanitize($input) {
                global $conn;
                return $conn->real_escape_string($input);
            }

            // Update client information
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editClient"])) {
                $id = sanitize($_POST["id"]);
                $name = sanitize($_POST["name"]);
                $contactNumber = sanitize($_POST["contactNumber"]);
                $email = sanitize($_POST["email"]);

                $sql = "UPDATE Clients SET ClientName='$name', ContactNumber='$contactNumber', Email='$email' WHERE ClientID=$id";
                $conn->query($sql);
            }

            // Update company information
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editCompany"])) {
                $id = sanitize($_POST["id"]);
                $name = sanitize($_POST["name"]);
                $contactNumber = sanitize($_POST["contactNumber"]);
                $email = sanitize($_POST["email"]);

                $sql = "UPDATE Companies SET CompanyName='$name', ContactNumber='$contactNumber', Email='$email' WHERE CompanyID=$id";
                $conn->query($sql);
            }

            // Display clients
            $sql = "SELECT * FROM Clients";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>Clients</h2>";
                echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Contact Number</th><th>Email</th><th>Action</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['ClientID']}</td><td>{$row['ClientName']}</td><td>{$row['ContactNumber']}</td><td>{$row['Email']}</td><td><a href='clients.php?action=edit&type=client&id={$row['ClientID']}'>Edit</a></td></tr>";
                }
                echo "</table>";
            }

            // Display companies
            $sql = "SELECT * FROM Companies";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>Companies</h2>";
                echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Contact Number</th><th>Email</th><th>Action</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['CompanyID']}</td><td>{$row['CompanyName']}</td><td>{$row['ContactNumber']}</td><td>{$row['Email']}</td><td><a href='clients.php?action=edit&type=company&id={$row['CompanyID']}'>Edit</a></td></tr>";
                }
                echo "</table>";
            }

            // Edit client
            if (isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["type"]) && $_GET["type"] == "client") {
                $id = sanitize($_GET["id"]);
                $sql = "SELECT * FROM Clients WHERE ClientID=$id";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                if ($row) {
                    echo "<h2>Edit Client</h2>";
                    echo "<form method='post' action='clients.php'>";
                    echo "<input type='hidden' name='id' value='$id'>";
                    echo "<input type='hidden' name='type' value='client'>";
                    echo "Name: <input type='text' name='name' value='{$row['ClientName']}'><br>";
                    echo "Contact Number: <input type='text' name='contactNumber' value='{$row['ContactNumber']}'><br>";
                    echo "Email: <input type='text' name='email' value='{$row['Email']}'><br>";
                    echo "<input type='submit' name='editClient' value='Save Changes'>";
                    echo "</form>";
                }
            }

            // Edit company
            if (isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["type"]) && $_GET["type"] == "company") {
                $id = sanitize($_GET["id"]);
                $sql = "SELECT * FROM Companies WHERE CompanyID=$id";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                if ($row) {
                    echo "<h2>Edit Company</h2>";
                    echo "<form method='post' action='clients.php'>";
                    echo "<input type='hidden' name='id' value='$id'>";
                    echo "<input type='hidden' name='type' value='company'>";
                    echo "Name: <input type='text' name='name' value='{$row['CompanyName']}'><br>";
                    echo "Contact Number: <input type='text' name='contactNumber' value='{$row['ContactNumber']}'><br>";
                    echo "Email: <input type='text' name='email' value='{$row['Email']}'><br>";
                    echo "<input type='submit' name='editCompany' value='Save Changes'>";
                    echo "</form>";
                }
            }

            // Close the database connection
            $conn->close();
        ?>
    </main>
</body>
</html>