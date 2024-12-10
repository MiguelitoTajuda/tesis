<?php
// Include config.php for database connection
include 'config.php';

// Function to sanitize user input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

// Check if ID parameter is set and not empty
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the activity data for editing
    $sql = "SELECT * FROM activities WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Handle form submission
        if(isset($_POST['submit'])) {
            if($_POST['submit'] == "Update") {
                // Update activity
                $actname = sanitizeInput($_POST['actname']);
                $barangay = sanitizeInput($_POST['barangay']);
                $date = sanitizeInput($_POST['date']);
                $description = sanitizeInput($_POST['description']);
                $picture = sanitizeInput($_POST['picture']);

                $update_sql = "UPDATE activities SET actname=?, barangay=?, date=?, description=?, picture=? WHERE id=?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sssssi", $actname, $barangay, $date, $description, $picture, $id);
                if($update_stmt->execute()) {
                    // Redirect to activities.php after successful update
                    header("Location: activities.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Error updating activity: " . $conn->error . "</p>";
                }
            } elseif($_POST['submit'] == "Delete") {
                // Confirm deletion
                if(isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == "yes") {
                    // Delete activity
                    $delete_sql = "DELETE FROM activities WHERE id=?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $id);
                    if($delete_stmt->execute()) {
                        // Redirect to activities.php after successful deletion
                        header("Location: activities.php");
                        exit();
                    } else {
                        echo "<p style='color: red;'>Error deleting activity: " . $conn->error . "</p>";
                    }
                } else {
                    echo "<p style='color: red;'>Please confirm deletion.</p>";
                }
            }
        }
        ?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Edit Activity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: gainsboro;
        }
 

        textarea {
    width: 100%;
    height: 150px; /* Adjust the height as needed */
}

textarea#description {
    grid-column: span 2;
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


        .delete-btn {
            background-color: #f44336;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .button-container input[type="submit"] {
            flex: 1;
            margin-right: 5px;
        }

        .button-container form {
            flex: 1;
        }

        h2 {
            text-align: center;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
        .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
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
    .btn-danger {
        background-color: #f44336;
        border-color: #f44336;
    }

    .btn:hover {
        opacity: 0.8;
    }
    </style>
</head>
<body>
<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-12 d-flex justify-content-between">
            <h3 class="m-0 text-center flex-grow-1">Update Event Form</h3>
            <img src="images/official.png" alt="Logo" class="logo" style="position: absolute; top: 60px; right: 350px; height: 70px;">
        </div>
    </div>
    <?php
    // Include config.php for database connection
    include 'config.php';

    // Check if ID parameter is set and not empty
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch the activity data for editing
        $sql = "SELECT * FROM activities WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Handle form submission
            if(isset($_POST['submit'])) {
                if($_POST['submit'] == "Update") {
                    // Update activity
                    $actname = $_POST['actname'];
                    $barangay = $_POST['barangay'];
                    $date = $_POST['date'];
                    $description = $_POST['description'];
                    $picture = $_POST['picture'];

                    $update_sql = "UPDATE activities SET actname=?, barangay=?, date=?, description=?, picture=? WHERE id=?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("sssssi", $actname, $barangay, $date, $description, $picture, $id);
                    if($update_stmt->execute()) {
                        // Redirect to activities.php after successful update
                        header("Location: activities.php");
                        exit();
                    } else {
                        echo "<p style='color: red;'>Error updating activity: " . $conn->error . "</p>";
                    }
                } elseif($_POST['submit'] == "Back") {
                    // Delete activity
                     
                        // Redirect to activities.php after successful deletion
                        header("Location: activities.php");
                        exit();
                    
                }
            }
            ?>
      <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <div class="form-grid">
            <div>
                <label for="actname">Activity Name:</label>
                <input type="text" id="actname" name="actname" value="<?php echo $row['actname']; ?>">
            </div>
            <div>
                <label for="barangay">Barangay:</label>
                <input type="text" id="barangay" name="barangay" value="<?php echo $row['barangay']; ?>">
            </div>
            <div>
                <label for="date">Date:</label>
                <input type="text" id="date" name="date" value="<?php echo $row['date']; ?>">
            </div>
            <div>
                <label for="picture">Picture:</label>
                <input type="text" id="picture" name="picture" value="<?php echo $row['picture']; ?>">
            </div>
        </div>

        <div style="grid-column: span 2;">
    <label for="description">Description:</label>
    <textarea id="description" name="description" style="width: 100%;"><?php echo $row['description']; ?></textarea>
</div>


<div class="d-flex justify-content-between mt-4">
                    <button type="submit" name="submit" value="Update" class="btn btn-primary">Update</button>
                    <button type="submit" name="submit" value="Back" class="btn btn-danger">Back</button>
                </div>



    </form>
            <?php
        } else {
            // No activity found with the given ID
            echo "<p style='color: red;'>Activity not found.</p>";
        }
    } else {
        // ID parameter is not set or empty
        echo "<p style='color: red;'>Invalid request.</p>";
    }
    ?>
</div>

</body>
</html>


<?php
    } else {
        // No activity found with the given ID
        echo "Activity not found.";
    }
} else {
    // ID parameter is not set or empty
    echo "Invalid request.";
}
?>
