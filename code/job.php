<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="job.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="eoc.js"></script>
    <title>EOC Environmental</title>
</head>
<body>
    <?php include 'connection.php'; ?>
    <?php
    // Function to sanitize user input
    function sanitizeInput($input) {
        return htmlspecialchars(stripslashes(trim($input)));
    }
    function executeQuery($sql) {
        global $conn;
        if ($conn->query($sql) === TRUE) {
            echo "Query executed successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        // Redirect to the same page to avoid form resubmission on page reload
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    function handleFileUpload($fileInputName, $uploadsDir) {
        $file_name = $_FILES[$fileInputName]['name'];
        $file_tmp = $_FILES[$fileInputName]['tmp_name'];
        
        // Sanitize the file name to remove special characters
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9_.-]/", "", $file_name);

        // Check if the file name is not empty
        if (!empty($sanitizedFileName)) {
            // Move the uploaded file to the designated folder
            move_uploaded_file($file_tmp, $uploadsDir . $sanitizedFileName);
        }

        // Return the sanitized file name (or NULL if empty)
        return !empty($sanitizedFileName) ? $sanitizedFileName : NULL;
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
        <div class="main-content">
            <h2>Job Management</h2>

            <!-- Add Job Form -->
            <form action="" method="post" enctype="multipart/form-data">
                <h3>Add Job</h3>
                <label for="workerName">Select Worker:</label>
                <select name="workerID">
                    <?php
                    // Fetch worker names and IDs from the Workers table
                    $result = $conn->query("SELECT WorkerID, CONCAT(FirstName, ' ', LastName) AS WorkerName FROM Workers");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['WorkerID'] . "'>" . $row['WorkerName'] . "</option>";
                    }
                    ?>
                </select><br>

                <label for="consultant">Consultant:</label>
                <input type="text" name="consultant" required><br>

                <label for="jobType">Job Type:</label>
                <input type="text" name="jobType"><br>

                Client or Company: 
                <input type="radio" name="clientOrCompany" value="client" checked> Client
                <input type="radio" name="clientOrCompany" value="company"> Company<br>
                Client/Company Name: <input type="text" name="clientOrCompanyName" required><br>

                <label for="email">Email:</label>
                <input type="email" name="email"><br>

                <label for="phone">Phone:</label>
                <input type="text" name="phone"><br>

                <label for="inspectionDate">Inspection Date:</label>
                <input type="date" name="inspectionDate"><br>

                <label for="inspectionTime">Inspection Time:</label>
                <input type="time" name="inspectionTime"><br>

                <label for="referralSource">Referral Source:</label>
                <input type="text" name="referralSource"><br>

                <label for="billingAddress">Billing Address:</label>
                <textarea name="billingAddress" rows="3"></textarea><br>

                <label for="subContractors">Sub-Contractors:</label>
                <input type="text" name="subContractors"><br>

                <label for="grossAmount">Gross Amount:</label>
                <input type="number" name="grossAmount" id="grossAmount"><br>

                <label for="notes">Notes:</label>
                <textarea name="notes" rows="3"></textarea><br>

                <div class="file-input-container">
                    <input type="file" id="fileInput" name="fileUpload" class="hidden">
                    <label for="fileInput" class="file-label">Choose a file</label>
                </div>

                <input type="submit" name="submitJob" value="Add Job">
            </form>

            <?php
            // Handle form submissions
            if (isset($_POST['submitJob'])) {
                // Retrieve form data
                $workerID = $_POST['workerID'] = !empty($_POST['workerID']) ? "'".$_POST['workerID']."'" : 'NULL';
                $consultant = $_POST['consultant'] = !empty($_POST['consultant']) ? "'".$_POST['consultant']."'" : 'NULL';
                $jobType = $_POST['jobType'] = !empty($_POST['jobType']) ? "'".$_POST['jobType']."'" : 'NULL';
                $clientOrCompany = $_POST['clientOrCompany'] = !empty($_POST['clientOrCompany']) ? "'".$_POST['clientOrCompany']."'" : 'NULL';
                $clientOrCompanyName = $_POST['clientOrCompanyName'] = !empty($_POST['clientOrCompanyName']) ? "'".$_POST['clientOrCompanyName']."'" : 'NULL';
                $email = $_POST['email'] = !empty($_POST['email']) ? "'".$_POST['email']."'" : 'NULL';
                $phone = $_POST['phone'] = !empty($_POST['phone']) ? "'".$_POST['phone']."'" : 'NULL';
                $inspectionDate = $_POST['inspectionDate'] = !empty($_POST['inspectionDate']) ? "'".$_POST['inspectionDate']."'" : 'NULL';
                $inspectionTime = $_POST['inspectionTime'] = !empty($_POST['inspectionTime']) ? "'".$_POST['inspectionTime']."'" : 'NULL';
                $referralSource = $_POST['referralSource'] = !empty($_POST['referralSource']) ? "'".$_POST['referralSource']."'" : 'NULL';
                $billingAddress = $_POST['billingAddress'] = !empty($_POST['billingAddress']) ? "'".$_POST['billingAddress']."'" : 'NULL';
                $subContractors = $_POST['subContractors'] = !empty($_POST['subContractors']) ? "'".$_POST['subContractors']."'" : 'NULL';
                $grossAmount = $_POST['grossAmount'] = !empty($_POST['grossAmount']) ? "'".$_POST['grossAmount']."'" : 'NULL';
                $notes = $_POST['notes'] = !empty($_POST['notes']) ? "'".$_POST['notes']."'" : 'NULL';

                $file_name = handleFileUpload('fileUpload', 'uploads/');

                // Check if the user entered a client or company name
                if ($clientOrCompany == 'client') {
                    // Call stored procedure for adding a job with a client
                    $sql = "CALL AddJobWithClient($workerID, $consultant, $jobType, $clientOrCompanyName, $email, $phone, $inspectionDate, $inspectionTime, $referralSource, $billingAddress, $subContractors, $grossAmount, $notes, '$file_name')";
                } else {
                    // Call stored procedure for adding a job with a company
                    $sql = "CALL AddJobWithCompany($workerID, $consultant, $jobType, $clientOrCompanyName, $email, $phone, $inspectionDate, $inspectionTime, $referralSource, $billingAddress, $subContractors, $grossAmount, $notes, '$file_name')";
                }

                executeQuery($sql);
            }
            ?>
        </div>
        <div class="allJobs">
            <?php
            // Handle filter form submission
            $filter = "all"; // Default filter
            $search = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $filter = sanitizeInput($_POST['filter']);
                $search = sanitizeInput($_POST['search']);
            }

            // Handle update form submission
            $updateField = "";
            $updateValue = "";
            $updateFile = "";

            if (isset($_POST['updateJobDetails']) && isset($_POST['selectedJobID'])) {
                $selectedJobID = sanitizeInput($_POST['selectedJobID']);
                $updateField = sanitizeInput($_POST['updateField']);
                $updateValue = sanitizeInput($_POST['updateValue']);
                $updateFile = $_FILES['updateFile'];  // Use $_FILES to handle file upload

                // Check if a file is being updated
                if ($updateField === "FileName" && $updateFile['error'] === UPLOAD_ERR_OK) {
                    $file_name = $updateFile['name'];
                    $file_tmp = $updateFile['tmp_name'];
                    move_uploaded_file($file_tmp, "uploads/$file_name"); // Move file to a designated folder

                    // Update the FileName field in the database
                    $updateSql = "UPDATE Jobs SET FileName = '$file_name' WHERE JobID = $selectedJobID";
                    if ($conn->query($updateSql) === TRUE) {
                        echo "File updated successfully.";
                    } else {
                        echo "Error updating file: " . $conn->error;
                    }
                } else {
                    // Update other fields in the database
                    $updateSql = "UPDATE Jobs SET $updateField = '$updateValue' WHERE JobID = $selectedJobID";
                    if ($conn->query($updateSql) === TRUE) {
                        echo "Job updated successfully.";
                    } else {
                        echo "Error updating job: " . $conn->error;
                    }
                }

                // Redirect to the same page to avoid form resubmission on page reload
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            }

            // Query to retrieve filtered jobs with related client and company information
            $sql = "SELECT 
                        Jobs.JobID,
                        Workers.WorkerID,
                        Jobs.Consultant,
                        Jobs.JobType,
                        Jobs.ClientName,
                        Jobs.CompanyName,
                        Jobs.InspectionDate,
                        Jobs.InspectionTime,
                        Jobs.ReferralSource,
                        Jobs.BillingAddress,
                        Jobs.SubContractors,
                        Jobs.CompletionDate,
                        Jobs.GrossAmount,
                        Jobs.Notes,
                        Jobs.FileName
                    FROM Jobs
                    LEFT JOIN Workers ON Jobs.WorkerID = Workers.WorkerID
                    LEFT JOIN Clients ON Jobs.ClientID = Clients.ClientID
                    LEFT JOIN Companies ON Jobs.CompanyID = Companies.CompanyID";

            // Apply filters
            if ($filter != "all") {
                $sql .= " WHERE ";
                switch ($filter) {
                    case "clientName":
                        $sql .= "Jobs.ClientName LIKE '%$search%'";
                        break;
                    case "companyName":
                        $sql .= "Jobs.CompanyName LIKE '%$search%'";
                        break;
                    case "inspectionDate":
                        $sql .= "Jobs.InspectionDate LIKE '%$search%'";
                        break;
                    case "consultant":
                        $sql .= "Jobs.Consultant LIKE '%$search%'";
                        break;
                    case "jobType":
                        $sql .= "Jobs.JobType LIKE '%$search%'";
                        break;
                }
            }

            $result = $conn->query($sql);

            // Display filter form
            echo '<form method="post" action="">
            <label for="filter">Filter by:</label>
            <select name="filter">
                <option value="all">Show All</option>
                <option value="clientName">Client Name</option>
                <option value="companyName">Company Name</option>
                <option value="inspectionDate">Inspection Date</option>
                <option value="consultant">Consultant</option>
                <option value="jobType">Job Type</option>
            </select>
            <label for="search">Search:</label>
            <input type="text" name="search" value="' . $search . '">
            <input type="submit" value="Apply Filters">
            </form>';

            // Display filtered jobs in a table
            echo '<table>
            <tr>
                <th>JobID</th>
                <th>Consultant</th>
                <th>Job Type</th>
                <th>Client Name</th>
                <th>Company Name</th>
                <th>Inspection Date</th>
                <th>Inspection Time</th>
                <th>Referral Source</th>
                <th>Billing Address</th>
                <th>SubContractors</th>
                <th>Completion Date</th>
                <th>Gross Amount</th>
                <th>Notes</th>
                <th>File Upload</th>
            </tr>';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>
                            <td>' . $row['JobID'] . '</td>
                            <td>' . $row['Consultant'] . '</td>
                            <td>' . $row['JobType'] . '</td>
                            <td>' . $row['ClientName'] . '</td>
                            <td>' . $row['CompanyName'] . '</td>
                            <td>' . $row['InspectionDate'] . '</td>
                            <td>' . $row['InspectionTime'] . '</td>
                            <td>' . $row['ReferralSource'] . '</td>
                            <td>' . $row['BillingAddress'] . '</td>
                            <td>' . $row['SubContractors'] . '</td>
                            <td>' . $row['CompletionDate'] . '</td>
                            <td>' . $row['GrossAmount'] . '</td>
                            <td>' . $row['Notes'] . '</td>
                            <td>';

                        if (!empty($row['FileName'])) {
                            echo '<a href=\'uploads/' . $row['FileName'] . '\' download>' . $row['FileName'] . '</a>';
                        }
                        
                        echo '</td>
                                <td>
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="selectedJobID" value="' . $row['JobID'] . '">
                                        <select name="updateField" onchange="toggleFileInput(this)">
                                            <option value="Consultant">Consultant</option>
                                            <option value="JobType">Job Type</option>
                                            <option value="InspectionDate">Inspection Date</option>
                                            <option value="InspectionTime">Inspection Time</option>
                                            <option value="ReferralSource">Referral Source</option>
                                            <option value="BillingAddress">Billing Address</option>
                                            <option value="SubContractors">SubContractors</option>
                                            <option value="CompletionDate">Completion Date</option>
                                            <option value="GrossAmount">Gross Amount</option>
                                            <option value="Notes">Notes</option>
                                            <option value="FileName">File</option>
                                        </select>';
                        
                        // Show file input only when "File" option is selected
                        echo '<input type="file" name="updateFile" accept=".pdf, .doc, .docx" style="display:none;">';
                        
                        echo '<input type="text" name="updateValue" placeholder="New Value">
                                        <input type="submit" name="updateJobDetails" value="Update Job">
                                    </form>
                                </td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="16">No jobs found.</td></tr>';
                }

                echo '</table>';

                // JavaScript function to toggle file input visibility
                echo '<script>
                        function toggleFileInput(select) {
                            var fileInput = select.parentNode.querySelector("input[name=\'updateFile\']");
                            fileInput.style.display = (select.value === "FileName") ? "block" : "none";
                        }
                    </script>';
                $conn->close();
            ?>
        </div>
    </main>
</body>
</html>