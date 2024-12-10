<?php

// Include the database configuration file
include 'config.php';  

function searchAttractions($conn, $search_term) {
    // Escape the search term for security
    $search_term = '%' . $conn->real_escape_string($search_term) . '%';

    // Prepare the SQL query to search in barangay, Type, and Name columns
    $sql = "SELECT * FROM activities WHERE barangay LIKE ? OR date LIKE ? OR actname LIKE ?";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    return $result;
}

// Check if search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $attractionsResult = searchAttractions($conn, $search_term);
} else {
    // Default query to fetch all attractions if no search term is provided
    $sql = "SELECT * FROM activities";
    $attractionsResult = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #eeeeee;
            font-family: 'Poppins', sans-serif;
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

        .comments-container {
            background-color: white;
            padding: 10px;
            border-radius: 10px;
            margin-top: auto;
            display: none; /* Hide comments by default */
            width: 80%; /* Make comments container full width */
            margin-left: 108px;
        }

        .comment {
    color: black;
    font-size: 14px;
    margin-bottom: 10px; /* Add margin for spacing between comments */
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Align items to the left */
    width: 100%; /* Full width for comments */
}

.comment .profile-pic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 10px; /* Space between the profile picture and comment text */
}

.comment-text {
    flex: 1;
    padding: 5px 10px; /* Padding to make the comment more readable */
    background-color: #f8f9fa; /* Light background for the comment text */
    border-radius: 10px; /* Rounded corners for each comment */
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
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

    <li class="">
        <a href="complaint.php" onclick="redirectTo('complaint.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-comment-detail'></i>Complaints
        </a>
    </li>
    <li class="">
        <a href="graph.php" onclick="redirectTo('graph.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-bar-chart-alt-2'></i>Statistics
        </a>
    </li>
    <li class="active">
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
    <div class="content">
        <div class="container mt-4">
            <h1>Events</h1>
            
            <form method="GET" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search events..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
<?php
// Check if search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $attractionsResult = searchAttractions($conn, $search_term);
} else {
    // Default query to fetch all attractions if no search term is provided
    $sql = "SELECT id, actname, barangay, date, picture, description FROM activities";
    $attractionsResult = $conn->query($sql);
}

if ($attractionsResult->num_rows > 0) {
    // Loop through the results and display them
    while ($row = $attractionsResult->fetch_assoc()) {
        $activity_id = $row["id"];
        // Fetch comments for each activity
        $comments_sql = "SELECT * FROM comments WHERE activity_id = $activity_id";
        $comments_result = $conn->query($comments_sql);
        $comment_count = $comments_result->num_rows;

        echo '<div class="activity">';
        echo '<img src="' . $row["picture"] . '" alt="Activity Image">';
        echo '<div class="activity-details">';
        echo '<h2>' . $row["actname"] . '</h2>';
        echo '<p class="activity-barangayname">' . $row["barangay"] . '</p>';
        echo '<p class="activity-description">' . $row["description"] . '</p>';
        echo '<p class="activity-date">' . $row["date"] . '</p>';
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
    echo "<p>No results found for your search.</p>";
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
        </script>
    </div>

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
