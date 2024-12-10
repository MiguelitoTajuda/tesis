<?php
// Include database configuration
include 'config.php';
session_start();

// Initialize filter query and parameters array
$filter_query = " WHERE 1=1"; // Default condition (always true) to simplify appending filters
$filter_params = [];
$filter_types = "";

// Fetch all complaints, ensuring it’s always sorted by date descending
$sql = "SELECT * FROM complaints" . $filter_query . " ORDER BY date DESC";
$stmt = $conn->prepare($sql);
if (!empty($filter_params)) {
    $stmt->bind_param($filter_types, ...$filter_params);
}
$stmt->execute();
$complaints = $stmt->get_result();  // Get the filtered complaints

// Check if search term is provided and add to the query
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_terms = explode(' ', $_GET['search']); // Split the search query into keywords
    foreach ($search_terms as $term) {
        $term = '%' . $term . '%'; // Use wildcard for partial matches
        $filter_query .= " AND (title LIKE ? OR complaint LIKE ? OR Barangay LIKE ? OR status LIKE ?)";
        $filter_params[] = $term;
        $filter_params[] = $term;
        $filter_params[] = $term;
        $filter_params[] = $term;
        $filter_types .= "ssss"; // Update the type to include 's' for each parameter
    }
}

// Final SQL query with filters
$sql = "SELECT * FROM complaints" . $filter_query . " ORDER BY date DESC";
$stmt = $conn->prepare($sql);

// Bind parameters for filtering if any exist
if (!empty($filter_params)) {
    $stmt->bind_param($filter_types, ...$filter_params);
}

$stmt->execute();
$complaints = $stmt->get_result();  // Get the filtered complaints

// Check if the form was submitted to add a complaint
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $title = $_POST['title'];
    $complaint = $_POST['complaint'];
    $type = $_POST['type'];
    $date = date('Y-m-d H:i:s'); // Current date
    $barangay = $_POST['barangay'];
    $contactnum = $_POST['contactnum']; // New contact number
    $complainant = isset($_POST['complainant']) ? $_POST['complainant'] : ''; // Optional complainant name
    $file = '';

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $targetDir = "uploaded_files/";
        $targetFile = $targetDir . basename($_FILES["file"]["name"]);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Allow only specific file types
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'mp4'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $file = $targetFile;
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Only JPG, JPEG, PNG, PDF, DOC, and DOCX files are allowed.";
            exit();
        }
    }

    // Insert the complaint into the database
    $stmt = $conn->prepare("INSERT INTO complaints (title, complaint, type, date, barangay, contactnum, complainant, file, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param('ssssssss', $title, $complaint, $type, $date, $barangay, $contactnum, $complainant, $file);
    
    if ($stmt->execute()) {
        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error submitting complaint: " . $stmt->error;
    }
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
    FROM complaints c" . $filter_query . " ORDER BY date DESC
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
            background-color:#eeeeee;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            margin-bottom: 1rem;
    margin: 1rem 10px; /* Adjust margin as needed */
    border-radius: 8px;
    height: 100px; /* Set your desired height */
    margin-top: 0.4rem;
    
}

        .card-title {
            font-size: 1rem;
            margin-bottom: 0.3rem;
        }

        .card-text {
            font-size: 0.875rem;
            margin-bottom: 0.3rem;
        }

        .badge {
            font-size: 0.75rem;
            margin-bottom: 0.4rem;
        }

        .form-container {
            margin-bottom: 2rem;
        }
  
    .sidebar .logo .logo-name{
        margin-left: 10px; /* Add space below the logo */
}
.container {

    background-color: #f8f9fa; /* Light background color */
    border: 1px solid #dee2e6; /* Light gray border */
    border-radius: 0.3rem; /* Rounded corners */
    padding: 20px; /* Padding inside the container */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    margin-top: 5px; /* Space above the container */
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
h1 {
    font-size: 2.5rem; /* Larger font size for prominence */
    font-weight: bold; /* Bold text for emphasis */
    color: #343a40; /* Dark gray color for the text */
    margin-bottom: 20px; /* Space below the heading */
    text-align: center; /* Center the heading */
    text-transform: uppercase; /* Uppercase letters for a formal look */
    letter-spacing: 2px; /* Slightly increased letter spacing */
    border-bottom: 2px solid #007bff; /* Blue border under the heading */
    padding-bottom: 10px; /* Space between the text and the border */
}

.btn-submit {
    background-color: #28a745; /* Green background */
    color: white;
    border-color: #28a745; /* Green border */
    margin-bottom: 10px;
}

.btn-submit:hover {
    background-color: #218838; /* Darker green on hover */
    border-color: #1e7e34; /* Darker border on hover */
}
.complaints-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .complaint-item {
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .complaint-item:last-child {
            border-bottom: none; /* Remove the bottom border from the last item */
        }

        .complaint-item h3 {
            font-size: 1.25rem;
            color: #343a40;
            margin-bottom: 5px;
        }

        .complaint-item p {
            color: #6c757d;
            margin-bottom: 5px;
        }

        .complaint-item span {
            font-size: 0.875rem;
            display: inline-block;
            margin-right: 15px;
        }

        .nav-wrapper {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

/* Optional: Add space between the elements if needed */
nav a, nav label, #date-time-container {
    margin-left: 10px;
    margin-bottom: 150px;
}   
/* Default sidebar styles */
.sidebar {
    width: 250px;
    transition: width 0.3s;
    overflow: hidden;
    background-color: #343a40;
    color: #ffffff;
    height: 100vh;
}

.sidebar .logo .logo-name {
    font-size: 1.5rem;
    margin-left: 10px;
    
}

.side-menu li {
    padding: 10px;
    display: flex;
    align-items: center;
}

.side-menu li a {
    text-decoration: none;
    color: #ffffff;
    display: flex;
    align-items: center;
}

.side-menu li a .menu-text {
    margin-left: 10px;
    white-space: nowrap;
    transition: opacity 0.3s;
    
}

.sidebar.minimized {
    width: 50px;
}

.sidebar.minimized .logo-name {
    display: none;
}

.sidebar.minimized .menu-text {
    opacity: 0;
    pointer-events: none; /* Prevent text selection when hidden */
}

.sidebar.minimized li {
    justify-content: center;
}


</style>

<body>

<div class="sidebar" id="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <i class='bx bx-menu' id="toggle-sidebar" style="margin-left: 15px;"></i>

        <ul class="side-menu">
    <li class="">
        <a href="jskn/index.html" onclick="redirectTo('jskn/index.html')" style="background-color: #343a40; color: white;">
            <i class='bx bx-home'></i>Home
        </a>
    </li>

    <li class="active">
        <a href="complaint.php" onclick="redirectTo('complaint.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-comment-detail'></i>Complaints
        </a>
    </li>
    <li class="">
        <a href="graph.php" onclick="redirectTo('graph.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-bar-chart-alt-2'></i>Statistics
        </a>
    </li>
    <li class="">
        <a href="events.php" onclick="redirectTo('events.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-calendar-event'></i>Events
        </a>
    </li>
    <li class="">
        <a href="gala.php" onclick="redirectTo('gala.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-star'></i>Gala
        </a>
    </li>
</ul>

    </div>
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
    <div class="container mt-4">
    <h1>Complaints List</h1>
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search complaints..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="nav-wrapper">
    <nav>
        <div id="date-time-container"></div>
        <input type="checkbox" id="theme-toggle" hidden>
        <a href="#" class="profile">
            <img src="images/logo-x.png">
        </a>
    </nav>
</div>

    <h6> Do you have Concerns pls submit it here !</h6>
    <button type="submit" class="btn btn-submit" name="submit_complaint" data-target="#submitComplaintModal"  class="btn btn-primary float-right" data-toggle="modal">Submit Complaint</button>

    <div class="complaints-container">
    <?php if ($complaints->num_rows > 0) { ?>
        <?php while ($row = $complaints->fetch_assoc()) { ?>
            <div class="complaint-item">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['complaint']); ?></p>
                <span><strong>Barangay:</strong> <?php echo htmlspecialchars($row['barangay']); ?></span>
                <span><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></span>
                <span><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></span>

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
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No complaints found.</p>
    <?php } ?>
</div>


    </div>


<div class="modal fade" id="submitComplaintModal" tabindex="-1" aria-labelledby="submitComplaintModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitComplaintModalLabel" style="margin-right:100px;">Submit a Complaint</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="complaintTitle">Title</label>
                            <input type="text" class="form-control" id="complaintTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="complaintText">Complaint</label>
                            <textarea class="form-control" id="complaintText" name="complaint" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="complaintType">Type</label>
                            <select class="form-control" id="complaintType" name="type" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="Public">Public</option>
                                <option value="Private">Private</option>
                            </select>
                        </div>
                        <div class="form-group">
    <label for="complaintBarangay">Barangay</label>
    <select class="form-control" id="complaintBarangay" name="barangay" required>
        <option value="" disabled selected>Select Barangay</option>
        <option value="Jampason">Jampason</option>
        <option value="Lower Jasaan">Lower Jasaan</option>
        <option value="Upper Jasaan">Upper Jasaan</option>
        <option value="Danao">Danao</option>
        <option value="Bobontugan">Bobontugan</option>
        <option value="San Nicolas">San Nicolas</option>
        <option value="I.S Cruz">I.S Cruz</option>
        <option value="San Antonio">San Antonio</option>
        <option value="Kimaya">Kimaya</option>
        <option value="Luz Banson">Luz Banson</option>
        <option value="Aplaya">Aplaya</option>
        <option value="Solana">Solana</option>
        <option value="Corrales">Corrales</option>
        <option value="San Isidro">San Isidro</option>
        <option value="Natubo">Natubo</option>
        <!-- Add more barangays as needed -->
    </select>
</div>

                        <div class="form-group">
                            <label for="complainant">Complainant Name (Optional)</label>
                            <input type="text" class="form-control" id="complainant" name="complainant">
                        </div>
                        <div class="form-group">
                            <label for="contactnum">Contact Number</label>
                            <input type="text" class="form-control" id="contactnum" name="contactnum" required>
                        </div>

                        <div class="form-group">
                            <label for="complaintFile">Upload File</label>
                            <input type="file" class="form-control-file" id="complaintFile" name="file">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit_complaint">Submit Complaint</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


        <div class="row">
    <?php while ($row = $complaints->fetch_assoc()) { ?>
        <div class="col-md-6">  <!-- Change col-md-12 to col-md-6 -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($row['complaint']); ?></p>
                    <span class="badge badge-info"><?php echo htmlspecialchars($row['type']); ?></span>
                    <span class="badge badge-secondary"><?php echo htmlspecialchars($row['date']); ?></span>
                    <span class="badge badge-success"><?php echo htmlspecialchars($row['barangay']); ?></span>
                    <span class="badge badge-warning"><?php echo htmlspecialchars($row['status']); ?></span>
                </div>
            </div>
        </div>


    <?php } ?>
</div>
<script src="index.js"></script>
<script>
function toggleComments(id) {
    const commentsSection = document.getElementById(`comments-section-${id}`);
    commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

    document.getElementById('toggle-sidebar').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('minimized');
});

    </script>

</body>

</html>