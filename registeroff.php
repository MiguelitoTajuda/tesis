<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Official</title>
</head>
<body>
    <h2>Register Official</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="position">Position:</label>
        <input type="text" id="position" name="position" required><br><br>
        
        <label for="barangay">Barangay:</label>
        <input type="text" id="barangay" name="barangay" required><br><br>
        
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required><br><br>
        
        <label for="contactnum">Contact Number:</label>
        <input type="text" id="contactnum" name="contactnum" required><br><br>
        
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*"><br><br>
        
        <label for="yearofterm">Year of Term:</label>
        <input type="text" id="yearofterm" name="yearofterm" required><br><br>
        
        <input type="submit" value="Register">
    </form>
</body>
</html>

<?php
// Function to establish a database connection
function connectDB() {
    $servername = "localhost";
    $username = "root"; // Change this to your database username
    $password = ""; // Change this to your database password
    $dbname = "jk"; // Change this to your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $name = htmlspecialchars($_POST["name"]);
    $position = htmlspecialchars($_POST["position"]);
    $barangay = htmlspecialchars($_POST["barangay"]);
    $age = htmlspecialchars($_POST["age"]);
    $contactnum = htmlspecialchars($_POST["contactnum"]);
    $yearofterm = htmlspecialchars($_POST["yearofterm"]);
    
    // Process the image upload
    $targetDir = "images/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

    // Establish database connection
    $conn = connectDB();

    // Insert data into the officials table
    $sql = "INSERT INTO officials (name, position, barangay, age, contactnum, image, yearofterm) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisss", $name, $position, $barangay, $age, $contactnum, $targetFile, $yearofterm);

    if ($stmt->execute()) {
        // Redirect to a success page or do any other necessary action
        header("Location: success.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
