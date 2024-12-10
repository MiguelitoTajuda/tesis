<?php
// Include config.php for database connection
include 'config.php';

// Function to sanitize user input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Prepare and execute delete query
    $delete_sql = "DELETE FROM activities WHERE id=?";
    if ($delete_stmt = $conn->prepare($delete_sql)) {
        $delete_stmt->bind_param("i", $id);
        if ($delete_stmt->execute()) {
            // Redirect back to this page after deletion
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            echo "<p style='color: red;'>Error deleting activity: " . $conn->error . "</p>";
        }
        $delete_stmt->close();
    } else {
        echo "<p style='color: red;'>Error preparing delete statement: " . $conn->error . "</p>";
    }
}

$sql = "SELECT COUNT(*) AS total FROM activities";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];

$records_per_page = 100; // Number of records to display per page
$total_pages = ceil($total_records / $records_per_page); // Calculate total pages

$current_page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number, default to 1 if not set

// Validate current page value
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Calculate the starting record for the current page
$start_from = ($current_page - 1) * $records_per_page;

// Fetch data from the resident table with pagination
$sql = "SELECT * FROM activities ORDER BY date LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Registered Activities</title>
    <style>
        /* Styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            text-align: left !important;
        }

        th {
            background-color: #f2f2f2 !important;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9 !important;
        }

        tr:hover {
            background-color: #f2f2f2 !important;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            background-color: #0056b3; 
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #0056b3; 
        }

        .action-buttons {
    display: flex;            /* Enable flexbox */
    justify-content: center;  /* Center horizontally */
    align-items: center;      /* Center vertically (if needed) */
    gap: 10px;                /* Space between buttons */
}


        .action-buttons a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
        }

        .action-buttons a:hover {
            color: #0056b3;
        }

        /* Styles for Add Activity button */
        #addActivityBtn {
            background-color: #0056b3; /* Green background */
            color: white; /* White text */
            padding: 10px 15px; /* Padding */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor */
            text-align: center; /* Centered text */
            text-decoration: none; /* No underline */
            font-size: 16px; /* Font size */
        }

        #addActivityBtn:hover {
            background-color: gray; /* Darker green on hover */
        }
    </style>
</head>
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
            <li>
            <br><br><br><br><br><br><br><br><br><br><br><br>
                <a href="#" class="logout"><i class='bx bx-log-out-circle'></i>Logout</a></li>
        </ul>
    </div>
    <!-- End of Sidebar -->

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
            <a href="#" class="profile">
                <img src="images/logo-x.png" alt="Profile">
            </a>
        </nav>
        <!-- End of Navbar -->

        <main>
            <div class="header">
                <div class="left">
                    <h1>Activities</h1>
                    <button id="addActivityBtn"><a href="registerA.php" style="text-decoration:none; color:inherit;">+ Activity</a></button>
                </div>
            </div>
            <div class="bottom-data">
                <table>
                    <thead>
                        <tr>
                            <th>Activity Name</th>
                            <th>Barangay</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Picture</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are activities
                        if ($result && $result->num_rows > 0) {
                            // Loop through results and display each activity in a table row
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . sanitizeInput($row["actname"]) . '</td>';
                                echo '<td>' . sanitizeInput($row["barangay"]) . '</td>';
                                echo '<td>' . sanitizeInput($row["date"]) . '</td>';
                                echo '<td>' . sanitizeInput($row["description"]) . '</td>';
                                echo '<td><img src="' . sanitizeInput($row["picture"]) . '" alt="Activity Picture" style="max-width: 100px; height: auto;"></td>';
                                echo '<td class="action-buttons">';
                                echo '<a href="editA.php?id=' . sanitizeInput($row["id"]) . '"><i class="bx bx-edit"></i> Edit</a>';
                                echo '<a href="?action=delete&id=' . sanitizeInput($row["id"]) . '" onclick="return confirm(\'Are you sure you want to delete this activity?\')"><i class="bx bx-trash"></i> Delete</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6">No activities registered yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <?php
                    // Pagination links
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href='?page=$i'";
                        if ($i == $current_page) echo " class='active'";
                        echo ">$i</a>";
                    }
                    ?>
                </div>
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
