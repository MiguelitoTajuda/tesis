<?php
// Include config.php for database connection
include 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $actname = $_POST["actname"];
    $barangay = $_POST["barangay"];
    $date = $_POST["date"];
    $description = $_POST["description"]; // New description field
    
    // Handle picture upload
    $picture_name = $_FILES["picture"]["name"];
    $picture_tmp = $_FILES["picture"]["tmp_name"];
    $picture_type = $_FILES["picture"]["type"];
    $picture_size = $_FILES["picture"]["size"];
    $picture_error = $_FILES["picture"]["error"];

    // Check if file was uploaded without errors
    if ($picture_error === 0) {
        // Move uploaded file to desired directory
        $target_directory = "images/"; // Your desired directory
        $target_file = $target_directory . basename($picture_name);
        
        // Move the uploaded file
        if (move_uploaded_file($picture_tmp, $target_file)) {
            // Prepare SQL statement to insert data into the activities table
            $sql = "INSERT INTO activities (actname, barangay, date, description, picture) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $actname, $barangay, $date, $description, $target_file);
            
            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to activities.php after successful registration
                header("Location: activities.php");
                exit(); // Ensure script execution stops here
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            // Close statement
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Error uploading file. Error code: $picture_error";
    }
}

// Close connection (handled in config.php)
?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Register Activity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: gainsboro;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"],
        input[type="date"],
        input[type="file"],
        input[type="submit"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 15px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        label {
            font-weight: bold;
            font-size: 15px;
        }

        /* Style the close button (X icon) */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 30px; /* Larger size for the icon */
            color: #333;
            text-decoration: none;
        }

        .close-btn i {
            font-size: 40px; /* Make the icon bigger */
        }

        .close-btn:hover {
            color: black; /* Red color on hover */
        }
    </style>
</head>
<body>

<a href="activities.php" class="close-btn">
            <i class="fas fa-times"></i> <!-- Close icon -->
        </a>


<div class="container">
<h2><img src="images/official.png" alt="Logo" style="height: 50px; vertical-align: middle;"> Register Activity</h2>

    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="actname">Activity Name:</label>
        <input type="text" id="actname" name="actname">
        
        <label for="barangay">Barangay:</label>
        <input type="text" id="barangay" name="barangay">
        
        <label for="date">Date:</label>
        <input type="date" id="date" name="date">
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5"></textarea>
        
        <label for="picture">Picture:</label>
        <input type="file" id="picture" name="picture">
        
        <input type="submit" value="Register">
    </form>
</div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

</body>
</html>
