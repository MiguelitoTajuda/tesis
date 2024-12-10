<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is being submitted for insert or delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database configuration
    include 'config.php';
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collecting form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $barangay = $_POST['barangay'];

    // Check if password is provided, and hash it if it's not empty
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Check if a file (profile picture) was uploaded
    $pic = null;
    if (isset($_FILES['pic']) && $_FILES['pic']['error'] == UPLOAD_ERR_OK) {
        $pic = file_get_contents($_FILES['pic']['tmp_name']);
    }

    if (isset($_POST['delete_user_id'])) {
        // Get the user ID to be deleted
        $user_id_to_delete = $_POST['delete_user_id'];
    
        // Step 1: Archive the user before deletion
        $stmt_archive = $conn->prepare("INSERT INTO usersarchive (id, name, email, password, role, pic, barangay) 
                                        SELECT id, name, email, password, role, pic, barangay FROM user WHERE id = ?");
        $stmt_archive->bind_param("i", $user_id_to_delete);
    
        if ($stmt_archive->execute()) {
            // Step 2: Delete the user after archiving
            $stmt_delete = $conn->prepare("DELETE FROM user WHERE id = ?");
            $stmt_delete->bind_param("i", $user_id_to_delete);
    
            if ($stmt_delete->execute()) {
                echo "User archived and deleted successfully!";
            } else {
                echo "Error deleting user: " . $stmt_delete->error;
            }
        } else {
            echo "Error archiving user: " . $stmt_archive->error;
        }
    
        $stmt_delete->close();
        $stmt_archive->close();
    } else {
        // Insert new user logic
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Always hash the password for new user

        // Handle file upload if an image was provided
        if ($pic) {
            $stmt = $conn->prepare("INSERT INTO user (name, barangay, email, password, role, pic) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $barangay, $email, $hashed_password, $role, $pic);
        } else {
            $stmt = $conn->prepare("INSERT INTO user (name, barangay, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name,$barangay, $email, $hashed_password, $role);
        }

        if ($stmt->execute()) {
            // Redirect to users.php after successful registration
            header("Location: users.php");
            exit(); // Always call exit() after a header redirect
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>User List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 960px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 20px !important;
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

        tr:nth-child(odd) {
            background-color: #f9f9f9 !important;
        }

        tr:hover {
            background-color: #f2f2f2 !important;
        }


        h2 {
            margin-top: 0;
        }

        .main {
            background-color: transparent;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .action-btn {
                 background-color: #0056b3;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        cursor: pointer;
        margin-bottom: 20px;
        }

        .action-btn:hover {
            background-color: lightgrey;
        }

        .action-btn.edit {
            background-color:#4CAF50;
        }

        .action-btn.delete {
            background-color: #dc3545;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* 10% from top and centered */
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #888;
            width: 40%; /* Adjust width */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: red;
            text-decoration: none;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <ul class="side-menu">
    <li><a href="dashboard.php" onclick="redirectTo('dashboard.php')"><i class='bx bx-home'></i>Dashboard</a></li>
    <li><a href="barangay.php" onclick="redirectTo('barangay.php')"><i class='bx bx-map'></i>Barangays</a></li>
    <li class="active"><a href="users.php" onclick="redirectTo('users.php')"><i class='bx bx-user'></i>Users</a></li>
    <li><a href="archive.php" onclick="redirectTo('archive.php')"><i class='bx bx-folder'></i>Archive</a></li>
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

    <div class="content">
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

            <main>
                <div class="header">
                    <div class="left">
                        <h1>User List</h1>
                        <ul class="breadcrumb">
                            <li><a href="#"></a></li>
                        </ul>
                    </div>
                </div>
    <!-- Button to open the modal -->
    <button id="openModalButton" class="action-btn">+ User</button>

    <div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Register User</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <input type="text" id="role" name="role" required>
            </div>
            <div class="form-group">
                <label for="barangay">Barangay:</label>
                <input type="text" id="barangay" name="barangay" required>
            </div>
            <div class="form-group">
                <label for="pic">Profile Picture (optional):</label>
                <input type="file" id="pic" name="pic" accept="image/*">
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</div>
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th class="name">Name</th>
                            <th class="email">Email</th>
                            <th class="user-type">User Type</th>
                            <th class="action">Action</th>
                        </tr>
                        </thead>
                        <tbody>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jk";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, email, role FROM user";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td>";
        
        // Only show edit and delete buttons if the user is a 'Super Admin'
        if ($_SESSION['user_role'] === 'Super Admin') {
            echo "<button class='action-btn edit' onclick='openEditModal(" . $row['id'] . ")'>Update</button>";
            echo "<button class='action-btn delete' onclick='deleteUser(" . $row['id'] . ")'>Delete</button>";
        } else {
            echo "<button class='action-btn' onclick='showMessage()'>Edit</button>";
            echo "<button class='action-btn' onclick='showMessage()'>Delete</button>";
        }

        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No users found.</td></tr>";
}
$conn->close();
?>
</tbody>
                    </table>
                </div>
            </main>
        </div>
        <script src="index.js"></script>
        <script>
    function openEditModal(userId) {
        // Show the modal
        const modal = document.getElementById("registerModal");
        modal.style.display = "block";

        // Send AJAX request to update_user.php to get the form for editing user details
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "update_user.php?id=" + userId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Insert the response (the form) into the modal's content
                document.querySelector(".modal-content").innerHTML = xhr.responseText;
            } else {
                alert('Error loading user data.');
            }
        };
        xhr.send();
    }

    // Close the modal when the close button is clicked
    const closeModalBt = document.getElementById("closeModal");
    closeModalBt.addEventListener("click", () => {
        const modal = document.getElementById("registerModal");
        modal.style.display = "none";
    });

    // Close the modal if clicking outside the modal content
    window.addEventListener("click", (event) => {
        const modal = document.getElementById("registerModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    
</script>


        <script>

function showMessage() {
        alert("You can't make this action.");
    }

    const modal = document.getElementById("registerModal");
    const openModalBtn = document.getElementById("openModalButton");
    const closeModalBtn = document.getElementById("closeModal");

    openModalBtn.addEventListener("click", () => {
        modal.style.display = "block";
    });

    closeModalBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = 'delete_user.php?id=' + id;
        }
    }
            function displayDateTime() {
            const dateTimeContainer = document.getElementById("date-time-container");
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = now.toLocaleDateString(undefined, options); // e.g., November 16, 2024
            const formattedTime = now.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' }); // e.g., 02:45:20 PM
            dateTimeContainer.textContent = `Date: ${formattedDate} | Time: ${formattedTime}`;
        }
        displayDateTime();
        setInterval(displayDateTime, 1000);
        </script>
    </body>
    </html>
