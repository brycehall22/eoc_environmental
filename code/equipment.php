<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="eoc.css">
    <link rel="stylesheet" href="equipment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="eoc.js"></script>
    <title>EOC Environmental</title>
</head>
<body>
    <?php include 'connection.php'; ?>
    <?php
        // Fetch equipment data
        $result = $conn->query("SELECT * FROM Equipment");
        $equipmentData = $result->fetch_all(MYSQLI_ASSOC);

        // Add new equipment
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addEquipment'])) {
            $equipmentName = $_POST['equipmentName'];
            $status = $_POST['status'];
            $maintenanceDate = $_POST['maintenanceDate'];

            // Insert new equipment
            $conn->query("INSERT INTO Equipment (EquipmentName, Status, LastMaintenanceDate) 
                        VALUES ('$equipmentName', '$status', '$maintenanceDate')");

            // Redirect to the same page to avoid form resubmission on page reload
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        }

        // Update equipment information
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateEquipment'])) {
            $equipmentID = $_POST['equipmentID'];
            $fieldToUpdate = $_POST['fieldToUpdate'];
            $newValue = $_POST['newValue'];

            // Update equipment information
            $conn->query("UPDATE Equipment 
            SET $fieldToUpdate = '$newValue' 
            WHERE EquipmentID = '$equipmentID'");

            // Redirect to the same page to avoid form resubmission on page reload
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
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
                <a href="job.php">Jobs</a>
                <a href="login.php">Offices</a>
                <a href="clients.php">Clients</a>
            </div>
            <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
            <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
            </a>
        </div>
    </header>
    <main>
        <h2>Equipment List</h2>

        <table>
            <tr>
                <th>Equipment ID</th>
                <th>Equipment Name</th>
                <th>Status</th>
                <th>Last Maintenance Date</th>
            </tr>

            <?php foreach ($equipmentData as $equipment): ?>
                <tr>
                    <td><?= $equipment['EquipmentID']; ?></td>
                    <td><?= $equipment['EquipmentName']; ?></td>
                    <td data-status="<?= $equipment['Status']; ?>"><?= $equipment['Status']; ?></td>
                    <td><?= $equipment['LastMaintenanceDate']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- HTML form for adding new equipment -->
        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
            <label for="equipmentName">Equipment Name:</label>
            <input type="text" name="equipmentName" required>

            <label for="status">Status:</label>
            <input type="text" name="status" required>

            <label for="maintenanceDate">Last Maintenance Date:</label>
            <input type="date" name="maintenanceDate" required>

            <button type="submit" name="addEquipment">Add Equipment</button>
        </form>

        <!-- HTML form for updating equipment information -->
        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
            <label for="equipmentID">Equipment ID:</label>
            <input type="text" name="equipmentID" required>

            <label for="fieldToUpdate">Field to Update:</label>
            <select name="fieldToUpdate" required>
                <option value="EquipmentName">Equipment Name</option>
                <option value="Status">Status</option>
                <option value="LastMaintenanceDate">Last Maintenance Date</option>
            </select>

            <label for="newValue">New Value:</label>
            <input type="text" name="newValue" required>

            <button type="submit" name="updateEquipment">Update Equipment</button>
        </form>
    </main>
</body>
</html>