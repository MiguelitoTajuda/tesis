<?php
// Include database configuration
include 'config.php';
session_start();

// Check if the user is logged in by verifying the 'user_id' session variable
if (!isset($_SESSION['user_id'])) {
    // Redirect to login.php if not logged in
    header("Location: login.php");
    exit();
}

// Initialize the filter query and filter parameters
$filter_query = "";
$filter_params = [];
$filter_types = "";

// Check if there are filter parameters in the URL or form input
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filter_query .= " WHERE status = ?";
    $filter_params[] = $_GET['status'];
    $filter_types .= "s"; // 's' for string type
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    if (!empty($filter_query)) {
        $filter_query .= " AND category = ?";
    } else {
        $filter_query .= " WHERE category = ?";
    }
    $filter_params[] = $_GET['category'];
    $filter_types .= "s"; // 's' for string type
}

// Final SQL query with filters (if any)
$sql = "SELECT * FROM comparchive" . $filter_query . " ORDER BY date DESC";
$stmt = $conn->prepare($sql);

// Bind parameters for filtering if any exist
if (!empty($filter_params)) {
    $stmt->bind_param($filter_types, ...$filter_params);
}

$stmt->execute();
$complaints = $stmt->get_result();  // Get the filtered complaints

// Fetch all complaints if no filter is applied
if (empty($filter_params)) {
    $complaints = $conn->query("SELECT * FROM comparchive ORDER BY date DESC");
}

// Check if the form was submitted to add a comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    $complaint_id = $_POST['complaint_id'];
    $comment = $_POST['comment'];

    // Insert the comment into the cmmnt table
    $stmt = $conn->prepare("INSERT INTO cmmnt (complaint_id, comment) VALUES (?, ?)");
    $stmt->bind_param('is', $complaint_id, $comment);

    if ($stmt->execute()) {
        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error submitting comment: " . $stmt->error;
    }
}

// Prepare the SQL query to fetch complaints with comment count
$sql = "
    SELECT c.*, 
           (SELECT COUNT(*) FROM cmmnt WHERE complaint_id = c.id) AS comment_count 
    FROM comparchive c" . $filter_query . " ORDER BY date DESC
";

// Prepare and execute the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameters for filtering if any exist
if (!empty($filter_params)) {
    $stmt->bind_param($filter_types, ...$filter_params);
}

$stmt->execute();
$complaints = $stmt->get_result();  // Get the complaints with comment counts

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Complaint Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        background-color: #eeeeee;
        font-family: 'Poppins', sans-serif;
    }

    .badge {
        font-size: 0.75rem;
    }
    table {width: 60%;}

    .table-responsive {
        margin-top: 20px;
    }

    h1 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 20px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }

    .btn-sm {
        font-size: 0.875rem;
        margin-bottom: 10px;
    }

    .container {
        width: 90%; /* Change to desired width */
        max-width: 1500px; /* Optional: maximum width */
        margin: 20px auto; /* Center the container */
        padding: 20px; /* Padding inside the container */
        background-color: #f8f9fa; /* Light background color */
        border: 1px solid #dee2e6; /* Light gray border */
        border-radius: 0.3rem; /* Rounded corners */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    }

    .form-group label {
        font-weight: bold; /* Bold labels for emphasis */
    }

    .btn-primary {
        margin-left: 10px;
        background-color: #007bff; /* Primary button color */
        border-color: #007bff; /* Border color matching the button */
    }

    .btn-secondary {
        margin-right: 140px;
        background-color: #6c757d; /* Secondary button color */
        border-color: #6c757d; /* Border color matching the button */
    }

    .btn-primary:hover {
        background-color: #0056b3; /* Darker blue on hover for primary */
    }

    .btn-secondary:hover {
        background-color: #5a6268; /* Darker gray on hover for secondary */
    }

    .d-flex {
        gap: 10px; /* Space between buttons */
    }

    .badge-custom {
        font-size: 0.75rem;  /* Adjust font size as needed */
        border-radius: 0.5rem;  /* Rounded corners */
        color: white;  /* Text color */
    }

    /* Define specific background colors for each badge */
    .badge-type {
        background-color: #28a745; /* Info color for type */
    }

    .badge-date {
        background-color: #28a745; /* Secondary color for date */
    }

    .badge-barangay {
        background-color: #28a745; /* Success color for barangay */
    }

    /* New Styles for the Table */
    table {
        width: 0%; /* Make table full width */
        border-collapse: collapse; /* Merge table borders */
        margin-top: 20px; /* Space above the table */
    }

    th, td {
        padding: 12px; /* Padding inside table cells */
        text-align: left; /* Align text to the left */
        border: 1px solid #dee2e6; /* Light gray border for cells */
    }

    th {
        background-color: #007bff; /* Header background color */
        color: white; /* Header text color */
    }

    tr:nth-child(even) {
        background-color: #f2f2f2; /* Light gray background for even rows */
    }

    tr:hover {
        background-color: #e2e6ea; /* Darker gray on hover */
    }

    nav {
    display: flex;
    align-items: center; /* Vertically center items */
    justify-content: space-between; /* Space between items */
    padding: 10px 20px;
    background-color: #f8f9fa; /* Adjust the background color as needed */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#date-time-container {
    flex: 1; /* Allow it to grow and take available space */
    text-align: right; /* Center-align the text horizontally */
    font-size: 16px;
    color: #333; /* Adjust the text color as needed */
}

.theme-toggle {
    margin: 0px; /* Adjust spacing around the toggle */
}

.profile {
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile img {
    width: 40px; /* Adjust image size */
    height: 40px;
    border-radius: 50%; /* Make the profile image circular */
    object-fit: cover;
}


</style>

</head>
<body>
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
                <li><a href="resarc.php">Residents</a></li>
                <li><a href="comparchive.php">Complaints</a></li>
                <li><a href="users_archive.php">Users</a></li>
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
            <div id="date-time-container"></div>
            <input type="checkbox" id="theme-toggle" hidden>
            <label for="theme-toggle" class="theme-toggle"></label>
            <a href="#" class="profile">
                <img src="images/logo-x.png">
            </a>
        </nav>
    <div class="container mt-4">
        <h1>Complaint Dashboard</h1>

        <form method="GET" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search complaints..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        
        <!-- Display complaints in a table -->
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Title</th>
                    <th>Complaint</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Barangay</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $complaints->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <p><?php echo htmlspecialchars($row['complaint']); ?></p>

                            <!-- Button to Show/Hide Comments with Count -->
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleComments(<?php echo $row['id']; ?>)">
                                Show Comments (<?php echo $row['comment_count']; ?>)
                            </button>

                            <!-- Comments Section -->
                            <div class="comments-section" id="comments-section-<?php echo $row['id']; ?>" style="display: none;">
                                <!-- Fetch and Display Comments -->
                                <?php
                                    $stmt_comments = $conn->prepare("SELECT comment, date FROM cmmnt WHERE complaint_id = ? ORDER BY date DESC");
                                    $stmt_comments->bind_param('i', $row['id']);
                                    $stmt_comments->execute();
                                    $comments_result = $stmt_comments->get_result();
                                    while ($comment_row = $comments_result->fetch_assoc()) {
                                ?>
                                    <div class="comment">
                                        <small><strong>Date:</strong> <?php echo htmlspecialchars($comment_row['date']); ?></small><br>
                                        <p><?php echo htmlspecialchars($comment_row['comment']); ?></p>
                                    </div>
                                    <hr>
                                <?php } ?>

                                <!-- Comment Submission Form -->
                                <form action="" method="POST">
                                    <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                    <textarea name="comment" class="form-control mt-2" rows="2" placeholder="Add a comment..."></textarea>
                                    <button type="submit" name="submit_comment" class="btn btn-primary btn-sm mt-2">Submit</button>
                                </form>
                            </div>
                        </td>
                        <td><span class="badge badge-custom badge-type"><?php echo htmlspecialchars($row['type']); ?></span></td>
                        <td><span class="badge badge-custom badge-date"><?php echo htmlspecialchars($row['date']); ?></span></td>
                        <td><span class="badge badge-custom badge-barangay"><?php echo htmlspecialchars($row['barangay']); ?></span></td>
                        <td><span class="badge badge-warning"><?php echo htmlspecialchars($row['status']); ?></span></td>
                        <td>
                            <!-- Update Status Button -->
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#updateModal<?php echo $row['id']; ?>" style="color: white; width: 65px; height: 30px; font-size: 10px;">
                                <i class="fas fa-sync"></i> Status
                            </button>

                            <!-- View Details Button -->
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $row['id']; ?>" style="color: white; width: 65px; height: 30px; font-size: 10px;">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>

                    <!-- Modal for Updating Complaint Status -->
                    <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?php echo $row['id']; ?>">Update Status</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="" method="POST">
                                        <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                        <div class="form-group">
                                            <label for="status">Select Status:</label>
                                            <select name="status" class="form-control" required>
                                                <option value="pending">Pending</option>
                                                <option value="under observation">Under Observation</option>
                                                <option value="accomplished">Resolved</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Viewing Complaint Details -->
                    <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewModalLabel<?php echo $row['id']; ?>">Complaint Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Title:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                                    <p><strong>Complaint:</strong> <?php echo htmlspecialchars($row['complaint']); ?></p>
                                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                                    <p><strong>Barangay:</strong> <?php echo htmlspecialchars($row['barangay']); ?></p>
                                    <p><strong>Complainant:</strong> <?php echo htmlspecialchars($row['complainant']); ?></p>
                                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contactnum']); ?></p>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                                    <?php if ($row['file']) { ?>
                                        <p><strong>File:</strong> <a href="<?php echo $row['file']; ?>" target="_blank">View File</a></p>
                                    <?php } ?>
                                </div>
                                <div class="modal-footer">
                                        <form action="" method="POST">
                                            <input type="hidden" name="title" value="<?= $row['title'] ?>">
                                            <input type="hidden" name="mobile_no" value="<?= $row['contactnum'] ?>">
                                            <div class="form-group">
                                                <label for="message">Select Message:</label>
                                                <select name="message" class="form-control" required>
                                                    <option value="Good Day! Your complaint for '<?= $row['title'] ?>' is currently under observation. We will update you soon. Thank you for your patience.">Under Observation</option>
                                                    <option value="We need additional information regarding your complaint for '<?= $row['title'] ?>'. Kindly contact us for verification. Thank you.">Pending</option>
                                                    <option value="Your complaint for '<?= $row['title'] ?>' has been actioned and resolved. Thank you for your cooperation.">Actioned and Resolved</option>
                                                </select>
                                        </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="send_response" class="btn btn-success">Send Response</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<script>
function toggleComments(id) {
    const commentsSection = document.getElementById(`comments-section-${id}`);
    commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
}
</script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

<script src="index.js"></script>
</body>

</html>