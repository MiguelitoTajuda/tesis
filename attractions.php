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
    $delete_sql = "DELETE FROM attractions WHERE id=?";
    if ($delete_stmt = $conn->prepare($delete_sql)) {
        $delete_stmt->bind_param("i", $id);
        if ($delete_stmt->execute()) {
            // Redirect back to this page after deletion
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            echo "<p style='color: red;'>Error deleting attraction: " . $conn->error . "</p>";
        }
        $delete_stmt->close();
    } else {
        echo "<p style='color: red;'>Error preparing delete statement: " . $conn->error . "</p>";
    }
}

$sql = "SELECT COUNT(*) AS total FROM attractions";
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
$sql = "SELECT * FROM attractions ORDER BY barangay LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
      
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
    max-width: 500px;
    text-align: left;
}

.close-btn {
    float: right;
    font-size: 24px;
    cursor: pointer;
    color: red;
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
            <li class="active"><a href="dashboard.php"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li><a href="barangay.php"><i class='bx bxs-compass'></i>Barangays</a></li>
            <li><a href="users.php"><i class='bx bx-group'></i>Users</a></li>
            <li><a href="archive.php"><i class='bx bx-cog'></i>Archive</a></li>
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
                    <h1>Attractions</h1>
                    <button id="addActivityBtn"><a href="registerA.php" style="text-decoration:none; color:inherit;">Add attraction</a></button>
                </div>
            </div>
            <div class="bottom-data">
                <table>
                    <thead>
                        <tr>
                            <th>Attraction</th>
                            <th>Barangay</th>
                            <th>Type</th>
                            <th>Description</th>
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
                                echo '<td>' . sanitizeInput($row["Name"]) . '</td>';
                                echo '<td>' . sanitizeInput($row["barangay"]) . '</td>';
                                echo '<td>' . sanitizeInput($row["Type"]) . '</td>';
                                echo '<td>' . sanitizeInput($row["Description"]) . '</td>';
                               // Fetch images for this attraction
                               $attractionID = $row['ID']; // Use $row since it's defined in the loop
                               $stmt = $conn->prepare("SELECT PictureURL FROM Pictures WHERE AttractionID = ? LIMIT 8");
                               $stmt->bind_param("i", $attractionID);
                               $stmt->execute();
                               $picturesResult = $stmt->get_result();
                               
                                echo '<td class="action-buttons">';
                                echo '<a href="update_attractions.php" onclick="openEditModal(' . $row["ID"] . ', \'' . $row["Name"] . '\', \'' . $row["barangay"] . '\', \'' . $row["Type"] . '\', \'' . $row["Description"] . '\')"><i class="bx bx-edit"></i> Edit</a>';
                                echo '<a href="=' . sanitizeInput($row["ID"]) . '" onclick="return confirm(\'Are you sure you want to delete this activity?\')"><i class="bx bx-trash"></i> Delete</a>';
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

    <div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Edit Attraction</h2>
        <form id="editForm">
            <input type="hidden" id="edit-id" name="id">
            <div>
                <label for="edit-name">Attraction Name:</label>
                <input type="text" id="edit-name" name="name" required>
            </div>
            <div>
                <label for="edit-barangay">Barangay:</label>
                <input type="text" id="edit-barangay" name="barangay" required>
            </div>
            <div>
                <label for="edit-type">Type:</label>
                <input type="text" id="edit-type" name="type" required>
            </div>
            <div>
                <label for="edit-description">Description:</label>
                <textarea id="edit-description" name="description" rows="4" required></textarea>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>



    <script src="index.js"></script>

    <script>

function openEditModal(id, name, barangay, type, description) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-name").value = name;
    document.getElementById("edit-barangay").value = barangay;
    document.getElementById("edit-type").value = type;
    document.getElementById("edit-description").value = description;

    document.getElementById("editModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}

document.getElementById("editForm").addEventListener("submit", function(e) {
    e.preventDefault(); // Prevent form from submitting normally

    const formData = new FormData(this);

    fetch("update_attraction.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Attraction updated successfully!");
            location.reload(); // Reload the page to reflect changes
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while updating the attraction.");
    });
});


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
