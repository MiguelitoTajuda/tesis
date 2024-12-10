<?php
// Include config.php for database connection
include 'config.php';

// Function to search activities
function searchActivities($conn, $search_term) {
    // Escape the search term for security
    $search_term = '%' . $conn->real_escape_string($search_term) . '%';

    // Prepare the SQL query to search in barangay, date, and activity name columns
    $sql = "SELECT * FROM activities WHERE barangay LIKE ? OR date LIKE ? OR actname LIKE ?";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    return $stmt->get_result();
}

// Check if search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $activitiesResult = searchActivities($conn, $search_term);
} else {
    // Default query to fetch all activities if no search term is provided
    $sql = "SELECT * FROM activities";
    $activitiesResult = $conn->query($sql);
}

// Handle delete action
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Prepare and execute delete query
    $delete_sql = "DELETE FROM activities WHERE id=?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);
    if($delete_stmt->execute()) {
        // Redirect back to this page after deletion
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "<p style='color: red;'>Error deleting activity: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Registered Activities</title>
</head>
<style>
     body {
        font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgba(238,238,238,255);
            color: #333;
        }

        .activity-container {
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding: 20px;
            gap: 20px;
        }

           .activity {
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative; /* Added for positioning comment form */
    align-items: center; /* Center contents horizontally */
    margin-bottom: 20px; /* Add space below each activity container */
}

.activity img {
    margin-top: 10px;
    width: 80%; /* Make the image take the full width of the container */
    height: 500px; /* Set a fixed height */
    object-fit: cover; /* Crop the image to fit without distortion */
}

.activity-details {
    padding: 15px;
    text-align: center; /* Center text within activity details */
}

       

        .comment {
            color: black;
            font-size: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            width: 100%; /* Full width for comments */
        }



        .comment .profile-pic {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-text {
            flex: 1;
        }

        .comment-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            color: black;
            margin-top: 10px;
        }

        .comment-toggle i {
            margin-right: 5px;
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

        .container {

background-color: #f8f9fa; /* Light background color */
border: 1px solid #dee2e6; /* Light gray border */
border-radius: 0.3rem; /* Rounded corners */
padding: 20px; /* Padding inside the container */
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
margin-top: 5px; /* Space above the container */
}

    h2 {
            margin: 10px 0;
            color: #007bff;
        }

        .theme-toggle {
    margin: 0px; /* Adjust spacing around the toggle */
    font-size: 16px;
}
button:hover {
            background-color: #0056b3;
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
            <form action="?" method="GET">
    <div class="form-input">
        <input type="search" name="search" placeholder="Search by barangay, date, or activity name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
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
<div class="container mt-4">
    <h1>Activity List</h1>
    <div class="add-activity-btn" style="text-align: left; margin-top: 10px;">
        <a href="activities.php" 
           style="display: inline-block; 
                  background-color: #0056b3; 
                  color: white; 
                  padding: 10px 20px;
                  margin-bottom:10px;
                  font-size: 14px; 
                  border-radius: 5px; 
                  font-size: 15px;
                  font-weight: bold;
                  text-decoration: none; 
                  transition: background-color 0.3s ease, transform 0.2s ease; 
                  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                  
            Manage Activities
        </a>
    </div>
    <?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'jk');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    // Modify the query to search for activities based on the search term
    $sql = "SELECT id, actname, barangay, date, picture, description FROM activities WHERE actname LIKE '%$search_term%' OR description LIKE '%$search_term%'";
} else {
    // Default query to show all activities
    $sql = "SELECT id, actname, barangay, date, picture, description FROM activities";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $activity_id = $row["id"];
        // Fetch comments for each activity
        $comments_sql = "SELECT * FROM comments WHERE activity_id = $activity_id";
        $comments_result = $conn->query($comments_sql);
        $comment_count = $comments_result->num_rows;

        echo '<div class="activity">';
        echo '<img src="' . $row["picture"] . '" alt="Activity Image">';
        echo '<div class="activity-details">';
        echo '<h2>' . $row["actname"] . '</h2>';
        echo '<p class="activity-barangayname"><strong>' . $row["barangay"] . '</strong></p>';
        echo '<p class="activity-date"><strong>' . $row["date"] . '</strong></p>';
        echo '<p class="activity-description">' . $row["description"] . '</p>';
        echo '<div class="comment-toggle" data-count="' . ($comment_count > 0 ? $comment_count : '0') . '" onclick="toggleComments(' . $activity_id . ', this)">';
        echo '<i class="bx bx-comment"></i>' . ($comment_count > 0 ? $comment_count . ' Comments' : 'No Comments');
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<div class="comments-container" id="comments-' . $activity_id . '">';

        // Display comments
        if ($comment_count > 0) {
            while ($comment_row = $comments_result->fetch_assoc()) {
                echo '<div class="comment">';
                echo '<img class="profile-pic" src="images/default.png" alt="Profile Picture">';
                echo '<div class="comment-text">' . $comment_row["comment"] . '</div>';
                echo '</div>';
            }
        }

        // Comment form
        echo '<form class="comment-form" method="post" action="submit_comment.php" style="position: relative; margin-top: 10px;">';
        echo '<input type="hidden" name="activity_id" value="' . $activity_id . '">';
        echo '<textarea name="comment" placeholder="Add your comment" rows="2" style="flex: 1; padding-right: 40px; width: 100%;"></textarea>';
        echo '<button type="submit" style="position: absolute; right: 10px; top: 5px; border: none; background: none; cursor: pointer;" title="Send Comment">';
        echo '<i class="bx bx-send" style="color: #444; font-size: 20px;"></i>'; // Send icon
        echo '</button>';
        echo '</form>';
        echo '</div>';
    }
} else {
    echo "0 results";
}

$conn->close();
?>

        </div>
     
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
function toggleComments(activityId, element) {
    const commentsContainer = document.getElementById(`comments-${activityId}`);
    const commentCount = element.getAttribute('data-count');

    if (commentsContainer.style.display === "none" || commentsContainer.style.display === "") {
        commentsContainer.style.display = "block";
        element.innerHTML = '<i class="bx bx-comment"></i> Hide Comments';
    } else {
        commentsContainer.style.display = "none";
        // Restore the original text
        if (commentCount === "0") {
            element.innerHTML = '<i class="bx bx-comment"></i> No Comments';
        } else {
            element.innerHTML = `<i class="bx bx-comment"></i> ${commentCount} Comments`;
        }
    }
}



function toggleComments(activityId, element) {
    const commentsContainer = document.getElementById(`comments-${activityId}`);
    const commentCount = element.getAttribute('data-count');

    if (commentsContainer.style.display === "none" || commentsContainer.style.display === "") {
        commentsContainer.style.display = "block";
        element.innerHTML = '<i class="bx bx-comment"></i> Hide Comments';
    } else {
        commentsContainer.style.display = "none";
        // Restore the original text
        if (commentCount === "0") {
            element.innerHTML = '<i class="bx bx-comment"></i> No Comments';
        } else {
            element.innerHTML = `<i class="bx bx-comment"></i> ${commentCount} Comments`;
        }
    }
}

        </script>
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
