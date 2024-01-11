<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="eoc.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="eoc.js"></script>
    <title>EOC Environmental</title>
</head>
<body>
    <header>
    <!-- Top Navigation Menu -->
    <div class="topnav">
        <a href="eoc.php" class="logolink">
            <img src="logo.png" alt="Logo" class="logo">
        </a>
        <!-- Navigation links (hidden by default) -->
        <div id="myLinks">
            <a href="login.php">Login</a>
        </div>
        <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
        <i class="fa fa-bars"></i>
        </a>
    </div>
    </header>

    <main>
        <div class="main-content">
            <h1 class="blue">Access Any Part of the Database Here</h1>

            <div class="card">
                <h2>Jobs</h2>
                <a href="job.php">
                    <button>Access Form</button>
                </a>
            </div>

            <div class="card">
                <h2>Clients</h2>
                <a href="clients.php">
                    <button>Access Information</button>
                </a>
            </div>

            <div class="card">
                <h2>Office</h2>
                <a href="login.php">
                    <button>Access Information</button>
                </a>
            </div>

            <div class="card">
                <h2>Equipment</h2>
                <a href="equipment.php">
                    <button>Access Information</button>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
