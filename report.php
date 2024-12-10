<?php

session_start();

// Check if the user is logged in by verifying session variables ('user_id' and 'barangay')
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    // Redirect to login.php if not logged in
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>JASAANKNOWN</title>

</head>

<style>
    .sidebar .logo .logo-name{
        margin-left: 10px; /* Add space below the logo */
}

.content main .insights li:nth-child(1) .bx{
    background: var(--grey);
    color: var(--primary);
}

.content main .insights li:nth-child(2) .bx{
    background: var(--grey);
    color: var(--warning);
}

.content main .insights li:nth-child(3) .bx{
    background: var(--grey);
    color: var(--success);
}

.content main .insights li:nth-child(4) .bx{
    background: var(--grey);
    color: var(--danger);
}
.content main .insights li:nth-child(5) .bx{
    background: var(--grey);
}
.content main .insights li:nth-child(6) .bx{
    background: var(--grey);
    color: var(--danger);
}
</style>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <ul class="side-menu">
    <li class="active"><a href="dashboard.php" onclick="redirectTo('dashboard.php')"><i class='bx bx-home'></i>Dashboard</a></li>
    <li class=""><a href="barangay.php" onclick="redirectTo('barangay.php')"><i class='bx bx-map'></i>Barangays</a></li>
    <li class=""><a href="users.php" onclick="redirectTo('users.php')"><i class='bx bx-user'></i>Users</a></li>
    <li class=""><a href="archive.php" onclick="redirectTo('archive.php')"><i class='bx bx-folder'></i>Archive</a></li>
</ul>

        <ul class="side-menu">
            <br><br><br><br><br><br><br><br><br><br><br><br>
            <li>
                <a href="logout.php" class="logout">
                    <i class='bx bx-log-out-circle'></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <div id="date-time-container"></div>
            <input type="checkbox" id="theme-toggle" hidden>
            <label for="theme-toggle" class="theme-toggle"></label>
            <a href="#" class="profile">
                <img src="images/logo-x.png">
            </a>
        </nav>

        <!-- End of Navbar -->

        <main>
            <div class="header">
                <div class="left">
                    <h1>Report Templates</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">
                            
                            </a></li>
                    </ul>
                </div>
              
            </div>

            <!-- Insights -->
            <ul class="insights">
            <li>
    <a href="reports.php">
        <i class='bx bx-pie-chart-alt-2'></i>
        <span style="color:black; font-weight:italic;">Barangay Residents by Age Bracket</span>
    </a>
</li>

    <li>
        <a href="reports_1.php">
            <i class='bx bx-group'></i>
            <span style="color:black; font-weight:italic;">Barangay Population and Household Number</span>
        </a>
    </li>
    <li>
    <a href="reports_2.php">
        <i class='bx bx-location-plus'></i>
        <span style="color:black; font-weight:italic;">Population per Barangay's Zones</span>
    </a>
</li>

    <li>
    <a href="reports_3.php">
        <i class='bx bxs-id-card'></i> <!-- Changed icon here -->
        <span style="color:black; font-weight:italic;">Voter Residents per Barangay</span>
    </a>
</li>

    <li>
        <a href="reports_4.php">
            <i class='bx bx-home'></i>
            <span style="color:black; font-weight:italic;">Residents per Barangay</span>
        </a>
    </li>
</ul>



    </div>

    <script src="index.js"></script>

    <script>
         function displayDateTime() {
        const dateTimeContainer = document.getElementById("date-time-container");
        const now = new Date();

        // Format the date and time
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = now.toLocaleDateString(undefined, options); // e.g., November 16, 2024
        const formattedTime = now.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' }); // e.g., 02:45:20 PM

        // Display the date and time in the container
        dateTimeContainer.textContent = `Date: ${formattedDate} | Time: ${formattedTime}`;
    }

    // Call the function on page load
    displayDateTime();

    // Update the time every second
    setInterval(displayDateTime, 1000);
    </script>

</body>

</html>