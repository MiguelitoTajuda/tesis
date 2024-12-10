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
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">
                            
                            </a></li>
                    </ul>
                </div>
              
            </div>

            <!-- Insights -->
            <ul class="insights">
                <li>
                    <a href="residents.php">
                        <i class='bx bx-group'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Residents</span>
                    </a>
                </li>
                <li>
                    <a href="voter.php">
                        <i class='bx bxs-registered'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Registered Voters</span>
                    </a>
                </li>
                <li>
                    <a href="household.php">
                        <i class='bx bxs-building-house'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Households</span>
                    </a>
                </li>
                <li>
                    <a href="activitydash.php">
                        <i class='bx bxs-hand'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Activities</span>
                    </a>
                </li>
                <li>
                    <a href="admingraph.php">
                        <i class='bx bxs-bar-chart-alt-2'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Graphs</span>
                    </a>
                </li>
                <li>
                    <a href="complaints.php">
                        <i class='bx bx-comment-dots'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Complaints</span>
                    </a>
                </li>
                <li>
                    <a href="Report.php">
                        <i class='bx bxs-info-square'></i>
                        <span style="color:#0056b3; font-weight:bold ;">Reports</span>
                    </a>
                </li>
                <li>
    <a href="admingala.php">
        <i class='bx bxs-map-pin' style="color:#4CAF50; background-color:black;"></i> <!-- Green for Attractions -->
        <span style="color:#0056b3; font-weight:bold;">Attractions</span>
    </a>
</li>
<li>
    <a href="announcement.php">
        <i class='bx bxs-megaphone' style="color: pink; background-color: #FF5733;"></i> <!-- Orange for Announcements -->
        <span style="color:#0056b3; font-weight:bold;">Announcements</span>
    </a>
</li>
            </ul>
            <!-- End of Insights -->

            <div class="bottom-data">
                <div class="orders">
                    <div class="header">
                        <i class='bx bx-receipt'></i>
                        <h3>Recent Users</h3>
                        <i class='bx bx-filter'></i>
                        <i class='bx bx-search'></i>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Recent User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <img src="images/lovely.jpg">
                                    <p>Lovely Vanessa</p>
                                </td>
                                <td><span class="status completed">15 mins ago</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="images/che.jpg">
                                    <p>Cherlyn Marfe</p>
                                </td>
                                <td><span class="status pending">Active 1 hour ago</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="images/shaiwo.jpg">
                                    <p>Shairo James</p>
                                </td>
                                <td><span class="status process">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Reminders -->
                <div class="reminders">
                    <div class="header">
                        <i class='bx bx-note'></i>
                        <h3>Reminders</h3>
                        <i class='bx bx-filter'></i>
                        <i class='bx bx-plus'></i>
                    </div>
                    <ul class="task-list">
                        <li class="completed">
                            <div class="task-title">
                                <i class='bx bx-check-circle'></i>
                                <p>Start Our Meeting</p>
                            </div>
                            <i class='bx bx-dots-vertical-rounded'></i>
                        </li>
                        <li class="completed">
                            <div class="task-title">
                                <i class='bx bx-check-circle'></i>
                                <p>record submission</p>
                            </div>
                            <i class='bx bx-dots-vertical-rounded'></i>
                        </li>
                        <li class="not-completed">
                            <div class="task-title">
                                <i class='bx bx-x-circle'></i>
                                <p>thesis proposal</p>
                            </div>
                            <i class='bx bx-dots-vertical-rounded'></i>
                        </li>
                    </ul>
                </div>

                <!-- End of Reminders-->

            </div>

        </main>

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