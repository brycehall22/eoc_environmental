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
        $result = $conn->query("SELECT Equipment.*, Clients.ClientName AS ClientName, Companies.CompanyName AS CompanyName
        FROM Equipment
        LEFT JOIN Clients ON Equipment.ClientID = Clients.ClientID
        LEFT JOIN Companies ON Equipment.CompanyID = Companies.CompanyID");
        $equipmentData = $result->fetch_all(MYSQLI_ASSOC);

        // Add new equipment
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addEquipment'])) {
            $model = !empty($_POST['model']) ? "'".$_POST['model']."'" : 'NULL';
            $serialNumber = !empty($_POST['serialNumber']) ? "'".$_POST['serialNumber']."'" : 'NULL';
            $installDate = !empty($_POST['installDate']) ? "'".$_POST['installDate']."'" : 'NULL';
            $warrantyExpiration = !empty($_POST['warrantyExpiration']) ? "'".$_POST['warrantyExpiration']."'" : 'NULL';
            $assignedName = !empty($_POST['assignedName']) ? "'".$_POST['assignedName']."'" : 'NULL';
            $status = !empty($_POST['status']) ? "'".$_POST['status']."'" : 'NULL';
            $maintenanceDate = !empty($_POST['maintenanceDate']) ? "'".$_POST['maintenanceDate']."'" : 'NULL';

            // Check if Assigned Name is provided
            if (!empty($assignedName)) {
                // Try to match Assigned Name with a client
                $clientResult = $conn->query("SELECT ClientID FROM Clients WHERE ClientName = '$assignedName'");
                $clientData = $clientResult->fetch_assoc();

                // Try to match Assigned Name with a company if not matched with a client
                if (!$clientData) {
                    $companyResult = $conn->query("SELECT CompanyID FROM Companies WHERE CompanyName = '$assignedName'");
                    $companyData = $companyResult->fetch_assoc();

                    // If matched with a company, set ClientID to NULL
                    $clientID = 'NULL';
                    $companyID = !empty($companyData['CompanyID']) ? $companyData['CompanyID'] : 'NULL';
                } else {
                    // If matched with a client, set CompanyID to NULL
                    $clientID = !empty($clientData['ClientID']) ? $clientData['ClientID'] : 'NULL';
                    $companyID = 'NULL';
                }
            } else {
                // If Assigned Name is not provided, set both ClientID and CompanyID to NULL
                $clientID = 'NULL';
                $companyID = 'NULL';
            }

            // Insert new equipment
            $sql = "INSERT INTO Equipment (Model, SerialNumber, InstallDate, WarrantyExpiration, 
                    ClientID, CompanyID, AssignedName, Status, LastMaintenanceDate) 
                    VALUES ($model, $serialNumber, $installDate, $warrantyExpiration, 
                            $clientID, $companyID, $assignedName, $status, $maintenanceDate)";

            if (!$conn->query($sql)) {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // Redirect to the same page to avoid form resubmission on page reload
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        }

        // Update equipment information
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateEquipment'])) {
            $equipmentID = $_POST['equipmentID'];
            $fieldToUpdate = $_POST['fieldToUpdate'];
            $newValue = $_POST['newValue'];

            // Update a single field for the specified equipment
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
                <th>Install Date</th>
                <th>Warranty Expiration</th>
                <th>Status</th>
                <th>Last Maintenance Date</th>
                <th>Client ID</th>
                <th>Company ID</th>
                <th>Model</th>
                <th>Serial Number</th>
                <th>Assigned Name</th>
            </tr>

            <?php foreach ($equipmentData as $equipment): ?>
                <tr>
                    <td><?= $equipment['EquipmentID']; ?></td>
                    <td><?= $equipment['InstallDate']; ?></td>
                    <td><?= $equipment['WarrantyExpiration']; ?></td>
                    <td data-status="<?= $equipment['Status']; ?>"><?= $equipment['Status']; ?></td>
                    <td><?= $equipment['LastMaintenanceDate']; ?></td>
                    <td><?= $equipment['ClientID']; ?></td>
                    <td><?= $equipment['CompanyID']; ?></td>
                    <td><?= $equipment['Model']; ?></td>
                    <td><?= $equipment['SerialNumber']; ?></td>
                    <td><?= $equipment['AssignedName']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- HTML form for adding new equipment -->
        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
            <label for="model">Model:</label>
            <input type="text" name="model" required>

            <label for="serialNumber">Serial Number:</label>
            <input type="text" name="serialNumber" required>

            <label for="installDate">Install Date:</label>
            <input type="date" name="installDate">

            <label for="warrantyExpiration">Warranty Expiration:</label>
            <input type="date" name="warrantyExpiration">

            <label for="assignedName">Assigned Name:</label>
            <input type="text" name="assignedName">

            <label for="status">Status (In Use or In Storage):</label>
            <input type="text" name="status">

            <label for="maintenanceDate">Last Maintenance Date:</label>
            <input type="date" name="maintenanceDate">

            <button type="submit" name="addEquipment">Add Equipment</button>
        </form>

        <!-- HTML form for updating equipment information -->
        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
            <label for="equipmentID">Equipment ID:</label>
            <input type="text" name="equipmentID" required>

            <label for="fieldToUpdate">Field to Update:</label>
            <select name="fieldToUpdate" required>
                <option value="Model">Model</option>
                <option value="SerialNumber">Serial Number</option>
                <option value="InstallDate">Install Date</option>
                <option value="WarrantyExpiration">Warranty Expiration</option>
                <option value="ClientID">Client ID</option>
                <option value="CompanyID">Company ID</option>
                <option value="AssignedName">Assigned Name</option>
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