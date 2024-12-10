<?php
// Include config.php for database connection
include 'config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to search attractions// Function to search attractions
function searchAttractions($conn, $search_term) {
    // Escape the search term for security
    $search_term = '%' . $conn->real_escape_string($search_term) . '%';

    // Prepare the SQL query to search in 'Type', 'barangay', and 'Name' columns
    $sql = "SELECT * FROM attractions WHERE Type LIKE ? OR barangay LIKE ? OR Name LIKE ?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    return $stmt->get_result();
}


// Default query to fetch all attractions if no search term is provided
$attractionsResult = null;

// Check if search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $attractionsResult = searchAttractions($conn, $search_term);
} else {
    // Default query to fetch all attractions
    $sql = "SELECT * FROM attractions";
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
            background-color: rgba(238,238,238,255);
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
        .close, .prev, .next {
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
        .close:hover, .prev:hover, .next:hover {
            color: #bbb;
        }
        .add-attraction-btn {
            display: block;
            margin: 20px auto;
            text-align: center;
        }
        .add-attraction-btn a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
        }
        .add-attraction-btn a:hover {
            background-color: #0056b3;
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

        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="?" method="GET">
    <div class="form-input">
        <input type="search" name="search" placeholder="Search by barangay, type, or name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
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
            <h1>Attractions</h1>
            <div style="text-align: left; margin-bottom: 20px;">
    <a href="attractionsform.php" class="btn btn-" style="text-decoration:none; background-color:#0056b3; color:#f8f9fa;">+ Attraction</a>
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
        // Select all images for the specified attractionID
        images = Array.from(document.querySelectorAll(`.image-container[data-attraction-id='${attractionID}'] .image-grid img`))
            .map(img => img.src);
        
        // Set the current image index to the clicked image
        currentImageIndex = images.indexOf(src);

        // Update and show the modal with the clicked image
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

    // Close modal if clicking outside of the image
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
