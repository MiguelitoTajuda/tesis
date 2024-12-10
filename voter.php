<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

// Include the config.php file for database connection
require_once('config.php');

// Assign session role correctly
$user_role = $_SESSION['user_role'];

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Base query for voter status
$sql = "SELECT * FROM rest WHERE voter_status = 'yes'";

// Filter records based on search input (sex, voter_status, employment, barangay)
if (!empty($search)) {
    $sql .= " AND (
        LOWER(sex) LIKE '%" . strtolower($search) . "%' OR
        LOWER(employment) LIKE '%" . strtolower($search) . "%' OR
        LOWER(barangay) LIKE '%" . strtolower($search) . "%'
    )";
}

// Add any role-based filters (for admin, if needed)
if ($user_role === 'Admin Lower Jasaan') {
    $sql .= " AND barangay = 'Lower Jasaan'";
} elseif ($user_role === 'Admin Jampason') {
    $sql .= " AND barangay = 'Jampason'";
} elseif ($user_role === 'Admin Kimaya') {
    $sql .= " AND barangay = 'Kimaya'";
} elseif ($user_role === 'Super Admin') {
    // Super Admin sees all data, no additional filter applied
    // No changes to $sql here
}

// Count total records
$count_query = str_replace("SELECT *", "SELECT COUNT(*) AS total", $sql);
$result = $conn->query($count_query);
$row = $result->fetch_assoc();
$total_records = $row['total'];

// Pagination setup
$records_per_page = 100;
$total_pages = ceil($total_records / $records_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$start_from = ($current_page - 1) * $records_per_page;

// Fetch paginated data
$sql .= " ORDER BY lastname LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Registered Voters</title>

</head>
    <title>Voters List</title>
    <style>
        
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

        .pagination a:hover {
            background-color: #0056b3; 
        }

        .sidebar .logo .logo-name{
        margin-left: 10px; /* Add space below the logo */
    }
        table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    thead {
        background-color: #0056b3; 
        color: white;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tbody tr:hover {
        background-color: #ddd;
    }

    th {
        background-color: #0056b3; 
        color: white;
    }

    .action-btn {
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    .action-btn:hover {
        background-color: #45a049;
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
                <a href="#" class="logout">
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
            <form action="?" method="GET">
            <form action="residents.php" method="GET">
    <div class="form-input">
        <input type="search" name="search" placeholder="Search by barangay, Gender, or employment..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
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
                    <h1>Registered Voters</h1>
                    <ul class="breadcrumb">
                        <li><a href="#"></a></li>
                    </ul>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Gender</th>
                            <th>Employment Status</th>
                            <th>Voter </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            
            // Concatenating the first name, middle name, last name, and extension
            $fullName = $row["lastname"];

            $fullName .= " " . $row["firstname"];
            
            // Append middle name if it exists
            if (!empty($row["middlename"])) {
                $fullName .= " " . $row["middlename"];
            }
            // Append last name
           
            
            // Append extension if it exists
            if (!empty($row["ext"])) {
                $fullName .= " " . $row["ext"];
            }

            echo "<td>" . $fullName . "</td>"; // Display the full name
            echo "<td>" . $row["barangay"] . "</td>";
            echo "<td>" . $row["sex"] . "</td>";
            echo "<td>" . $row["employment"] . "</td>"; 
            echo "<td>" . $row["voter_status"] . "</td>";// Assuming the column name is "employment_status"
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No residents found</td></tr>";
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