<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="eoc.css">
    <link rel="stylesheet" href="office.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="eoc.js"></script>
    <title>EOC Environmental</title>
</head>
<body>
    <?php include 'connection.php'; ?>
    <?php
        // Function to get user data based on user ID
        function getUserData($conn, $userID) {
            $stmt = $conn->prepare("SELECT UserName, Role, OfficeID FROM Users WHERE UserID = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->bind_result($username, $role, $officeID);
            $stmt->fetch();
            $userData = array('UserName' => $username, 'Role' => $role, 'OfficeID' => $officeID);
            return $userData;
        }

        // Function to get all offices
        function getAllOffices($conn) {
            $result = $conn->query("SELECT * FROM Offices");
            $offices = array();
            while ($row = $result->fetch_assoc()) {
                $offices[] = $row;
            }
            return $offices;
        }

        // Function to get workers based on office ID
        function getWorkersByOffice($conn, $officeID) {
            $stmt = $conn->prepare("SELECT * FROM Workers WHERE OfficeID = ?");
            $stmt->bind_param("i", $officeID);
            $stmt->execute();
            $result = $stmt->get_result();
            $workers = array();
            while ($row = $result->fetch_assoc()) {
                $workers[] = $row;
            }
            return $workers;
        }

        // Check if the user role and ID are provided in the URL
        if (isset($_GET['role']) && isset($_GET['userID'])) {
            $userRole = $_GET['role'];
            $userID = $_GET['userID'];

            // Get user data
            $userData = getUserData($conn, $userID);

            // Display data based on user role
            if ($userRole === 'Owner') {
                // Display all offices and workers table data
                $allOffices = getAllOffices($conn);
                $allWorkers = $conn->query("SELECT * FROM Workers")->fetch_all(MYSQLI_ASSOC);
            } elseif ($userRole === 'Manager') {
                // Display data for the office the manager manages and workers assigned to that office
                $officeID = $userData['OfficeID'];
                $managedOffice = $conn->query("SELECT * FROM Offices WHERE OfficeID = $officeID")->fetch_assoc();
                $workersInOffice = getWorkersByOffice($conn, $officeID);
            } elseif ($userRole === 'Worker') {
                // Display data associated with the worker
                $workerID = $userID;
                $workerData = $conn->query("SELECT * FROM Workers WHERE WorkerID = $workerID")->fetch_assoc();
            } else {
                die("Invalid user role.");
            }
        } else {
            die("User role or ID not provided.");
        }

        // Function to handle office or worker update
        function handleUpdate($conn, $selectedAction, $itemID, $fieldToUpdate, $updatedValue) {
            // Validate input data as needed
            if ($selectedAction == "editOffice") {
                // Handle updating an existing office
                $fieldToUpdate = mysqli_real_escape_string($conn, $fieldToUpdate); // Sanitize input
                $updatedValue = mysqli_real_escape_string($conn, $updatedValue);
                $query = "UPDATE Offices SET $fieldToUpdate = '$updatedValue' WHERE OfficeID = $itemID";
                mysqli_query($conn, $query);
            } elseif ($selectedAction == "editWorker") {
                // Handle updating an existing worker
                $fieldToUpdate = mysqli_real_escape_string($conn, $fieldToUpdate); // Sanitize input
                $updatedValue = mysqli_real_escape_string($conn, $updatedValue);
                $query = "UPDATE Workers SET $fieldToUpdate = '$updatedValue' WHERE WorkerID = $itemID";
                mysqli_query($conn, $query);
            }
            $userRole = $_GET['role'];
            $userID = $_GET['userID'];
            // Redirect to office.php with both user ID and role
            header("Location: office.php?userID=$userID&role=$userRole");
        }

        // Handle office or worker update
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["updateAction"])) {
                $selectedAction = $_POST["updateAction"];
                $itemID = $_POST["itemID"];
                $fieldToUpdate = $_POST["fieldToUpdate"];
                $updatedValue = $_POST["updatedValue"];

                handleUpdate($conn, $selectedAction, $itemID, $fieldToUpdate, $updatedValue);
            } elseif (isset($_POST["addOffice"])) {
                // Handle adding a new office
                $newOfficeName = $_POST["newOfficeName"];
                $newOwnerName = $_POST["newOwnerName"];
                $newAddress = $_POST["newAddress"];
                $newLicense = $_POST["newLicense"];
                $newLicenseExpirationDate = $_POST["newLicenseExpirationDate"];
                $newLicenseExpirationFlag = isset($_POST["newLicenseExpirationFlag"]) ? 1 : 0;
                $newInsurance = $_POST["newInsurance"];
                $newRoyaltyPercentage = $_POST["newRoyaltyPercentage"];
                // Simple validation
                if (empty($newOfficeName) || empty($newOwnerName) || empty($newAddress) || empty($newLicense) || empty($newLicenseExpirationDate) || empty($newInsurance) || empty($newRoyaltyPercentage)) {
                    echo "Please fill in all required fields.";
                } else {
                    // Assuming $conn is your database connection
                    $stmt = $conn->prepare("INSERT INTO Offices (OfficeName, OwnerName, Address, License, LicenseExpirationDate, LicenseExpirationFlag, Insurance, RoyaltyPercentage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssiid", $newOfficeName, $newOwnerName, $newAddress, $newLicense, $newLicenseExpirationDate, $newLicenseExpirationFlag, $newInsurance, $newRoyaltyPercentage);

                    if ($stmt->execute()) {
                        echo "New office added successfully.";
                    } else {
                        echo "Error adding new office: " . $stmt->error;
                    }
                    
                    $userRole = $_GET['role'];
                    $userID = $_GET['userID'];
                    // Redirect to the same page to avoid form resubmission on page reload
                    header("Location: {$_SERVER['PHP_SELF']}");

                    $stmt->close();
                }
            } elseif (isset($_POST["addWorker"])) {
                // Handle adding a new worker
                $newWorkerFirstName = $_POST["newWorkerFirstName"];
                $newWorkerLastName = $_POST["newWorkerLastName"];
                $newPosition = $_POST["newPosition"];
                $newContactNumber = $_POST["newContactNumber"];
                $newEmail = $_POST["newEmail"];
                $newCertification = $_POST["newCertification"];
                $newWorkerOfficeID = $_POST["newWorkerOfficeID"];        
                // Simple validation
                if (empty($newWorkerFirstName) || empty($newWorkerLastName) || empty($newPosition) || empty($newContactNumber) || empty($newEmail) || empty($newCertification) || empty($newWorkerOfficeID)) {
                    echo "Please fill in all required fields.";
                } else {
                    // Assuming $conn is your database connection
                    $stmt = $conn->prepare("INSERT INTO Workers (FirstName, LastName, Position, ContactNumber, Email, Certification, OfficeID) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssi", $newWorkerFirstName, $newWorkerLastName, $newPosition, $newContactNumber, $newEmail, $newCertification, $newWorkerOfficeID);

                    if ($stmt->execute()) {
                        echo "New worker added successfully.";
                    } else {
                        echo "Error adding new worker: " . $stmt->error;
                    }

                    $userRole = $_GET['role'];
                    $userID = $_GET['userID'];
                    // Redirect to office.php with both user ID and role
                    header("Location: office.php?userID=$userID&role=$userRole");

                    $stmt->close();
                }
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
                <a href="job.php">Jobs</a>
                <a href="clients.php">Clients</a>
                <a href="equipment.php">Equipment</a>
            </div>
            <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
            <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
            </a>
        </div>
    </header>
    <div class="container">
        <h2>User Information</h2>
        <label>Username: <?php echo $userData['UserName']; ?></label>
        <label>Role: <?php echo $userData['Role']; ?></label>
        <label>Office ID: <?php echo $userData['OfficeID']; ?></label>

        <?php if ($userRole === 'Owner') : ?>
            <h2>All Offices</h2>
            <table>
                <tr>
                    <th>Office ID</th>
                    <th>Office Name</th>
                    <th>Manager Name</th>
                    <th>Address</th>
                    <th>License</th>
                    <th>License Exiration Date</th>
                    <th>Expiration Warning</th>
                    <th>Insurance</th>
                    <th>Royalty Percentage</th>
                    <th>Update</th>
                </tr>
                <?php foreach ($allOffices as $office) : ?>
                    <tr>
                        <td><?php echo $office['OfficeID']; ?></td>
                        <td><?php echo $office['OfficeName']; ?></td>
                        <td><?php echo $office['OwnerName']; ?></td>
                        <td><?php echo $office['Address']; ?></td>
                        <td><?php echo $office['License']; ?></td>
                        <td><?php echo $office['LicenseExpirationDate']; ?></td>
                        <td>
                            <?php if ($office['LicenseExpirationFlag'] == 1): ?>
                                <span style="color: red;">Expiration Warning!</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $office['Insurance']; ?></td>
                        <td><?php echo $office['RoyaltyPercentage']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="itemID" value="<?= $office['OfficeID']; ?>">
                                <input type="hidden" name="updateAction" value="editOffice">
                                <label for="fieldToUpdate">Select Field:</label>
                                <select name="fieldToUpdate">
                                    <option value="OfficeName">Office Name</option>
                                    <option value="OwnerName">Manager Name</option>
                                    <option value="Address">Address</option>
                                    <option value="License">License</option>
                                    <option value="LicenseExpirationDate">License Expiration Date</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="RoyaltyPercentage">Royalty Percentage</option>
                                </select>
                                <label for="updatedValue">Updated Value:</label>
                                <input type="text" name="updatedValue" required>
                                <input type="submit" value="Update">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <form method="post" action="">
                <label for="newOfficeName">New Office Name:</label>
                <input type="text" id="newOfficeName" name="newOfficeName" required><br>
                <label for="newOwnerName">Manager Name:</label>
                <input type="text" id="newOwnerName" name="newOwnerName" required><br>
                <label for="newAddress">Address:</label>
                <input type="text" id="newAddress" name="newAddress" required><br>
                <label for="newLicense">License:</label>
                <input type="text" id="newLicense" name="newLicense" required><br>
                <label for="newLicenseExpirationDate">License Expiration Date:</label>
                <input type="date" id="newLicenseExpirationDate" name="newLicenseExpirationDate" required><br>
                <label for="newLicenseExpirationFlag">License Expiration Flag (Don't Check):</label>
                <input type="checkbox" id="newLicenseExpirationFlag" name="newLicenseExpirationFlag"><br>
                <label for="newInsurance">Insurance:</label>
                <input type="text" id="newInsurance" name="newInsurance" required><br>
                <label for="newRoyaltyPercentage">Royalty Percentage:</label>
                <input type="text" id="newRoyaltyPercentage" name="newRoyaltyPercentage" required><br>
                <input type="submit" name="addOffice" value="Add Office">
            </form>

            <h2>All Workers</h2>
            <table>
                <tr>
                    <th>Worker ID</th>
                    <th>Worker Name</th>
                    <th>Position</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Certification</th>
                    <th>Office ID</th>
                    <th>Update</th>
                </tr>
                <?php foreach ($allWorkers as $worker) : ?>
                    <tr>
                        <td><?php echo $worker['WorkerID']; ?></td>
                        <td><?php echo $worker['FirstName'], " ", $worker['LastName']; ?></td>
                        <td><?php echo $worker['Position']; ?></td>
                        <td><?php echo $worker['ContactNumber']; ?></td>
                        <td><?php echo $worker['Email']; ?></td>
                        <td><?php echo $worker['Certification']; ?></td>
                        <td><?php echo $worker['OfficeID']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="itemID" value="<?= $worker['WorkerID']; ?>">
                                <input type="hidden" name="updateAction" value="editWorker">
                                <label for="fieldToUpdate">Select Field:</label>
                                <select name="fieldToUpdate">
                                    <option value="FirstName">First Name</option>
                                    <option value="LastName">Last Name</option>
                                    <option value="Position">Position</option>
                                    <option value="ContactNumber">Contact Number</option>
                                    <option value="Email">Email</option>
                                    <option value="Certification">Certification</option>
                                </select>
                                <label for="updatedValue">Updated Value:</label>
                                <input type="text" name="updatedValue" required>
                                <input type="submit" value="Update">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <form method="post" action="">
                <label for="newWorkerFirstName">New Worker First Name:</label>
                <input type="text" id="newWorkerFirstName" name="newWorkerFirstName" required><br>
                <label for="newWorkerLastName">New Worker Last Name:</label>
                <input type="text" id="newWorkerLastName" name="newWorkerLastName" required><br>
                <label for="newPosition">Position:</label>
                <input type="text" id="newPosition" name="newPosition" required><br>
                <label for="newContactNumber">Contact Number:</label>
                <input type="text" id="newContactNumber" name="newContactNumber" required><br>
                <label for="newEmail">Email:</label>
                <input type="email" id="newEmail" name="newEmail" required><br>
                <label for="newCertification">Certification:</label>
                <input type="text" id="newCertification" name="newCertification" required><br>
                <label for="newWorkerOfficeID">Office ID:</label>
                <input type="text" id="newWorkerOfficeID" name="newWorkerOfficeID" required><br>
                <input type="submit" name="addWorker" value="Add Worker">
            </form>

        <?php elseif ($userRole === 'Manager') : ?>
            <h2>Managed Office Information</h2>
            <table>
                <tr>
                    <th>Office ID</th>
                    <th>Office Name</th>
                    <th>Manager Name</th>
                    <th>Address</th>
                    <th>License</th>
                    <th>License Exiration Date</th>
                    <th>Expiration Warning</th>
                    <th>Insurance</th>
                    <th>Royalty Percentage</th>
                    <th>Update</th>
                </tr>    
                <tr>
                    <td><?php echo $managedOffice['OfficeID']; ?></td>
                    <td><?php echo $managedOffice['OfficeName']; ?></td>
                    <td><?php echo $managedOffice['OwnerName']; ?></td>
                    <td><?php echo $managedOffice['Address']; ?></td>
                    <td><?php echo $managedOffice['License']; ?></td>
                    <td><?php echo $managedOffice['LicenseExpirationDate']; ?></td>
                    <td>
                        <?php if ($managedOffice['LicenseExpirationFlag'] == 1): ?>
                            <span style="color: red;">Expiration Warning!</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $managedOffice['Insurance']; ?></td>
                    <td><?php echo $managedOffice['RoyaltyPercentage']; ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="itemID" value="<?= $managedOffice['OfficeID']; ?>">
                            <input type="hidden" name="updateAction" value="editOffice">
                            <label for="fieldToUpdate">Select Field:</label>
                            <select name="fieldToUpdate">
                                <option value="OfficeName">Office Name</option>
                                <option value="OwnerName">Manager Name</option>
                                <option value="Address">Address</option>
                                <option value="License">License</option>
                                <option value="LicenseExpirationDate">License Expiration Date</option>
                                <option value="LicenseExpirationFlag">License Expiration Flag</option>
                                <option value="Insurance">Insurance</option>
                                <option value="RoyaltyPercentage">Royalty Percentage</option>
                            </select>
                            <label for="updatedValue">Updated Value:</label>
                            <input type="text" name="updatedValue" required>
                            <input type="submit" value="Update">
                        </form>
                    </td>
                </tr>
            </table>

            <h2>Workers in Managed Office</h2>
            <table>
                <tr>
                    <th>Worker ID</th>
                    <th>Worker Name</th>
                    <th>Position</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Certification</th>
                    <th>Office ID</th>
                    <th>Update</th>
                </tr>
                <?php foreach ($workersInOffice as $worker) : ?>
                    <tr>
                        <td><?php echo $worker['WorkerID']; ?></td>
                        <td><?php echo $worker['FirstName'], " ", $worker['LastName']; ?></td>
                        <td><?php echo $worker['Position']; ?></td>
                        <td><?php echo $worker['ContactNumber']; ?></td>
                        <td><?php echo $worker['Email']; ?></td>
                        <td><?php echo $worker['Certification']; ?></td>
                        <td><?php echo $worker['OfficeID']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="itemID" value="<?= $worker['WorkerID']; ?>">
                                <input type="hidden" name="updateAction" value="editWorker">
                                <label for="fieldToUpdate">Select Field:</label>
                                <select name="fieldToUpdate">
                                    <option value="FirstName">First Name</option>
                                    <option value="LastName">Last Name</option>
                                    <option value="Position">Position</option>
                                    <option value="ContactNumber">Contact Number</option>
                                    <option value="Email">Email</option>
                                    <option value="Certification">Certification</option>
                                </select>
                                <label for="updatedValue">Updated Value:</label>
                                <input type="text" name="updatedValue" required>
                                <input type="submit" value="Update">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($userRole === 'Worker') : ?>
            <h2>Worker Information</h2>
            <label>Worker ID: <?php echo $workerData['WorkerID']; ?></label>
            <label>Worker Name: <?php echo $workerData['FirstName'], " ", $workerData['LastName']; ?></label>
            <label>Position: <?php echo $workerData['Position']; ?></label>
            <label>Contact Number: <?php echo $workerData['ContactNumber']; ?></label>
            <label>Email: <?php echo $workerData['Email']; ?></label>
            <label>Certification: <?php echo $workerData['Certification']; ?></label>
            <label>Office Id: <?php echo $workerData['OfficeID']; ?></label>
            <label>Update:
                <form method="post" action="">
                    <input type="hidden" name="itemID" value="<?= $workerData['WorkerID']; ?>">
                    <input type="hidden" name="updateAction" value="editWorker">
                    <label for="fieldToUpdate">Select Field:</label>
                    <select name="fieldToUpdate">
                        <option value="FirstName">First Name</option>
                        <option value="LastName">Last Name</option>
                        <option value="Position">Position</option>
                        <option value="ContactNumber">Contact Number</option>
                        <option value="Email">Email</option>
                        <option value="Certification">Certification</option>
                    </select>
                    <label for="updatedValue">Updated Value:</label>
                    <input type="text" name="updatedValue" required>
                    <input type="submit" value="Update">
                </form>
            </label>
        <?php endif; ?>
    </div>
</body>
</html>