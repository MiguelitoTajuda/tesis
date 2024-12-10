<?php
// Include config.php for database connection
include 'config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = ""; // Variable to hold the success message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $barangay = $_POST['barangay'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $map = $_POST['map'];

    // Insert attraction data into the database
    $sql = "INSERT INTO Attractions (Name, barangay, Type, Description, google_map) VALUES ('$name', '$barangay', '$type', '$description', '$map')";
    if ($conn->query($sql) === TRUE) {
        $attractionID = $conn->insert_id; // Get the last inserted ID

        // Handle file uploads
        if (isset($_FILES['pictures']) && $_FILES['pictures']['error'][0] != UPLOAD_ERR_NO_FILE) {
            $files = $_FILES['pictures'];
            $uploadDirectory = 'images/';

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $files['tmp_name'][$i];
                    $fileName = basename($files['name'][$i]);
                    $filePath = $uploadDirectory . uniqid() . '-' . $fileName;

                    // Validate file type (e.g., allow only images)
                    $fileType = mime_content_type($fileTmpPath);
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                    if (in_array($fileType, $allowedTypes)) {
                        if (move_uploaded_file($fileTmpPath, $filePath)) {
                            // Insert picture info into Pictures table
                            $sql = "INSERT INTO Pictures (AttractionID, PictureURL) VALUES ('$attractionID', '$filePath')";
                            $conn->query($sql);
                        } else {
                            echo "Error moving the file: " . $fileName . "<br>";
                        }
                    } else {
                        echo "File type not allowed: " . $fileName . "<br>";
                    }
                } else {
                    echo "Error uploading file: " . $files['name'][$i] . "<br>";
                }
            }
        }

        $successMessage = "Attraction added successfully!";
        header("Location: Attractionsform.php?success=1"); // Redirect to avoid resubmission
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Gala Spots</title>
</head>
<style>
body {
        font-family: Arial, sans-serif;
        background-color: gainsboro;
    }
        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h3 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
        }
        input[type="text"], input[type="date"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 10%;
        }
        button:disabled {
            background-color: #ddd;
        }
        .checkbox-group {
            margin-top: 20px;
        }
        .checkbox-group label {
            display: inline-block;
            margin-right: 10px;
        }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-danger {
        background-color: #f44336;
        border-color: #f44336;
    }

    .btn:hover {
        opacity: 0.8;
    }
</style>

<body>
<div class="container">
<div class="row align-items-center mb-4">
        <div class="col-md-12 d-flex justify-content-between">
            <h3 class="m-0 text-center flex-grow-1">Attractions Registration Form</h3>
            <img src="images/official.png" alt="Logo" class="logo" style="height: 80px;">
        </div>
    </div>
        <form action="Attractionsform.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="barangay">Location:</label>
                    <input type="text" class="form-control" id="barangay" name="barangay" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type">Type:</label>
                    <input type="text" class="form-control" id="type" name="type" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="map">Google Map:</label>
                    <input type="text" class="form-control" id="iframeSrc" readonly id="map" name="map" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="description">Description:</label>
                <textarea id="description" class="form-control" name="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="embedCode">Google Embed Map:</label>
                <textarea id="embedCode" onchange="extractIframeSrc()" class="form-control" rows="3" placeholder="Paste the iframe embed code here"></textarea>
            </div>
            <div class="mb-3">
                <label for="pictures">Upload Pictures:</label>
                <input type="file" class="form-control" id="pictures" name="pictures[]" multiple required>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 10px; margin-top: 20px;">
    <button type="submit" class="btn btn-primary" style="width: 20%; padding: 10px; font-size: 16px;">Submit</button>
    <button type="button" class="btn btn-danger" style="width: 20%; padding: 10px; font-size: 16px;" onclick="window.location.href='admingala.php'">Back</button>
</div>


        </form>
    </div>

    <script>
        function extractIframeSrc() {
            const embedCode = document.getElementById('embedCode').value;
            const srcMatch = embedCode.match(/<iframe[^>]*src=["']([^"']*)["']/);
            document.getElementById('iframeSrc').value = srcMatch ? srcMatch[1] : "Invalid iframe code!";
        }

        // Show success alert if redirected
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        Swal.fire({
            title: 'Success!',
            text: 'Attraction added successfully!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>
    </script>
</body>

</html>
