<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_response'])) {
    $message = $_POST['message'];
    $recipients = $_POST['recipients']; // Array of selected contact numbers
    if (empty($recipients)) {
        echo "<script>alert('No recipients selected.'); window.history.back();</script>";
        exit;
    }
    $token = 'cf08d16eac5ce663c8f7bed33418b745'; // Replace with your actual token

    foreach ($recipients as $phone) {
        $send_data = [
            'mobile' => '+63' . $phone,
            'message' => $message,
            'token' => $token
        ];

        $parameters = json_encode($send_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://app.qproxy.xyz/api/sms/v1/send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        $get_sms_status = curl_exec($ch);
        curl_close($ch);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
// Assign session role correctly
$user_role = $_SESSION['user_role'];

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Count total announcements with optional filtering
$sql = "SELECT COUNT(*) AS total FROM rest WHERE 1";

if ($search) {
    $sql .= " AND (
        LOWER(zone) LIKE '%" . strtolower($search) . "%'
        OR LOWER(barangay) LIKE '%" . strtolower($search) . "%'
        OR LOWER(sex) LIKE '%" . strtolower($search) . "%'
    )";
}

// Role-based filtering (example based on barangay or scope of announcements)
if ($user_role === 'Admin Lower Jasaan') {
    $sql .= " AND barangay = 'Lower Jasaan'";
} elseif ($user_role === 'Admin Jampason') {
    $sql .= " AND barangay = 'Jampason'";
} elseif ($user_role === 'Super Admin') {
    // Super Admin sees all data
    // No additional filters
}

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];

// Pagination setup
$records_per_page = 10;
$total_pages = ceil($total_records / $records_per_page);
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$start_from = ($current_page - 1) * $records_per_page;

// Fetch announcements with filtering and pagination
$sql = "SELECT * FROM rest WHERE 1";

if ($search) {
    $sql .= " AND (
        LOWER(zone) LIKE '%" . strtolower($search) . "%'
        OR LOWER(barangay) LIKE '%" . strtolower($search) . "%'
        OR LOWER(sex) LIKE '%" . strtolower($search) . "%'
    )";
}

if ($user_role === 'Admin Lower Jasaan') {
    $sql .= " AND barangay = 'Lower Jasaan'";
} elseif ($user_role === 'Admin Jampason') {
    $sql .= " AND barangay = 'Jampason'";
}

$sql .= " ORDER BY barangay DESC LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Send Announcement</title>
    <style>
         body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgba(238,238,238,255);
            color: #333;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        h1 .date-time {
            font-size: 16px;
            color: #007bff;
            margin-left: 20px; /* Add some space between the title and date */
            text-align: right;
        }
        form {
            max-width: 1100px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            font-size: 16px;
            color: #555;
            display: block;
            margin-bottom: 8px;
        }
        .announcement-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        textarea {
            flex: 1;
            height: 120px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
            resize: none;
            transition: border-color 0.3s ease;
        }
        textarea:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .check-all-container {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .check-all-container label {
            margin: 0;
        }
        button {
            background-color: #2596be;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            display: block;
            margin: 20px auto 0;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .action-checkbox {
            text-align: center;
        }
        .check-all-container {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 15px; /* Add margin to the top */
}
.sidebar .logo .logo-name{
        margin-left: 10px; /* Add space below the logo */
}

nav {
    display: flex;
    justify-content: space-between; /* To align items on opposite sides */
    align-items: center;
    padding: 15px;
    background-color: #343a40;
}

.navbar-content {
    display: flex;
    justify-content: flex-end; /* Aligns the content (time, profile, notification) to the right */
    gap: 15px; /* Add some space between the items */
    font-size: 15px;
    color: black;
}

.navbar-content a {
    color: #fbc02d;
}

.theme-toggle {
    margin: 0px; /* Adjust spacing around the toggle */
}

#date-time-container {
    margin-left: 20px; /* Optional space between date/time and other content */
}
    </style>
    <script>
        // Handle "Check All" functionality
        function toggleCheckAll(source) {
            const checkboxes = document.querySelectorAll('input[name="recipients[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }
    </script>
</head>
<body>
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
                <a href="logout.php" class="logout">
                    <i class='bx bx-log-out-circle'></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
  <div class="content">
        <nav>
    <i class='bx bx-menu'></i> <!-- Hamburger Icon on the left -->
    <div class="navbar-content">
        <div id="date-time-container"></div>
        <input type="checkbox" id="theme-toggle" hidden>
        <label for="theme-toggle" class="theme-toggle"></label>
        <a href="#" class="profile">
            <img src="images/logo-x.png">
        </a>
    </div>
</nav> 
<div class="container mt-4"> <h1>Announcements</h1>
<form style = "background-color: #f0f0f0; "method="GET" action="announcement.php" class="form-inline mb-3" style="display: flex; align-items: center;">
<input 
    type="text" 
    name="search" 
    class="form-control mr-2" 
    placeholder="Search by Barangay, Sex, or Zone..." 
    style="width: 450px; background-color: white;" 
    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
>
    <button type="submit" class="btn btn-primary" style="margin-left: 2px; margin-bottom: 18px; width: 100px;">Search</button>
</form></div>
<div>
<form method="post">
            <h2>Send Announcement</h2>
            <div class="announcement-container">
                <textarea id="message" name="message" placeholder="Write your announcement here..." required></textarea>
            </div>
            
            <div class="check-all-container">
                <input type="checkbox" id="check_all" onclick="toggleCheckAll(this)">
                <label for="check_all">Check All</label>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Barangay</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
// SQL for filtering by search parameters
$search_barangay = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT id, CONCAT(firstname, ' ', lastname) AS name, contactnum, barangay, sex, zone 
        FROM rest 
        WHERE contactnum IS NOT NULL";

if ($search_barangay) {
    $sql .= " AND (
        barangay LIKE '%$search_barangay%' 
        OR LOWER(sex) = '" . strtolower($search_barangay) . "' 
        OR zone LIKE '%$search_barangay%'
    )";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['barangay']) . '</td>';
        echo '<td>' . htmlspecialchars($row['contactnum']) . '</td>';
        echo '<td class="action-checkbox"><input type="checkbox" name="recipients[]" value="' . $row['contactnum'] . '"></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No residents found with the given filters.</td></tr>';
}
?>

                </tbody>
            </table>
            <button type="submit" name="send_response">Send Announcement</button>
        </form>
    </div>
    <script src="index.js"></script>
    <script>
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
    document.getElementById('search-btn').addEventListener('click', function(event) {
    const searchQuery = document.getElementById('search-input').value.trim();
    if (searchQuery === "") {
        event.preventDefault(); // Prevent the form from submitting if the search input is empty.
        alert("Please enter a search query.");
    }
});
</script>
</body>
</html>
