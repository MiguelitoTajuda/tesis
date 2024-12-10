<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

// Include the config.php file for database connection
require_once('config.php');

// Check user role from session or database (Assuming session is used for user authentication)
$user_role = $_SESSION['user_role']; // Replace with actual logic to fetch user role

// Default condition for fetching all records
$barangay_condition = "";

// Check the user role and set the barangay condition
if ($user_role === 'Admin Lower Jasaan') {
    $barangay_condition = " AND h.barangay = 'Lower Jasaan' ";
} elseif ($user_role === 'Admin Kimaya') {
    $barangay_condition = " AND h.barangay = 'Kimaya' ";
} elseif ($user_role === 'Admin Jampason') {
    $barangay_condition = " AND h.barangay = 'Jampason' ";
} elseif ($user_role === 'Admin Upper Jasaan') {
    $barangay_condition = " AND h.barangay = 'Upper Jasaan' ";
} elseif ($user_role === 'Admin Bobontugan') {
    $barangay_condition = " AND h.barangay = 'Bobontugan' ";
} elseif ($user_role === 'Admin Solana') {
    $barangay_condition = " AND h.barangay = 'Solana' ";
} elseif ($user_role === 'Admin Danao') {
    $barangay_condition = " AND h.barangay = 'Danao' ";
} elseif ($user_role === 'Admin San Nicolas') {
    $barangay_condition = " AND h.barangay = 'San Nicolas' ";
} elseif ($user_role === 'Admin Natubo') {
    $barangay_condition = " AND h.barangay = 'Natubo' ";
} elseif ($user_role === 'Admin Corales') {
    $barangay_condition = " AND h.barangay = 'Corales' ";
} elseif ($user_role === 'Admin IS Cruz') {
    $barangay_condition = " AND h.barangay = 'IS Cruz' ";
} elseif ($user_role === 'Admin San Antonio') {
    $barangay_condition = " AND h.barangay = 'San Antonio' ";
} elseif ($user_role === 'Admin Aplaya') {
    $barangay_condition = " AND h.barangay = 'Aplaya' ";
} // No condition for Super Admin, they can view all

// Fetch total count of household heads (where is_leader = 1) with the role-based barangay filter
$sql = "SELECT COUNT(*) AS total FROM rest h WHERE h.is_leader = 1" . $barangay_condition;

// Add the search condition to the query if a search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];

    // Check if the search term is numeric, indicating a family count search
    if (is_numeric($search)) {
        // Search for exact match on family count
        $sql .= " AND (SELECT COUNT(*) FROM rest WHERE household_leader_id = h.id) = '$search'";
    } else {
        // Search across other fields (firstname, lastname, etc.)
        $sql .= " AND (h.firstname LIKE '%$search%' OR h.middlename LIKE '%$search%' OR h.lastname LIKE '%$search%' 
                  OR CONCAT(h.municipality, ', ', h.barangay, ', ', h.zone) LIKE '%$search%')";
    }
}
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];

$records_per_page = 100;
$total_pages = ceil($total_records / $records_per_page);

$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$start_from = ($current_page - 1) * $records_per_page;

// Fetch household heads with pagination and family member count
$sql = "SELECT h.id, h.firstname, h.middlename, h.lastname, h.municipality, h.barangay, h.zone,
        (SELECT COUNT(*) FROM rest WHERE household_leader_id = h.id) AS family_count
        FROM rest AS h
        WHERE h.is_leader = 1" . $barangay_condition;

// Add the search condition to the query if a search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];

    // Check if the search term is numeric, indicating a family count search
    if (is_numeric($search)) {
        // Search for exact match on family count
        $sql .= " AND (SELECT COUNT(*) FROM rest WHERE household_leader_id = h.id) = '$search'";
    } else {
        // Search across other fields (firstname, lastname, etc.)
        $sql .= " AND (h.firstname LIKE '%$search%' OR h.middlename LIKE '%$search%' OR h.lastname LIKE '%$search%' 
                  OR CONCAT(h.municipality, ', ', h.barangay, ', ', h.zone) LIKE '%$search%')";
    }
}

$sql .= " LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Household</title>

    <style>
        /* Add some basic styles for table and layout */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table th {
            background-color: #0056b3; 
            color: white;
            font-weight: bold;
        }

        .styled-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .styled-table tr:hover {
            background-color: #f1f1f1;
        }

        .styled-table td a {
            text-decoration: none;
            color: #007BFF;
        }

        .styled-table td a:hover {
            color: #0056b3;
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

        .pagination a:hover {
            background-color: #0056b3; 
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
        <br><br><br><br><br><br><br><br><br><br><br><br>
            <li><a href="#" class="logout"><i class='bx bx-log-out-circle'></i>Logout</a></li>
        </ul>
    </div>
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form method="GET" action="">
    <div class="form-input">
        <input type="search" name="search" placeholder="Search by name, family count, or address" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
    </div>
</form>
            <div id="date-time-container"></div>
            <input type="checkbox" id="theme-toggle" hidden>
            <label for="theme-toggle" class="theme-toggle"></label>
            <a href="#" class="profile"><img src="images/logo-x.png"></a>
        </nav>

        <main>
            <div class="header">
                <h1>Households</h1>
            </div>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Family Count</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $full_name = $row["firstname"] . " " . $row["middlename"] . " " . $row["lastname"];
                                $address = $row["municipality"] . ", " . $row["barangay"] . ", " . $row["zone"];
                                $family_count = $row["family_count"];

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($full_name) . "</td>";
                                echo "<td>" . htmlspecialchars($family_count) . "</td>";
                                echo "<td>" . htmlspecialchars($address) . "</td>";
                                echo "<td><a href='viewR.php?id=" . urlencode($row["id"]) . "' title='View Household'><i class='bx bx-show'></i></a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No household leaders found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <?php
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
