<?php
// Include the database connection from config.php
include('config.php'); 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to search attractions
function searchAttractions($conn, $search_term) {
    // Escape the search term for security
    $search_term = '%' . $conn->real_escape_string($search_term) . '%';

    // Prepare the SQL query to search in barangay, Type, and Name columns
    $sql = "SELECT * FROM Attractions WHERE barangay LIKE ? OR Type LIKE ? OR Name LIKE ?";
    
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
    $sql = "SELECT * FROM Attractions";
    $attractionsResult = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Gala Spots</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
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

        h2 {
            margin: 10px 0;
            color: #007bff;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.3rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .image-container {
            position: relative;
            margin: 20px 0;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .image-grid img {
            width: 100%;
            aspect-ratio: 1;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        .image-grid img:hover {
            transform: scale(1.05);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }

        .close,
        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .close {
            top: 15px;
            right: 35px;
        }

        .prev {
            left: 15px;
        }

        .next {
            right: 15px;
        }

        .close:hover,
        .prev:hover,
        .next:hover {
            color: #bbb;
        }

        .add-attraction-btn {
            display: block;
            margin: 20px auto;
            text-align: center;
        }

        .add-attraction-btn a {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .add-attraction-btn a:hover {
            background-color: #0056b3;
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
    <li class="">
        <a href="events.php" onclick="redirectTo('events.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-calendar-event'></i>Events
        </a>
    </li>
    <li class="active">
        <a href="gala.php" onclick="redirectTo('gala.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-star'></i>Gala
        </a>
    </li>
</ul>
    </div>

    <div class="content">
        <div class="container mt-4">
            <h1>Attractions</h1>
            
            <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search gala..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
            if ($attractionsResult->num_rows > 0) {
                while ($attraction = $attractionsResult->fetch_assoc()) {
                    echo '<h2>' . htmlspecialchars($attraction['Name']) . '</h2>';
                    echo '<p><strong>Location:</strong> ' . htmlspecialchars($attraction['barangay']) . '</p>';
                    echo '<p><strong>Type:</strong> ' . htmlspecialchars($attraction['Type']) . '</p>';
                    echo '<p><strong>Description:</strong> ' . htmlspecialchars($attraction['Description']) . '</p>';

                    // Fetch images for this attraction
                    $attractionID = $attraction['ID'];
                    $stmt = $conn->prepare("SELECT PictureURL FROM Pictures WHERE AttractionID = ? LIMIT 8");
                    $stmt->bind_param("i", $attractionID);
                    $stmt->execute();
                    $picturesResult = $stmt->get_result();


                    $iframe_link = htmlspecialchars($attraction['google_map']);

                    // Parse the pb parameter from the iframe link
                    $parsed_url = parse_url($iframe_link);

                    // Check if the pb parameter exists
                    if (isset($parsed_url['query'])) {
                        parse_str($parsed_url['query'], $query_params);

                        if (isset($query_params['pb'])) {
                            // Extract the location name from the `pb` parameter
                            preg_match('/!2s([^!]+)/', $query_params['pb'], $matches);
                            $location_name = isset($matches[1]) ? urldecode($matches[1]) : "Unknown Location";

                            // Build the direct Google Maps link
                            $base_url = "https://www.google.com/maps/";
                            $direct_link = $base_url . "?q=" . urlencode($location_name);

                            // Output the dynamic link
                            echo '<a href="' . $direct_link . '" target="_blank" class="btn btn-info btn-sm">View Location</a>';

                        } else {
                            echo "Error: 'pb' parameter not found in the provided iframe link.";
                        }
                    } else {
                        echo "Error: Invalid iframe link format.";
                    }


                    echo '<div class="image-container" data-attraction-id="' . $attractionID . '">';
                    echo '<div class="image-grid">';

                    while ($row = $picturesResult->fetch_assoc()) {
                        echo '<img src="' . htmlspecialchars($row['PictureURL']) . '" alt="Attraction Image" onclick="openModal(' . $attractionID . ', \'' . htmlspecialchars($row['PictureURL']) . '\')">';
                    }


                    echo '</div></div><hr>';
                }
            } else {
                echo "<p>No attractions found.</p>";
            }

            $conn->close();
            ?>

            <!-- Modal -->
            <div id="myModal" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <span class="prev" onclick="changeImage(-1)">&#10094;</span>
                <span class="next" onclick="changeImage(1)">&#10095;</span>
                <div class="modal-content">
                    <img id="modalImg" src="" alt="Modal Image">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Image Modal functionality
        let images = [];
        let currentImageIndex = 0;

        function openModal(attractionID, src) {
            images = Array.from(document.querySelectorAll(`.image-container[data-attraction-id='${attractionID}'] .image-grid img`))
                .map(img => img.src);
            currentImageIndex = images.indexOf(src);
            updateModalImage(src);
            document.getElementById('myModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        function changeImage(direction) {
            currentImageIndex = (currentImageIndex + direction + images.length) % images.length;
            updateModalImage(images[currentImageIndex]);
        }

        function updateModalImage(src) {
            document.getElementById('modalImg').src = src;
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                closeModal();
            }
        }
    </script>

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