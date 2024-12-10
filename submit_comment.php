<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $activity_id = $_POST["activity_id"];
    $comment = $_POST["comment"];

    include 'config.php';
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert comment into database
    $sql = "INSERT INTO comments (activity_id, comment) VALUES ($activity_id, '$comment')";

    if ($conn->query($sql) === TRUE) {
        // Comment inserted successfully
        header("Location: ".$_SERVER['HTTP_REFERER']); // Redirect back to the previous page
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
