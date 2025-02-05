<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

// Include the config.php file for database connection
require_once('config.php');

function calculateAge($birthdate) {
    $dob = new DateTime($birthdate);
    $now = new DateTime();
    $difference = $now->diff($dob);
    return $difference->y;
}

// Assign session role correctly
$user_role = $_SESSION['user_role'];

$search = isset($_GET['search']) ? $_GET['search'] : '';
// Count total records with optional filtering for different roles
$sql = "SELECT COUNT(*) AS total FROM resarchive WHERE 1";

// Filter records based on search input (age, gender, status)
if ($search) {
    // Calculate age dynamically from birthdate and filter by search term
    $sql .= " AND (YEAR(CURDATE()) - YEAR(birthdate) LIKE '%$search%' 
    OR LOWER(sex) LIKE '%" . strtolower($search) . "%' 
    OR LOWER(employment) LIKE '%" . strtolower($search) . "%')";

}

// Add any role-based filters (for admin, if needed)
if ($user_role === 'Admin Lower Jasaan') {
    $sql .= " AND barangay = 'Lower Jasaan'";
} elseif ($user_role === 'Admin Jampason') {
    $sql .= " AND barangay = 'Jampason'";
} elseif ($user_role === 'Admin Kimaya') {
    $sql .= " AND barangay = 'Kimaya'";
} elseif ($user_role === 'Admin Solana') {
    $sql .= " AND barangay = 'Solana'";
}  elseif ($user_role === 'Super Admin') {
    // Super Admin sees all data, no additional filter applied
    // No changes to $sql here
}

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];

// Pagination setup
$records_per_page = 100;
$total_pages = ceil($total_records / $records_per_page);
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$start_from = ($current_page - 1) * $records_per_page;

// Fetch filtered data based on the search input and pagination
$sql = "SELECT * FROM resarchive WHERE 1";
if ($search) {
    // Calculate age dynamically from birthdate and filter by search term
    $sql .= " AND (
        (YEAR(CURDATE()) - YEAR(birthdate)) LIKE '%$search%' 
        OR LOWER(sex) = '" . strtolower($search) . "'
          OR LOWER(lastname) = '" . strtolower($search) . "'
        OR LOWER(barangay) LIKE '%" . strtolower($search) . "%'
        OR LOWER(employment) ='" . strtolower($search) . "'
    )";
}

if ($user_role === 'Admin Lower Jasaan') {
    $sql .= " AND barangay = 'Lower Jasaan'";
} elseif ($user_role === 'Admin Jampason') {
    $sql .= " AND barangay = 'Jampason'";
} elseif ($user_role === 'Admin Kimaya') {
    $sql .= " AND barangay = 'Kimaya'";
} elseif ($user_role === 'Admin Solana') {
    $sql .= " AND barangay = 'Solana'";  
} elseif ($user_role === 'Super Admin') {
    // Super Admin sees all data, no additional filter applied
    // No changes to $sql here
}

if (isset($_GET['action']) && $_GET['action'] == 'retrieve' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the record from `resarchive`
    $sql = "SELECT * FROM resarchive WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Insert the record into `rest` table
        $insertSql = "INSERT INTO rest (
            lastname, suffix, firstname, middlename, birthdate, birthplace, sex, civilstatus, 
            religion, voter_status, municipality, barangay, zone, employment, contactnum, 
            emailadd, highesteduc, age, is_leader, household_leader_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param(
            "ssssssssssssssssiiii",
            $row['lastname'], $row['suffix'], $row['firstname'], $row['middlename'], $row['birthdate'], 
            $row['birthplace'], $row['sex'], $row['civilstatus'], $row['religion'], $row['voter_status'], 
            $row['municipality'], $row['barangay'], $row['zone'], $row['employment'], $row['contactnum'], 
            $row['emailadd'], $row['highesteduc'], $row['age'], $row['is_leader'], $row['household_leader_id']
        );

        if ($insertStmt->execute()) {
            // Delete the record from `resarchive`
            $deleteSql = "DELETE FROM resarchive WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {
                // Redirect with success message
                header("Location: resarc.php?message=retrieved");
                exit();
            } else {
                echo "Error deleting record from archive: " . $conn->error;
            }

            $deleteStmt->close();
        } else {
            echo "Error inserting record into residents table: " . $conn->error;
        }

        $insertStmt->close();
    } else {
        echo "No record found with ID: $id";
    }

    $stmt->close();
}



if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the deletion query
    $sql = "DELETE FROM resarchive WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Successful deletion, redirect to the archive page with a success message
        header("Location: resarc.php?message=deleted");
        exit();
    } else {
        // Error in deletion
        echo "Error deleting record: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
}



$sql .= " ORDER BY lastname LIMIT $start_from, $records_per_page";
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
    <title>Residents</title>

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
        #addResidentsBtn {
        background-color: gray;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        cursor: pointer;
        margin-bottom: 20px;
    }

    /* Hover effect */
    #addResidentsBtn:hover {
        background-color: gray;
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

.dropdown-menu {
    display: none; /* Initially hidden */
}

.dropdown-menu.show {
    display: block; /* Show when toggled */
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
    <li class=""><a href="dashboard.php" onclick="redirectTo('dashboard.php')"><i class='bx bx-home'></i>Dashboard</a></li>
    <li class=""><a href="barangay.php" onclick="redirectTo('barangay.php')"><i class='bx bx-map'></i>Barangays</a></li>
    <li class=""><a href="users.php" onclick="redirectTo('users.php')"><i class='bx bx-user'></i>Users</a></li>
    <li class="archive-dropdown">
    <a href="archive.php" class="dropdown-toggle"><i class='bx bx-folder'></i> Archive</a>
            <ul class="dropdown-menu">
            <li class="active"><a href="resarc.php" onclick="redirectTo('resarc.php')"><i class='bx bx-archive'></i>Resident archive</a></li>
            <li><a href="arc.php">Complaints Archive</a></li>
            <li><a href="a2.php">Users Archive</a></li>
            </ul>
        </li>
    </ul>

        <ul class="side-menu">
            <li>
            <br><br><br><br><br><br><br><br><br><br><br><br>
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
        <nav>
            <i class='bx bx-menu'></i>
            <form action="residents.php" method="GET">
    <div class="form-input">
        <input type="search" name="search" placeholder="Search by Age, Gender, or Status..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
    </div>
</form>

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
                    <h1>Residents Archive</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">

                        </a></li>

                    </ul>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Barangay</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Action</th>
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
            echo "<td>" . calculateAge($row["birthdate"]) . "</td>"; // Calculate age using the bday column
            echo "<td>" . $row["barangay"] . "</td>";
            echo "<td>" . $row["sex"] . "</td>";
            echo "<td>" . $row["employment"] . "</td>"; // Assuming the column name is "employment_status"
            echo "<td>";
            echo "<a href='?action=retrieve&id=" . $row["id"] . "' class='retrieve-btn'><i class='bx bx-refresh'></i></a>";
            echo "&nbsp;&nbsp;&nbsp;"; 
            echo "<a href='?action=delete&id=" . $row["id"] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this resident?\")'><i class='bx bx-trash'></i></a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No residents found</td></tr>";
    }
    ?>
</tbody>

                </table>
                <div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <a href="residents.php?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="page-link"><?php echo $i; ?></a>
    <?php } ?>
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

    document.addEventListener("DOMContentLoaded", function () {
    const archiveToggle = document.querySelector(".archive-dropdown .dropdown-toggle");
    const dropdownMenu = document.querySelector(".archive-dropdown .dropdown-menu");

    // Check if the current page is "resarc.php" and make the dropdown stay open
    if (window.location.href.includes("resarc.php")) {
        dropdownMenu.classList.add("show");
    }

    archiveToggle.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default link behavior
        dropdownMenu.classList.toggle("show"); // Toggle the 'show' class
    });

    // Optional: Close the dropdown when clicking outside
    document.addEventListener("click", function (e) {
        if (!archiveToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.remove("show");
        }
    });
});


    
    </script>
</body>

</html>